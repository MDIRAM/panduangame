<x-filament-panels::page
    @class([
        'fi-resource-edit-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>
    @php
        $chapterTree = $this->getChapterTree();
        $relationManagers = $this->getRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
    @endphp

    @capture($form)
        <x-filament-panels::form
            id="form"
            :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            wire:submit="save"
        >
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
    @endcapture

    <div class="walkthrough-editor-layout">
        <aside class="walkthrough-editor-sidebar" aria-label="{{ $record->game->title }} walkthrough chapters">
            <div class="walkthrough-editor-sidebar-header">
                <p class="walkthrough-editor-sidebar-title">{{ $record->game->title }}</p>
                <p class="walkthrough-editor-sidebar-copy">
                    {{ $record->game->chapters()->count() }} chapters · pilih chapter untuk mengedit isinya
                </p>
            </div>

            <nav class="walkthrough-editor-tree">
                @foreach ($chapterTree as $parentChapter)
                    @php
                        $hasChildren = $parentChapter->children->isNotEmpty();
                        $isCurrentGroup = $record->is($parentChapter)
                            || $parentChapter->children->contains(fn ($child) => $record->is($child));
                    @endphp

                    @if ($hasChildren)
                        <details class="walkthrough-editor-group" @if ($isCurrentGroup) open @endif>
                            <summary>
                                <svg class="walkthrough-editor-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 0 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.08 0Z" clip-rule="evenodd" />
                                </svg>
                                <a
                                    href="{{ \App\Filament\Admin\Resources\ChapterResource::getUrl('edit', ['record' => $parentChapter]) }}"
                                    class="walkthrough-editor-link {{ $record->is($parentChapter) ? 'is-active' : '' }}"
                                    wire:navigate
                                >
                                    {{ $parentChapter->chapter_title }}
                                </a>
                            </summary>

                            <div class="walkthrough-editor-children">
                                @foreach ($parentChapter->children as $childChapter)
                                    <a
                                        href="{{ \App\Filament\Admin\Resources\ChapterResource::getUrl('edit', ['record' => $childChapter]) }}"
                                        class="walkthrough-editor-link {{ $record->is($childChapter) ? 'is-active' : '' }}"
                                        wire:navigate
                                    >
                                        {{ $childChapter->chapter_title }}
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @else
                        <div class="walkthrough-editor-group">
                            <a
                                href="{{ \App\Filament\Admin\Resources\ChapterResource::getUrl('edit', ['record' => $parentChapter]) }}"
                                class="walkthrough-editor-link {{ $record->is($parentChapter) ? 'is-active' : '' }}"
                                wire:navigate
                            >
                                {{ $parentChapter->chapter_title }}
                            </a>
                        </div>
                    @endif
                @endforeach
            </nav>
        </aside>

        <div class="walkthrough-editor-content">
            @if ((! $hasCombinedRelationManagerTabsWithContent) || (! count($relationManagers)))
                {{ $form() }}
            @endif

            @if (count($relationManagers))
                <x-filament-panels::resources.relation-managers
                    :active-locale="isset($activeLocale) ? $activeLocale : null"
                    :active-manager="$this->activeRelationManager ?? ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))"
                    :content-tab-label="$this->getContentTabLabel()"
                    :content-tab-icon="$this->getContentTabIcon()"
                    :content-tab-position="$this->getContentTabPosition()"
                    :managers="$relationManagers"
                    :owner-record="$record"
                    :page-class="static::class"
                >
                    @if ($hasCombinedRelationManagerTabsWithContent)
                        <x-slot name="content">
                            {{ $form() }}
                        </x-slot>
                    @endif
                </x-filament-panels::resources.relation-managers>
            @endif
        </div>
    </div>

    <x-filament-panels::page.unsaved-data-changes-alert />

    <script data-navigate-once>
        if (!window.walkthroughPasteNormalizerInstalled) {
            window.walkthroughPasteNormalizerInstalled = true;

            const isWalkthroughEditor = (target) => {
                return target?.closest?.('.walkthrough-document-editor')
                    || target?.closest?.('.fi-fo-rich-editor')?.querySelector?.('.walkthrough-document-editor');
            };

            const firstSrcsetCandidate = (srcset) => {
                return (srcset || '')
                    .split(',')
                    .map((candidate) => candidate.trim().split(/\s+/)[0])
                    .find(Boolean) || '';
            };

            const normalizeImageSource = (image) => {
                const attributes = [
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

                for (const attribute of attributes) {
                    const value = image.getAttribute(attribute);

                    if (value && !value.startsWith('data:image/') && value !== '#') {
                        return value.startsWith('//') ? `https:${value}` : value;
                    }
                }

                const srcsetSource = firstSrcsetCandidate(image.getAttribute('srcset'))
                    || firstSrcsetCandidate(image.getAttribute('data-srcset'));

                if (srcsetSource && !srcsetSource.startsWith('data:image/')) {
                    return srcsetSource.startsWith('//') ? `https:${srcsetSource}` : srcsetSource;
                }

                return '';
            };

            const normalizeWalkthroughHtml = (html) => {
                const template = document.createElement('template');
                template.innerHTML = html || '';
                const root = template.content;

                root.querySelectorAll('script, style, iframe, object, embed, form, input, button, figcaption')
                    .forEach((element) => element.remove());

                root.querySelectorAll('a').forEach((link) => {
                    const highlight = document.createElement('mark');
                    highlight.append(...link.childNodes);
                    link.replaceWith(highlight);
                });

                root.querySelectorAll('h1, h2, h3, h4, h5, h6').forEach((heading) => {
                    const text = heading.textContent.trim();
                    const targetTag = text.length > 90
                        ? 'p'
                        : (['H3', 'H4', 'H5', 'H6'].includes(heading.tagName) ? 'h3' : 'h2');

                    if (heading.tagName.toLowerCase() !== targetTag) {
                        const replacement = document.createElement(targetTag);
                        replacement.append(...heading.childNodes);
                        heading.replaceWith(replacement);
                    }
                });

                root.querySelectorAll('strong, b').forEach((strong) => {
                    if (strong.textContent.trim().length <= 90) {
                        return;
                    }

                    strong.replaceWith(...strong.childNodes);
                });

                root.querySelectorAll('*').forEach((element) => {
                    [...element.attributes].forEach((attribute) => {
                        const keepImageAttribute = element.tagName === 'IMG'
                            && ['src', 'alt', 'title', 'loading', 'decoding', 'srcset', 'data-src', 'data-srcset'].includes(attribute.name);

                        if (!keepImageAttribute) {
                            element.removeAttribute(attribute.name);
                        }
                    });

                    if (element.tagName === 'IMG') {
                        const source = normalizeImageSource(element);

                        if (source) {
                            element.setAttribute('src', source);
                            element.setAttribute('loading', 'lazy');
                            element.setAttribute('decoding', 'async');
                        } else {
                            element.remove();
                        }

                        element.removeAttribute('srcset');
                        element.removeAttribute('data-src');
                        element.removeAttribute('data-srcset');
                    }
                });

                const wrapper = document.createElement('div');
                wrapper.appendChild(root.cloneNode(true));

                [...wrapper.childNodes].forEach((node) => {
                    if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
                        const paragraph = document.createElement('p');
                        paragraph.textContent = node.textContent.trim();
                        node.replaceWith(paragraph);
                    }
                });

                return wrapper.innerHTML;
            };

            const normalizeExistingEditor = (editor) => {
                if (!editor || editor.dataset.walkthroughNormalizing === 'true') {
                    return;
                }

                const currentHtml = editor.innerHTML;
                const normalizedHtml = normalizeWalkthroughHtml(currentHtml);

                if (!normalizedHtml || normalizedHtml === currentHtml) {
                    return;
                }

                editor.dataset.walkthroughNormalizing = 'true';

                if (editor.editor?.loadHTML) {
                    const previousScrollTop = editor.scrollTop;
                    editor.editor.loadHTML(normalizedHtml);
                    editor.scrollTop = previousScrollTop;
                } else {
                    editor.innerHTML = normalizedHtml;
                    editor.dispatchEvent(new Event('input', { bubbles: true }));
                }

                window.setTimeout(() => {
                    editor.dataset.walkthroughNormalizing = 'false';
                }, 80);
            };

            const scheduleEditorNormalize = (editor) => {
                if (!editor || !isWalkthroughEditor(editor)) {
                    return;
                }

                window.clearTimeout(editor.walkthroughNormalizeTimeout);
                editor.walkthroughNormalizeTimeout = window.setTimeout(() => {
                    normalizeExistingEditor(editor);
                }, 180);
            };

            const installEditorTools = () => {
                document.querySelectorAll('trix-editor.walkthrough-document-editor').forEach((editor) => {
                    if (editor.dataset.walkthroughToolsInstalled === 'true') {
                        return;
                    }

                    editor.dataset.walkthroughToolsInstalled = 'true';
                    const toolbar = document.getElementById(editor.getAttribute('toolbar'));

                    if (!toolbar || toolbar.querySelector('[data-walkthrough-clean-paste]')) {
                        return;
                    }

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.dataset.walkthroughCleanPaste = 'true';
                    button.textContent = 'Rapikan Paste';
                    button.className = 'walkthrough-clean-paste-button';
                    button.addEventListener('click', () => normalizeExistingEditor(editor));

                    toolbar.appendChild(button);
                });
            };

            document.addEventListener('trix-before-paste', (event) => {
                const paste = event.paste;

                if (!paste?.html || !isWalkthroughEditor(event.target)) {
                    return;
                }

                paste.html = normalizeWalkthroughHtml(paste.html);
            });

            document.addEventListener('trix-paste', (event) => {
                if (!isWalkthroughEditor(event.target)) {
                    return;
                }

                scheduleEditorNormalize(event.target);
            });

            document.addEventListener('paste', (event) => {
                if (!isWalkthroughEditor(event.target) || event.defaultPrevented) {
                    return;
                }

                const clipboard = event.clipboardData;
                const html = clipboard?.getData('text/html');
                const text = clipboard?.getData('text/plain');

                if (!html && !text) {
                    return;
                }

                const normalized = normalizeWalkthroughHtml(
                    html || text
                        .split(/\n{2,}/)
                        .map((paragraph) => `<p>${paragraph.trim()}</p>`)
                        .join(''),
                );

                event.preventDefault();

                if (event.target.tagName === 'TRIX-EDITOR' && event.target.editor?.insertHTML) {
                    event.target.editor.insertHTML(normalized);
                    scheduleEditorNormalize(event.target);

                    return;
                }

                document.execCommand('insertHTML', false, normalized);
                scheduleEditorNormalize(event.target.closest('trix-editor'));
            }, true);

            document.addEventListener('error', (event) => {
                if (event.target?.tagName !== 'IMG' || !isWalkthroughEditor(event.target)) {
                    return;
                }

                const editor = event.target.closest('trix-editor');
                event.target.remove();
                editor?.dispatchEvent(new Event('input', { bubbles: true }));
            }, true);

            document.addEventListener('trix-change', (event) => {
                if (!isWalkthroughEditor(event.target)) {
                    return;
                }

                event.target.querySelectorAll('img').forEach((image) => {
                    image.setAttribute('loading', 'lazy');
                    image.setAttribute('decoding', 'async');

                    if (!image.getAttribute('src') || image.naturalWidth === 0 && image.complete) {
                        image.remove();
                    }
                });
            });

            document.addEventListener('trix-initialize', (event) => {
                if (!isWalkthroughEditor(event.target)) {
                    return;
                }

                installEditorTools();
            });

            document.addEventListener('livewire:navigated', installEditorTools);
            document.addEventListener('DOMContentLoaded', installEditorTools);
            installEditorTools();
        }
    </script>
</x-filament-panels::page>
