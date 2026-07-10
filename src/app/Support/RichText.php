<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

class RichText
{
    public static function sanitize(string $content): string
    {
        $content = preg_replace('/<\s*(script|style|iframe|object|embed)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $content) ?? '';

        $content = strip_tags($content, [
            'a',
            'b',
            'blockquote',
            'br',
            'em',
            'h2',
            'h3',
            'h4',
            'i',
            'li',
            'ol',
            'p',
            's',
            'strong',
            'u',
            'ul',
        ]);

        $content = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $content) ?? '';
        $content = preg_replace('/\s+style\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $content) ?? '';
        $content = preg_replace('/href\s*=\s*([\'"])\s*javascript:[^\'"]*\1/i', 'href="#"', $content) ?? '';

        return trim($content);
    }

    public static function sanitizeWalkthrough(string $content): string
    {
        if (trim($content) === '') {
            return '';
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previousErrors = libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="UTF-8"><div id="walkthrough-content">' . $content . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        $root = $dom->getElementById('walkthrough-content');

        if (! $root) {
            return '';
        }

        self::cleanWalkthroughNode($root, $dom);
        self::wrapLooseText($root, $dom);
        self::removeEmptyBlocks($root);

        $html = '';

        foreach ($root->childNodes as $child) {
            $html .= $dom->saveHTML($child);
        }

        return trim($html);
    }

    private static function cleanWalkthroughNode(DOMNode $node, DOMDocument $dom): void
    {
        $allowedTags = [
            'blockquote', 'br', 'em', 'figure', 'h2', 'h3', 'h4',
            'i', 'img', 'li', 'mark', 'ol', 'p', 's', 'strong', 'u', 'ul',
        ];

        foreach (iterator_to_array($node->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, ['button', 'embed', 'figcaption', 'form', 'iframe', 'input', 'object', 'script', 'style'], true)) {
                $child->parentNode?->removeChild($child);

                continue;
            }

            if (in_array($tag, ['h1', 'h5', 'h6'], true)) {
                $replacement = $dom->createElement($tag === 'h1' ? 'h2' : 'h3');

                while ($child->firstChild) {
                    $replacement->appendChild($child->firstChild);
                }

                $child->parentNode?->replaceChild($replacement, $child);
                $child = $replacement;
                $tag = strtolower($child->tagName);
            }

            if ($tag === 'a') {
                $highlight = $dom->createElement('mark');

                while ($child->firstChild) {
                    $highlight->appendChild($child->firstChild);
                }

                $child->parentNode?->replaceChild($highlight, $child);
                self::cleanWalkthroughNode($highlight, $dom);

                continue;
            }

            if (in_array($tag, ['h2', 'h3', 'h4'], true) && mb_strlen(trim($child->textContent)) > 90) {
                $paragraph = $dom->createElement('p');

                while ($child->firstChild) {
                    $paragraph->appendChild($child->firstChild);
                }

                $child->parentNode?->replaceChild($paragraph, $child);
                $child = $paragraph;
                $tag = 'p';
            }

            if (in_array($tag, ['b', 'strong'], true) && mb_strlen(trim($child->textContent)) > 90) {
                self::cleanWalkthroughNode($child, $dom);

                while ($child->firstChild) {
                    $child->parentNode?->insertBefore($child->firstChild, $child);
                }

                $child->parentNode?->removeChild($child);

                continue;
            }

            if (! in_array($tag, $allowedTags, true)) {
                self::cleanWalkthroughNode($child, $dom);

                while ($child->firstChild) {
                    $child->parentNode?->insertBefore($child->firstChild, $child);
                }

                $child->parentNode?->removeChild($child);

                continue;
            }

            if ($tag === 'figure') {
                $child->setAttribute('data-walkthrough-media', 'true');
            }

            if ($tag === 'img') {
                $imageSource = self::resolveImageSource($child);

                if (filled($imageSource)) {
                    $child->setAttribute('src', $imageSource);
                }
            }

            if ($tag === 'img' && str_starts_with($child->getAttribute('src'), '//')) {
                $child->setAttribute('src', 'https:' . $child->getAttribute('src'));
            }

            foreach (iterator_to_array($child->attributes) as $attribute) {
                if ($tag === 'img' && in_array($attribute->name, ['src', 'alt', 'title', 'loading', 'decoding'], true)) {
                    continue;
                }

                $child->removeAttribute($attribute->name);
            }

            if ($tag === 'img' && ! self::isSafeImageSource($child->getAttribute('src'))) {
                $child->parentNode?->removeChild($child);

                continue;
            }

            if ($tag === 'img') {
                $child->setAttribute('loading', 'lazy');
                $child->setAttribute('decoding', 'async');
            }

            self::cleanWalkthroughNode($child, $dom);
        }
    }

    private static function wrapLooseText(DOMNode $node, DOMDocument $dom): void
    {
        $blockTags = ['blockquote', 'figure', 'h2', 'h3', 'h4', 'li', 'ol', 'p', 'ul'];

        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMText && trim($child->textContent) !== '') {
                $paragraph = $dom->createElement('p');
                $paragraph->appendChild($dom->createTextNode(trim($child->textContent)));
                $child->parentNode?->replaceChild($paragraph, $child);

                continue;
            }

            if (! $child instanceof DOMElement) {
                continue;
            }

            if (! in_array(strtolower($child->tagName), $blockTags, true)) {
                self::wrapLooseText($child, $dom);
            }
        }
    }

    private static function removeEmptyBlocks(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }

            self::removeEmptyBlocks($child);

            $tag = strtolower($child->tagName);

            if (in_array($tag, ['p', 'h2', 'h3', 'h4', 'li', 'mark', 'strong', 'em'], true)
                && trim($child->textContent) === ''
                && $child->getElementsByTagName('img')->length === 0) {
                $child->parentNode?->removeChild($child);
            }
        }
    }

    private static function isSafeImageSource(string $source): bool
    {
        return str_starts_with($source, '/')
            || str_starts_with($source, 'https://')
            || str_starts_with($source, 'http://');
    }

    private static function resolveImageSource(DOMElement $image): ?string
    {
        $attributes = [
            'src',
            'data-src',
            'data-lazy-src',
            'data-original',
            'data-fullurl',
            'data-file-url',
            'data-image',
            'data-image-src',
            'data-thumb',
        ];

        foreach ($attributes as $attribute) {
            $source = trim($image->getAttribute($attribute));

            if ($source !== '' && ! self::isPlaceholderImage($source)) {
                return self::normalizeImageSource($source);
            }
        }

        foreach (['srcset', 'data-srcset'] as $attribute) {
            $source = self::firstSrcsetCandidate($image->getAttribute($attribute));

            if ($source !== null) {
                return self::normalizeImageSource($source);
            }
        }

        return null;
    }

    private static function firstSrcsetCandidate(string $srcset): ?string
    {
        foreach (explode(',', $srcset) as $candidate) {
            $source = trim(strtok(trim($candidate), " \t\n\r\0\x0B") ?: '');

            if ($source !== '' && ! self::isPlaceholderImage($source)) {
                return $source;
            }
        }

        return null;
    }

    private static function normalizeImageSource(string $source): string
    {
        if (str_starts_with($source, '//')) {
            return 'https:' . $source;
        }

        return $source;
    }

    private static function isPlaceholderImage(string $source): bool
    {
        $source = strtolower(trim($source));

        return $source === ''
            || $source === '#'
            || $source === 'about:blank'
            || str_starts_with($source, 'data:image/')
            || str_contains($source, 'blank.gif')
            || str_contains($source, 'placeholder');
    }
}
