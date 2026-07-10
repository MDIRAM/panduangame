@php
    $ratingAverage = max(0, min(5, (float) ($average ?? 0)));
    $ratingCount = (int) ($count ?? 0);
    $ratingLabel = $ratingCount > 0
        ? number_format($ratingAverage, 1).'/5 dari '.$ratingCount.' user'
        : 'Belum ada rating';
@endphp

<span class="rating-display" aria-label="{{ $ratingLabel }}">
    <span class="rating-stars" aria-hidden="true">
        <span class="rating-stars-track">★★★★★</span>
        <span class="rating-stars-fill" style="width: {{ $ratingAverage * 20 }}%">★★★★★</span>
    </span>
    <span class="rating-value">
        {{ $ratingCount > 0 ? number_format($ratingAverage, 1).' ('.$ratingCount.')' : 'Belum ada rating' }}
    </span>
</span>
