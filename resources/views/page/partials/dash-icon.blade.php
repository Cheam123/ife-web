{{-- Stroke icon for the dashboards: @include('page.partials.dash-icon', ['name' => 'alert', 'size' => 20]).
     Decorative only (aria-hidden); the text next to it carries the meaning. --}}
@php
    $size  = $size ?? 20;
    $paths = [
        'error'        => '<circle cx="12" cy="12" r="10"></circle><path d="m15 9-6 6"></path><path d="m9 9 6 6"></path>',
        'alert'        => '<path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path>',
        'check-circle' => '<circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path>',
        'check'        => '<path d="M20 6 9 17l-5-5"></path>',
        'clock'        => '<circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path>',
        'form'         => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><path d="m9 15 2 2 4-4"></path>',
        'bell-off'     => '<path d="M8.7 3A6 6 0 0 1 18 8a21.3 21.3 0 0 0 .6 5"></path><path d="M17 17H3s3-2 3-9a4.7 4.7 0 0 1 .3-1.7"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path><path d="m2 2 20 20"></path>',
        'store'        => '<path d="M3 9 4.5 4h15L21 9"></path><path d="M3 9h18v1.5a3 3 0 0 1-6 0 3 3 0 0 1-6 0 3 3 0 0 1-6 0z"></path><path d="M5 13v7h14v-7"></path><path d="M10 20v-4h4v4"></path>',
        'info'         => '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>',
        'gauge'        => '<path d="m12 14 4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path>',
        'arrow-up'     => '<path d="M12 19V5"></path><path d="m5 12 7-7 7 7"></path>',
        'arrow-down'   => '<path d="M12 5v14"></path><path d="m19 12-7 7-7-7"></path>',
        'arrow-right'  => '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
        'minus'        => '<path d="M5 12h14"></path>',
        'search'       => '<circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path>',
        'plus'         => '<path d="M12 5v14"></path><path d="M5 12h14"></path>',
        'users'        => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.9"></path><path d="M16 3.1a4 4 0 0 1 0 7.8"></path>',
        'file-text'    => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><path d="M8 13h8"></path><path d="M8 17h5"></path>',
        'package'      => '<path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.7z"></path><path d="M3.3 7 12 12l8.7-5"></path><path d="M12 22V12"></path>',
        'map'          => '<path d="M9 3 3 6v15l6-3 6 3 6-3V3l-6 3z"></path><path d="M9 3v15"></path><path d="M15 6v15"></path>',
        'refresh'      => '<path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"></path><path d="M3 12A9 9 0 0 1 18.5 5.8L21 8"></path><path d="M21 3v5h-5"></path><path d="M3 21v-5h5"></path>',
    ];
@endphp
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $stroke ?? 2 }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">{!! $paths[$name] ?? '' !!}</svg>
