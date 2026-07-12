<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080d18">
    <title>{{ $chapter->chapter_title }} | {{ $chapter->game->title }}</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @php
        $theme = $chapter->game->theme;
    @endphp
    <style>
        :root {
            --guide-accent: {{ $theme['accent'] }};
            --guide-accent-soft: {{ $theme['accent_soft'] }};
            --guide-bg: {{ $theme['background'] }};
            --guide-glow: {{ $theme['background_glow'] }};
            --guide-border: {{ $theme['border'] }};
        }

        * {
            box-sizing: border-box;
        }

        html {
            height: 100%;
            background: var(--guide-bg);
            color-scheme: dark;
        }

        body {
            height: 100%;
            margin: 0;
            overflow: hidden;
            background:
                linear-gradient(180deg, var(--guide-glow), transparent 380px),
                var(--guide-bg);
            color: #e8edf5;
            font-family: "Instrument Sans", Arial, sans-serif;
        }

        a {
            color: inherit;
        }

        .walkthrough-shell {
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            height: 100dvh;
            overflow: hidden;
        }

        .chapter-sidebar {
            display: grid;
            grid-template-rows: auto minmax(0, 1fr);
            min-width: 0;
            height: 100dvh;
            overflow: hidden;
            border-right: 1px solid var(--guide-border);
            background: rgba(10, 17, 31, 0.96);
        }

        .sidebar-header {
            padding: 22px 20px 18px;
            border-bottom: 1px solid var(--guide-border);
        }

        .sidebar-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--guide-accent);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .sidebar-header h2 {
            margin: 16px 0 0;
            color: #ffffff;
            font-size: 19px;
            line-height: 1.3;
        }

        .sidebar-header p {
            margin: 6px 0 0;
            color: #8191aa;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .chapter-list {
            min-height: 0;
            height: 100%;
            padding: 14px 12px 28px;
            overflow-x: hidden;
            overflow-y: scroll;
            scrollbar-color: #526078 transparent;
            scrollbar-width: thin;
            touch-action: pan-y;
        }

        .chapter-list:focus-visible {
            outline: 2px solid var(--guide-accent);
            outline-offset: -2px;
        }

        .chapter-group + .chapter-group {
            margin-top: 20px;
        }

        .chapter-group-title {
            margin: 0 8px 8px;
            color: #8191aa;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .chapter-link {
            display: block;
            margin-bottom: 4px;
            padding: 10px 12px;
            border-left: 3px solid transparent;
            border-radius: 4px;
            color: #bdc8d8;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
            text-decoration: none;
            transition: background 160ms ease, border-color 160ms ease, color 160ms ease;
        }

        .chapter-link:hover {
            background: #172235;
            color: #ffffff;
        }

        .chapter-link.active {
            border-left-color: var(--guide-accent);
            background: var(--guide-accent-soft);
            color: #ffffff;
        }

        .chapter-link.has-active-child {
            color: #ffffff;
        }

        .chapter-child-list {
            margin: 0 0 8px 10px;
            padding-left: 8px;
            border-left: 1px solid #2b3a50;
        }

        .chapter-link.child {
            padding: 8px 10px;
            color: #c8d2df;
            font-size: 13px;
            font-weight: 500;
        }

        .chapter-link.child::before {
            content: "— ";
            color: #6f7f96;
        }

        .guide-scroll {
            min-width: 0;
            height: 100dvh;
            overflow-y: auto;
            scrollbar-color: #526078 transparent;
            scrollbar-width: thin;
        }

        .guide {
            width: min(100% - 32px, 980px);
            margin: 0 auto;
            padding: 34px 0 72px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            min-height: 40px;
            padding: 0 15px;
            border: 1px solid color-mix(in srgb, var(--guide-border) 80%, #64748b);
            border-radius: 6px;
            background: #172235;
            color: #dbeafe;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: background 160ms ease, border-color 160ms ease;
        }

        .back-link:hover {
            border-color: var(--guide-accent);
            background: var(--guide-accent-soft);
        }

        .guide-header {
            margin-top: 30px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--guide-border);
        }

        .game-name {
            margin: 0;
            color: var(--guide-accent);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        h1 {
            max-width: 850px;
            margin: 10px 0 0;
            color: #ffffff;
            font-size: clamp(30px, 5vw, 46px);
            line-height: 1.12;
        }

        .guide-byline {
            margin: 12px 0 0;
            color: #9fb0c8;
            font-size: 14px;
            font-weight: 600;
        }

        .guide-updated {
            margin-left: 8px;
            color: #6f819d;
            font-weight: 500;
        }

        .guide-source {
            display: inline-flex;
            margin-left: 8px;
            color: var(--guide-accent);
            font-weight: 700;
            text-decoration: none;
        }

        .guide-source:hover {
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .engagement-status {
            margin: 20px 0 0;
            padding: 11px 14px;
            border: 1px solid #256d4b;
            border-radius: 6px;
            background: #10271f;
            color: #b7f7d4;
            font-size: 14px;
            font-weight: 600;
        }

        .game-engagement {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            margin-top: 20px;
            padding: 18px;
            border: 1px solid color-mix(in srgb, var(--guide-border) 80%, #64748b);
            border-radius: 8px;
            background: #111a2a;
        }

        .rating-display {
            display: inline-flex;
            align-items: center;
            gap: 9px;
        }

        .rating-stars {
            position: relative;
            display: inline-block;
            color: #475569;
            font-size: 21px;
            line-height: 1;
        }

        .rating-stars-track,
        .rating-stars-fill {
            display: block;
            white-space: nowrap;
        }

        .rating-stars-fill {
            position: absolute;
            inset: 0 auto 0 0;
            overflow: hidden;
            color: #facc15;
        }

        .rating-value {
            color: #9fb0c8;
            font-size: 13px;
            font-weight: 700;
        }

        .engagement-actions,
        .rating-form {
            display: flex;
            align-items: end;
            gap: 9px;
        }

        .rating-input {
            display: grid;
            gap: 4px;
            margin: 0;
            padding: 0;
            border: 0;
        }

        .rating-input legend {
            padding: 0;
            color: #9fb0c8;
            font-size: 12px;
            font-weight: 700;
        }

        .engagement-button,
        .engagement-login {
            min-height: 40px;
            border: 1px solid #526078;
            border-radius: 6px;
            background: #172235;
            color: #ffffff;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
        }

        .rating-options {
            display: inline-flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            min-height: 40px;
            align-items: center;
        }

        .rating-options input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
        }

        .rating-options label {
            color: #526078;
            cursor: pointer;
            font-size: 28px;
            line-height: 1;
            transition: color 120ms ease, transform 120ms ease;
        }

        .rating-options label:hover,
        .rating-options label:hover ~ label,
        .rating-options input:checked ~ label {
            color: #facc15;
        }

        .rating-options label:hover {
            transform: translateY(-1px);
        }

        .rating-options input:focus-visible + label {
            outline: 2px solid var(--guide-accent);
            outline-offset: 3px;
        }

        .engagement-button,
        .engagement-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 14px;
            cursor: pointer;
            text-decoration: none;
        }

        .engagement-button.primary {
            border-color: var(--guide-accent);
            background: var(--guide-accent);
            color: #07111f;
        }

        .engagement-button.danger {
            border-color: #713c49;
            color: #fda4af;
        }

        .field-error {
            margin: 8px 0 0;
            color: #fda4af;
            font-size: 13px;
        }

        .steps {
            margin-top: 12px;
        }

        .step {
            padding: 30px 0 34px;
            border-bottom: 1px solid var(--guide-border);
        }

        .step h2 {
            margin: 0 0 12px;
            color: #f8fafc;
            font-size: 21px;
            line-height: 1.35;
        }

        .step-content {
            color: #cbd5e1;
            font-size: 16px;
            line-height: 1.7;
            font-weight: 400;
            overflow-wrap: anywhere;
        }

        .step-content :where(p, li, blockquote, strong, em, mark, a, span) {
            font-size: 16px;
            line-height: 1.7;
        }

        .step-content p {
            margin: 0 0 16px;
            font-weight: 400;
        }

        .step-content p:last-child {
            margin-bottom: 0;
        }

        .step-content ul,
        .step-content ol {
            margin: 14px 0;
            padding-left: 28px;
        }

        .step-content li {
            margin: 6px 0;
        }

        .step-content strong {
            color: #f8fafc;
            font-weight: 700;
        }

        .step-content em {
            font-style: italic;
        }

        .step-content a {
            color: var(--guide-accent);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .step-content mark {
            padding: 0;
            background: transparent;
            color: var(--guide-accent);
            font-weight: 700;
            text-decoration: none;
        }

        .step-content figure {
            margin: 24px 0;
        }

        .step-content figcaption {
            display: none;
        }

        .step-content h2,
        .step-content h3 {
            margin: 34px 0 12px;
            padding-top: 26px;
            border-top: 1px solid var(--guide-border);
            color: #f8fafc;
            line-height: 1.35;
        }

        .step-content h2:first-child,
        .step-content h3:first-child {
            margin-top: 0;
            padding-top: 0;
            border-top: 0;
        }

        .step-content h2 {
            font-size: 21px;
        }

        .step-content h3 {
            font-size: 18px;
        }

        .step-content blockquote {
            margin: 20px 0;
            padding: 12px 16px;
            border-left: 3px solid var(--guide-accent);
            background: #111a2a;
            color: #dbeafe;
        }

        .step-content img {
            margin: 22px auto;
        }

        .step img {
            display: block;
            width: min(100%, 780px);
            min-height: auto;
            max-height: 600px;
            height: auto;
            margin-top: 22px;
            border: 1px solid #334155;
            border-radius: 6px;
            background: #111827;
            object-fit: contain;
            cursor: zoom-in;
        }

        .step-content figure.is-broken,
        .step-content img.is-broken,
        .step > img.is-broken,
        .chapter-overview-media img.is-broken {
            display: none;
        }

        .scroll-actions {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 50;
            display: grid;
            gap: 10px;
        }

        .back-to-top,
        .scroll-to-bottom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            border: 1px solid color-mix(in srgb, var(--guide-accent) 70%, #ffffff);
            border-radius: 999px;
            background: color-mix(in srgb, var(--guide-accent) 88%, #ffffff);
            color: #07111f;
            cursor: pointer;
            font-size: 22px;
            font-weight: 900;
            opacity: 0;
            pointer-events: none;
            transform: translateY(10px);
            transition: opacity 160ms ease, transform 160ms ease, background 160ms ease;
        }

        .back-to-top.is-visible,
        .scroll-to-bottom.is-visible {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .back-to-top:hover,
        .scroll-to-bottom:hover {
            background: #ffffff;
        }

        .image-lightbox {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(2, 6, 23, 0.92);
            backdrop-filter: blur(10px);
        }

        .image-lightbox.is-open {
            display: flex;
        }

        .image-lightbox img {
            display: block;
            width: auto;
            max-width: min(96vw, 1320px);
            max-height: 90vh;
            border: 1px solid color-mix(in srgb, var(--guide-accent) 55%, #ffffff);
            border-radius: 8px;
            background: #020617;
            object-fit: contain;
            box-shadow: 0 28px 90px rgba(0, 0, 0, 0.55);
        }

        .image-lightbox-close {
            position: fixed;
            top: 18px;
            right: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.78);
            color: #ffffff;
            cursor: pointer;
            font-size: 28px;
            line-height: 1;
        }

        .image-lightbox-close:hover {
            background: rgba(30, 41, 59, 0.95);
        }

        body.theme-gold .steps {
            margin-top: 6px;
        }

        body.theme-gold .chapter-sidebar {
            border-color: var(--guide-border);
            background: rgba(13, 14, 14, 0.97);
        }

        body.theme-gold .sidebar-header {
            border-color: var(--guide-border);
        }

        body.theme-gold .guide-header,
        body.theme-gold .chapter-overview,
        body.theme-gold .step {
            border-color: var(--guide-border);
        }

        body.theme-gold .chapter-overview-media h2 {
            color: var(--guide-accent);
            font-family: Georgia, "Times New Roman", serif;
        }

        body.theme-gold .step h2 {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 24px;
        }

        body.theme-gold .step .step-content h2 {
            font-size: 21px;
        }

        body.theme-gold .step .step-content h3 {
            font-family: "Instrument Sans", Arial, sans-serif;
            font-size: 18px;
        }

        body.theme-gold .step img {
            width: min(100%, 700px);
            margin-right: auto;
            margin-left: auto;
            border-color: #514b3b;
        }

        body.theme-gold .guide-header {
            border-bottom-color: var(--guide-border);
        }

        .chapter-overview {
            display: grid;
            grid-template-columns: minmax(220px, 34%) minmax(0, 1fr);
            gap: 28px;
            margin-top: 30px;
            padding: 26px 0 32px;
            border-bottom: 1px solid var(--guide-border);
        }

        .chapter-overview-media h2 {
            margin: 0 0 16px;
            color: var(--guide-accent);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 19px;
            font-weight: 400;
        }

        .chapter-overview-media img {
            display: block;
            width: 100%;
            aspect-ratio: 16 / 9;
            border: 1px solid #4a463a;
            border-radius: 2px;
            object-fit: cover;
        }

        .chapter-overview-copy p {
            margin: 0 0 13px;
            color: #d2d0cb;
            font-size: 16px;
            line-height: 1.55;
        }

        .chapter-overview-copy p:last-child {
            margin-bottom: 0;
        }

        body.theme-gold .step {
            padding: 14px 0;
            border-bottom: 0;
        }

        body.theme-gold .step.has-title {
            margin-top: 24px;
            padding-top: 34px;
            border-top: 1px solid #394252;
        }

        body.theme-gold .step.has-title:first-child {
            margin-top: 0;
            border-top: 0;
        }

        body.theme-gold .step h2 {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 25px;
        }

        body.theme-gold .step-content {
            color: #d6d9df;
            line-height: 1.75;
        }

        body.theme-gold .step img {
            width: min(100%, 620px);
            margin-right: auto;
            margin-left: auto;
        }

        .empty {
            margin-top: 30px;
            padding: 24px;
            border: 1px solid color-mix(in srgb, var(--guide-border) 80%, #64748b);
            border-radius: 6px;
            background: #111a2a;
        }

        .empty h2 {
            margin: 0;
            color: #ffffff;
            font-size: 22px;
        }

        .empty p {
            margin: 8px 0 0;
            color: #aebbd0;
            line-height: 1.7;
        }

        .contribution-cta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin: 30px 0 0;
            padding: 18px 20px;
            border: 1px solid color-mix(in srgb, var(--guide-border) 80%, #64748b);
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(17, 26, 42, 0.98), rgba(10, 18, 32, 0.98));
        }

        .contribution-cta strong {
            display: block;
            color: #ffffff;
            font-size: 17px;
        }

        .contribution-cta p {
            margin: 5px 0 0;
            color: #aebbd0;
            line-height: 1.6;
        }

        .contribution-cta a {
            display: inline-flex;
            min-height: 44px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            padding: 0 18px;
            border-radius: 6px;
            background: var(--guide-accent);
            color: #04101d;
            font-weight: 800;
            text-decoration: none;
        }

        .community-guides {
            margin-top: 34px;
            padding-top: 26px;
            border-top: 1px solid color-mix(in srgb, var(--guide-border) 80%, #64748b);
        }

        .community-guides h2 {
            margin: 8px 0 0;
            color: #f8fafc;
            font-size: 24px;
            line-height: 1.25;
        }

        .community-guide-list {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .community-guide-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 18px 20px;
            border: 1px solid #526078;
            border-radius: 8px;
            background: #111a2a;
            color: inherit;
            text-decoration: none;
            transition: border-color 160ms ease, background 160ms ease;
        }

        .community-guide-card:hover {
            border-color: var(--guide-accent);
            background: #17243a;
        }

        .community-guide-card div {
            display: grid;
            gap: 5px;
        }

        .community-guide-card strong {
            color: #ffffff;
            font-size: 17px;
        }

        .community-guide-card span {
            color: #9fb0c8;
            font-size: 13px;
            font-weight: 700;
        }

        .community-guide-arrow {
            flex: 0 0 auto;
            color: var(--guide-accent) !important;
        }

        .chapter-navigation {
            margin-top: 38px;
            padding-top: 26px;
            border-top: 1px solid color-mix(in srgb, var(--guide-border) 80%, #64748b);
        }

        .chapter-navigation h2 {
            margin: 0;
            color: #f8fafc;
            font-size: 20px;
            line-height: 1.4;
        }

        .navigation-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 18px;
        }

        .navigation-link {
            display: flex;
            min-height: 96px;
            flex-direction: column;
            justify-content: center;
            padding: 17px 20px;
            border: 1px solid #526078;
            border-radius: 8px;
            background: #111a2a;
            text-decoration: none;
            transition: border-color 160ms ease, background 160ms ease;
        }

        .navigation-link:hover {
            border-color: var(--guide-accent);
            background: #17243a;
        }

        .navigation-link.next {
            text-align: right;
        }

        .navigation-label {
            color: #ffffff;
            font-size: 17px;
            font-weight: 700;
        }

        .navigation-title {
            margin-top: 5px;
            overflow-wrap: anywhere;
            color: #9fb0c8;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .navigation-spacer {
            min-height: 1px;
        }

        .comments-section {
            margin-top: 38px;
            padding: 24px;
            border: 1px solid color-mix(in srgb, var(--guide-border) 76%, rgba(255, 255, 255, 0.26));
            border-radius: 10px;
            background:
                radial-gradient(circle at top right, color-mix(in srgb, var(--guide-accent) 16%, transparent), transparent 34%),
                linear-gradient(135deg, rgba(14, 22, 35, 0.62), rgba(6, 10, 17, 0.52));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(8px);
        }

        .comments-header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
        }

        .comments-kicker {
            margin: 0 0 7px;
            color: var(--guide-accent);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .comments-header h2 {
            margin: 0;
            color: #f8fafc;
            font-size: 24px;
            line-height: 1.25;
        }

        .comments-header span {
            color: #9fb0c8;
            font-size: 13px;
            font-weight: 700;
        }

        .comment-status {
            margin: 18px 0 0;
            padding: 11px 14px;
            border: 1px solid #256d4b;
            border-radius: 6px;
            background: #10271f;
            color: #b7f7d4;
            font-size: 14px;
            font-weight: 700;
        }

        .comment-form,
        .comment-login-card,
        .comments-empty {
            margin-top: 18px;
            padding: 18px;
            border: 1px solid color-mix(in srgb, var(--guide-border) 78%, rgba(255, 255, 255, 0.24));
            border-radius: 10px;
            background: rgba(9, 15, 26, 0.58);
        }

        .comment-form {
            display: grid;
            gap: 10px;
        }

        .comment-form label {
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
        }

        .comment-form textarea {
            width: 100%;
            min-height: 118px;
            resize: vertical;
            border: 1px solid color-mix(in srgb, var(--guide-border) 72%, rgba(255, 255, 255, 0.28));
            border-radius: 7px;
            background: rgba(4, 10, 18, 0.58);
            color: #e5edf8;
            font: inherit;
            line-height: 1.6;
            padding: 12px 14px;
        }

        .comment-form textarea:focus {
            border-color: var(--guide-accent);
            outline: 2px solid color-mix(in srgb, var(--guide-accent) 35%, transparent);
            outline-offset: 2px;
        }

        .comment-form button,
        .comment-login-actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 150px;
            min-height: 42px;
            padding: 0 16px;
            border: 1px solid var(--guide-accent);
            border-radius: 6px;
            background: var(--guide-accent);
            color: #07111f;
            cursor: pointer;
            font: inherit;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
        }

        .comment-form button {
            justify-self: end;
            padding: 0 18px;
        }

        .comment-login-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .comment-login-card h3,
        .comments-empty h3 {
            margin: 0;
            color: #ffffff;
            font-size: 18px;
        }

        .comment-login-card p,
        .comments-empty p {
            margin: 7px 0 0;
            color: #9fb0c8;
            line-height: 1.6;
        }

        .comment-login-actions {
            display: flex;
            flex: 0 0 auto;
            gap: 10px;
        }

        .comment-login-actions a + a {
            border-color: #526078;
            background: #172235;
            color: #ffffff;
        }

        .comment-list {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .comment-card {
            display: grid;
            grid-template-columns: 48px minmax(0, 1fr);
            gap: 15px;
            padding: 16px;
            border: 1px solid color-mix(in srgb, var(--guide-border) 70%, rgba(255, 255, 255, 0.2));
            border-radius: 10px;
            background: rgba(4, 9, 15, 0.52);
        }

        .comment-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border: 1px solid color-mix(in srgb, var(--guide-accent) 45%, #64748b);
            border-radius: 999px;
            background: color-mix(in srgb, var(--guide-accent) 18%, #111827);
            color: #ffffff;
            font-size: 15px;
            font-weight: 900;
            overflow: hidden;
        }

        .comment-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .comment-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .comment-meta > div {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 8px;
        }

        .comment-meta strong {
            color: #ffffff;
            font-size: 15px;
        }

        .comment-meta time {
            color: #73849d;
            font-size: 12px;
            font-weight: 700;
        }

        .comment-body p {
            margin: 8px 0 0;
            color: #cbd5e1;
            line-height: 1.65;
            overflow-wrap: anywhere;
            white-space: pre-line;
        }

        .comment-delete-form {
            margin: 0;
        }

        .comment-delete-form button {
            border: 1px solid rgba(248, 113, 113, 0.32);
            border-radius: 999px;
            background: rgba(127, 29, 29, 0.12);
            color: #fca5a5;
            cursor: pointer;
            font: inherit;
            font-size: 12px;
            font-weight: 800;
            padding: 6px 10px;
        }

        .comment-delete-form button:hover {
            border-color: rgba(248, 113, 113, 0.72);
            background: rgba(127, 29, 29, 0.28);
        }

        body.theme-gold .comments-section {
            background:
                radial-gradient(circle at top right, color-mix(in srgb, var(--guide-accent) 14%, transparent), transparent 34%),
                linear-gradient(135deg, rgba(29, 27, 18, 0.64), rgba(7, 9, 8, 0.58));
        }

        body.theme-gold .comment-form,
        body.theme-gold .comment-login-card,
        body.theme-gold .comments-empty,
        body.theme-gold .comment-card {
            background: rgba(7, 9, 8, 0.52);
        }

        body.theme-gold .comment-form textarea {
            background: rgba(3, 5, 5, 0.58);
        }

        @media (max-width: 860px) {
            html {
                height: auto;
            }

            body {
                height: auto;
                min-height: 100vh;
                overflow: auto;
                overflow-x: hidden;
            }

            .walkthrough-shell {
                display: block;
                width: 100%;
                max-width: 100%;
                height: auto;
                overflow: visible;
            }

            .chapter-sidebar {
                display: block;
                position: relative;
                height: auto;
                overflow: visible;
                border-right: 0;
                border-bottom: 1px solid var(--guide-border);
            }

            .sidebar-header {
                padding: 16px 18px 12px;
            }

            .sidebar-header h2 {
                margin-top: 10px;
                font-size: 17px;
            }

            .chapter-list {
                display: flex;
                gap: 8px;
                width: 100%;
                max-width: 100vw;
                height: auto;
                padding: 10px 14px 14px;
                overflow-x: auto;
                overflow-y: hidden;
                touch-action: pan-x;
            }

            .chapter-group {
                display: contents;
            }

            .chapter-child-list {
                display: contents;
                margin: 0;
                padding-left: 0;
                border-left: 0;
            }

            .chapter-group + .chapter-group {
                margin-top: 0;
            }

            .chapter-group-title {
                display: none;
            }

            .chapter-link {
                flex: 0 0 min(260px, 78vw);
                margin: 0;
                border-left: 0;
                border-bottom: 3px solid transparent;
            }

            .chapter-link.child {
                flex-basis: min(230px, 74vw);
            }

            .chapter-link.active {
                border-bottom-color: var(--guide-accent);
            }

            .guide-scroll {
                width: 100%;
                max-width: 100%;
                height: auto;
                overflow: visible;
            }
        }

        @media (max-width: 640px) {
            .guide {
                width: auto;
                max-width: 100%;
                margin-right: 12px;
                margin-left: 12px;
                padding-top: 20px;
            }

            h1 {
                font-size: 30px;
                overflow-wrap: anywhere;
            }

            .step {
                max-width: 100%;
                padding: 24px 0 28px;
            }

            .step-content {
                max-width: 100%;
                font-size: 16px;
                overflow-wrap: anywhere;
            }

            .navigation-grid {
                grid-template-columns: 1fr;
            }

            .contribution-cta {
                align-items: stretch;
                flex-direction: column;
            }

            .community-guide-card {
                align-items: flex-start;
                flex-direction: column;
            }

            .navigation-link.next {
                text-align: left;
            }

            .chapter-overview {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .chapter-overview-media img {
                width: min(100%, 520px);
            }

            .game-engagement,
            .engagement-actions,
            .rating-form {
                align-items: stretch;
                flex-direction: column;
            }

            .engagement-actions form,
            .engagement-button,
            .engagement-login {
                width: 100%;
            }

            .comments-header,
            .comment-login-card,
            .comment-login-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .comment-form button {
                justify-self: stretch;
            }

            .comment-card {
                grid-template-columns: 38px minmax(0, 1fr);
            }

            .comment-avatar {
                width: 38px;
                height: 38px;
            }
        }
    </style>
</head>
<body class="{{ $theme['class'] }} game-{{ $chapter->game->slug }}">
    @php
        $isPersona = $chapter->game->slug === 'persona-3-reload';
        $gamePageSlug = $chapter->game->route_slug;
        $chapterUrl = static fn ($item) => $isPersona
            ? route('persona.story.show', ['mission' => $item->slug])
            : route('games.walkthrough.show', [
                'gameSlug' => $chapter->game->route_slug,
                'chapterSlug' => $item->slug,
            ]);
        $overviewImageUrl = $chapter->overview_image_url;
    @endphp

    <div class="walkthrough-shell">
        <aside class="chapter-sidebar">
            <header class="sidebar-header">
                <a href="{{ route('home') }}" class="sidebar-back">
                    <span aria-hidden="true">&larr;</span>
                    Game Library
                </a>
                <h2>Walkthrough Chapters</h2>
                <p>{{ $gameChapters->count() }} chapters</p>
            </header>

            <nav class="chapter-list" aria-label="{{ $chapter->game->title }} chapters" tabindex="0">
                @foreach ($gameChapters->whereNull('parent_id')->groupBy(fn ($item) => $item->section_title ?: 'Progress Route') as $section => $chapters)
                    <section class="chapter-group">
                        <h3 class="chapter-group-title">{{ $section }}</h3>
                        @foreach ($chapters as $sidebarChapter)
                            @php
                                $childChapters = $gameChapters->where('parent_id', $sidebarChapter->id);
                                $hasActiveChild = $childChapters->contains('id', $chapter->id);
                            @endphp
                            <a
                                href="{{ $chapterUrl($sidebarChapter) }}"
                                class="chapter-link {{ $sidebarChapter->is($chapter) ? 'active' : '' }} {{ $hasActiveChild ? 'has-active-child' : '' }}"
                                @if ($sidebarChapter->is($chapter)) aria-current="page" @endif
                            >
                                {{ $sidebarChapter->chapter_title }}
                            </a>

                            @if ($childChapters->isNotEmpty())
                                <div class="chapter-child-list" aria-label="{{ $sidebarChapter->chapter_title }} missions">
                                    @foreach ($childChapters as $childChapter)
                                        <a
                                            href="{{ $chapterUrl($childChapter) }}"
                                            class="chapter-link child {{ $childChapter->is($chapter) ? 'active' : '' }}"
                                            @if ($childChapter->is($chapter)) aria-current="page" @endif
                                        >
                                            {{ $childChapter->chapter_title }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </section>
                @endforeach
            </nav>
        </aside>

        <main class="guide-scroll">
            <div class="guide">
                <a href="{{ route('home') }}" class="back-link">
                    Back to Game Library
                </a>

                <header class="guide-header">
                    <p class="game-name">{{ $chapter->game->title }}</p>
                    <h1>{{ $chapter->chapter_title }}</h1>
                    <p class="guide-byline">
                        By Walkthrough Game Hub
                        <span class="guide-updated">Updated {{ $chapter->updated_at->format('M j, Y') }}</span>
                        @if ($chapter->source_url)
                            <a href="{{ $chapter->source_url }}" class="guide-source" target="_blank" rel="noopener noreferrer">
                                Reference source &nearr;
                            </a>
                        @endif
                    </p>
                </header>

                @if (session('status'))
                    <p class="engagement-status" role="status">{{ session('status') }}</p>
                @endif

                <section class="game-engagement" aria-label="Favorite and rating for {{ $chapter->game->title }}">
                    <div class="rating-summary">
                        @include('partials.rating-stars', [
                            'average' => $chapter->game->ratings_avg_rating,
                            'count' => $chapter->game->ratings_count,
                        ])
                    </div>

                    @auth
                        <div class="engagement-actions">
                            <form
                                action="{{ $isFavorited
                                    ? route('games.favorite.destroy', $chapter->game)
                                    : route('games.favorite.store', $chapter->game) }}"
                                method="POST"
                            >
                                @csrf
                                @if ($isFavorited)
                                    @method('DELETE')
                                @endif
                                <button type="submit" class="engagement-button {{ $isFavorited ? 'danger' : '' }}">
                                    {{ $isFavorited ? 'Remove favorite' : 'Add to favorite' }}
                                </button>
                            </form>

                            <form action="{{ route('games.rating.update', $chapter->game) }}" method="POST" class="rating-form">
                                @csrf
                                @method('PUT')
                                <fieldset class="rating-input">
                                    <legend>Your rating</legend>
                                    <div class="rating-options">
                                        @foreach (range(5, 1) as $ratingValue)
                                            <input
                                                id="game-rating-{{ $ratingValue }}"
                                                type="radio"
                                                name="rating"
                                                value="{{ $ratingValue }}"
                                                @checked((int) old('rating', $userRating) === $ratingValue)
                                                required
                                            >
                                            <label for="game-rating-{{ $ratingValue }}" title="{{ $ratingValue }} dari 5">
                                                ★
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                                <button type="submit" class="engagement-button primary">Save rating</button>
                            </form>

                            @if ($userRating)
                                <form action="{{ route('games.rating.destroy', $chapter->game) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="engagement-button danger">Remove rating</button>
                                </form>
                            @endif
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="engagement-login">Login to favorite and rate</a>
                    @endauth
                </section>

                @error('rating')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                @if (filled($chapter->overview))
                    <section class="chapter-overview" aria-label="{{ $chapter->chapter_title }} overview">
                        <div class="chapter-overview-media">
                            <h2>{{ $chapter->chapter_title }}</h2>
                            @if ($overviewImageUrl)
                                <img src="{{ $overviewImageUrl }}" alt="{{ $chapter->chapter_title }}" loading="eager" decoding="async">
                            @endif
                        </div>

                        <div class="chapter-overview-copy">
                            @foreach ($chapter->overview as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="steps">
                    @forelse ($chapter->steps as $step)
                        @php
                            $imageUrl = $step->resolved_image_url;
                            $renderedContent = strip_tags($step->content) === $step->content
                                ? nl2br(e($step->content))
                                : \App\Support\RichText::sanitizeWalkthrough($step->content);
                        @endphp

                        @php
                            $showStepTitle = filled($step->step_title)
                                && ! ($chapter->steps->count() === 1 && $step->step_title === $chapter->chapter_title);
                        @endphp

                        <article class="step {{ $showStepTitle ? 'has-title' : 'continuation' }}">
                            @if ($showStepTitle)
                                <h2>{{ $step->step_title }}</h2>
                            @endif
                            <div class="step-content">{!! $renderedContent !!}</div>

                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $step->step_title ?: $chapter->chapter_title }}" loading="lazy" decoding="async">
                            @endif
                        </article>
                    @empty
                        <div class="empty">
                            <h2>Konten belum tersedia</h2>
                            <p>Data walkthrough untuk misi ini belum diimpor ke database.</p>
                        </div>
                    @endforelse
                </section>

                <nav class="chapter-navigation" aria-label="Walkthrough navigation">
                    @if ($nextChapter)
                        <h2>Up Next: {{ $nextChapter->chapter_title }}</h2>
                    @elseif ($previousChapter)
                        <h2>Walkthrough Navigation</h2>
                    @endif

                    <div class="navigation-grid">
                        @if ($previousChapter)
                            <a href="{{ $chapterUrl($previousChapter) }}" class="navigation-link">
                                <span class="navigation-label">&larr; Previous</span>
                                <span class="navigation-title">{{ $previousChapter->chapter_title }}</span>
                            </a>
                        @else
                            <span class="navigation-spacer" aria-hidden="true"></span>
                        @endif

                        @if ($nextChapter)
                            <a href="{{ $chapterUrl($nextChapter) }}" class="navigation-link next">
                                <span class="navigation-label">Next &rarr;</span>
                                <span class="navigation-title">{{ $nextChapter->chapter_title }}</span>
                            </a>
                        @endif
                    </div>
                </nav>

                @if ($chapter->game->comments_enabled)
                    <section class="comments-section" id="comments" aria-label="Comments for {{ $chapter->chapter_title }}">
                        <div class="comments-header">
                            <div>
                                <p class="comments-kicker">Discussion</p>
                                <h2>Comments</h2>
                            </div>
                            <span>{{ $chapter->comments->count() }} comment{{ $chapter->comments->count() === 1 ? '' : 's' }}</span>
                        </div>

                        @if (session('comment_status'))
                            <p class="comment-status" role="status">{{ session('comment_status') }}</p>
                        @endif

                        @auth
                            <form action="{{ route('chapters.comments.store', $chapter) }}" method="POST" class="comment-form">
                                @csrf
                                <label for="comment-body">Join the discussion</label>
                                <textarea
                                    id="comment-body"
                                    name="body"
                                    rows="4"
                                    maxlength="2000"
                                    placeholder="Tulis komentar atau catatan tentang halaman walkthrough ini..."
                                    required
                                >{{ old('body') }}</textarea>
                                @error('body')
                                    <p class="field-error">{{ $message }}</p>
                                @enderror
                                <button type="submit">Submit comment</button>
                            </form>
                        @else
                            <div class="comment-login-card">
                                <div>
                                    <h3>Login to join the discussion</h3>
                                    <p>Guest bisa membaca komentar. Login dulu kalau mau ikut menulis komentar di halaman ini.</p>
                                </div>
                                <div class="comment-login-actions">
                                    <a href="{{ route('login') }}">Login to comment</a>
                                    <a href="{{ route('register') }}">Create account</a>
                                </div>
                            </div>
                        @endauth

                        <div class="comment-list">
                            @forelse ($chapter->comments as $comment)
                                <article class="comment-card">
                                    <div class="comment-avatar" aria-hidden="true">
                                        @if ($comment->user->avatar_url)
                                            <img src="{{ asset('storage/' . $comment->user->avatar_url) }}" alt="">
                                        @else
                                            {{ strtoupper(mb_substr($comment->user->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="comment-body">
                                        <div class="comment-meta">
                                            <div>
                                                <strong>{{ $comment->user->name }}</strong>
                                                <time datetime="{{ $comment->created_at->toIso8601String() }}">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </time>
                                            </div>
                                            @auth
                                                @if (auth()->id() === $comment->user_id || auth()->user()->hasRole('super_admin'))
                                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="comment-delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" aria-label="Delete comment">Delete</button>
                                                    </form>
                                                @endif
                                            @endauth
                                        </div>
                                        <p>{{ $comment->body }}</p>
                                    </div>
                                </article>
                            @empty
                                <div class="comments-empty">
                                    <h3>Belum ada komentar</h3>
                                    <p>Jadilah yang pertama membahas halaman walkthrough ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endif
            </div>
        </main>
    </div>

    <div class="scroll-actions" aria-label="Page scroll shortcuts">
        <button type="button" class="scroll-to-bottom" aria-label="Scroll to comments and bottom">↓</button>
        <button type="button" class="back-to-top" aria-label="Back to top">↑</button>
    </div>

    <div class="image-lightbox" data-image-lightbox aria-hidden="true">
        <button type="button" class="image-lightbox-close" data-image-lightbox-close aria-label="Close image preview">&times;</button>
        <img src="" alt="" data-image-lightbox-image>
    </div>

    <script>
        const guideScroll = document.querySelector('.guide-scroll');
        const chapterList = document.querySelector('.chapter-list');
        const activeChapter = chapterList?.querySelector('.chapter-link.active');
        const backToTopButton = document.querySelector('.back-to-top');
        const scrollToBottomButton = document.querySelector('.scroll-to-bottom');
        const imageLightbox = document.querySelector('[data-image-lightbox]');
        const imageLightboxImage = document.querySelector('[data-image-lightbox-image]');
        const imageLightboxClose = document.querySelector('[data-image-lightbox-close]');

        if (chapterList && activeChapter) {
            window.requestAnimationFrame(() => {
                if (window.matchMedia('(max-width: 860px)').matches) {
                    chapterList.scrollLeft = activeChapter.offsetLeft
                        - ((chapterList.clientWidth - activeChapter.clientWidth) / 2);
                } else {
                    chapterList.scrollTop = activeChapter.offsetTop
                        - ((chapterList.clientHeight - activeChapter.clientHeight) / 2);
                }
            });
        }

        document.querySelectorAll('.guide-scroll img').forEach((image) => {
            if (!image.hasAttribute('loading')) {
                image.setAttribute('loading', 'lazy');
            }

            image.setAttribute('decoding', 'async');

            const hideBrokenImage = () => {
                image.classList.add('is-broken');
                image.closest('.step-content figure')?.classList.add('is-broken');
            };

            image.addEventListener('error', hideBrokenImage, { once: true });

            if (image.complete && image.naturalWidth === 0) {
                hideBrokenImage();
            }

            image.addEventListener('click', () => {
                if (image.classList.contains('is-broken') || !imageLightbox || !imageLightboxImage) {
                    return;
                }

                imageLightboxImage.src = image.currentSrc || image.src;
                imageLightboxImage.alt = image.alt || 'Walkthrough image preview';
                imageLightbox.classList.add('is-open');
                imageLightbox.setAttribute('aria-hidden', 'false');
            });
        });

        const closeImageLightbox = () => {
            if (!imageLightbox || !imageLightboxImage) {
                return;
            }

            imageLightbox.classList.remove('is-open');
            imageLightbox.setAttribute('aria-hidden', 'true');
            imageLightboxImage.src = '';
            imageLightboxImage.alt = '';
        };

        imageLightboxClose?.addEventListener('click', closeImageLightbox);

        imageLightbox?.addEventListener('click', (event) => {
            if (event.target === imageLightbox) {
                closeImageLightbox();
            }
        });

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && imageLightbox?.classList.contains('is-open')) {
                closeImageLightbox();
            }
        });

        const isMobileLayout = () => window.matchMedia('(max-width: 860px)').matches;
        const currentScrollTop = () => isMobileLayout()
            ? window.scrollY
            : (guideScroll?.scrollTop ?? 0);
        const maxScrollTop = () => {
            if (isMobileLayout()) {
                return Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
            }

            return Math.max(0, (guideScroll?.scrollHeight ?? 0) - (guideScroll?.clientHeight ?? 0));
        };
        const updateScrollActions = () => {
            const top = currentScrollTop();
            const maxTop = maxScrollTop();

            backToTopButton?.classList.toggle('is-visible', top > 520);
            scrollToBottomButton?.classList.toggle('is-visible', maxTop - top > 520);
        };

        guideScroll?.addEventListener('scroll', updateScrollActions, { passive: true });
        window.addEventListener('scroll', updateScrollActions, { passive: true });
        window.addEventListener('resize', updateScrollActions, { passive: true });

        backToTopButton?.addEventListener('click', () => {
            if (isMobileLayout()) {
                window.scrollTo({ top: 0, behavior: 'smooth' });

                return;
            }

            guideScroll?.scrollTo({ top: 0, behavior: 'smooth' });
        });

        scrollToBottomButton?.addEventListener('click', () => {
            const commentsSection = document.getElementById('comments');

            if (commentsSection) {
                commentsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

                return;
            }

            if (isMobileLayout()) {
                window.scrollTo({ top: maxScrollTop(), behavior: 'smooth' });

                return;
            }

            guideScroll?.scrollTo({ top: maxScrollTop(), behavior: 'smooth' });
        });

        updateScrollActions();

        chapterList?.addEventListener('wheel', (event) => {
            if (window.matchMedia('(max-width: 860px)').matches) {
                if (chapterList.scrollWidth <= chapterList.clientWidth) {
                    return;
                }

                event.preventDefault();
                chapterList.scrollLeft += event.deltaY || event.deltaX;

                return;
            }

            if (chapterList.scrollHeight <= chapterList.clientHeight) {
                return;
            }

            event.preventDefault();
            chapterList.scrollTop += event.deltaY;
        }, { passive: false });
    </script>
</body>
</html>
