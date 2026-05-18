<?php
/**
 * Batch apply modern banner to index pages that still have the old content-header pattern.
 * Run: php apply_banner.php
 */

$viewsBase = __DIR__ . '/resources/views';

$pages = [
    // Batch 4 — restaurant module + misc
    'restaurant/booking/index.blade.php'       => ['icon' => 'fa-calendar-alt'],
    'restaurant/modifier_sets/index.blade.php' => ['icon' => 'fa-sliders-h'],
    'restaurant/table/index.blade.php'         => ['icon' => 'fa-chair'],
    'parcel/routes/index.blade.php'            => ['icon' => 'fa-route'],
    'sync/index.blade.php'                     => ['icon' => 'fa-sync'],
];

$cssInclude = "\n@section('css')\n@parent\n@include('layouts.partials.page_modern_css')\n@endsection\n";
$cssInsert = "\n@include('layouts.partials.page_modern_css')\n";

foreach ($pages as $file => $cfg) {
    $path = $viewsBase . '/' . $file;
    if (!file_exists($path)) {
        echo "SKIP (not found): $file\n";
        continue;
    }

    $content = file_get_contents($path);

    // Skip if already processed
    if (strpos($content, 'page-modern') !== false || strpos($content, 'pg-banner') !== false) {
        echo "SKIP (already done): $file\n";
        continue;
    }

    $icon = $cfg['icon'] ?? 'fa-list';

    // 1. Does it have @section('css') already?
    $hasExistingCss = preg_match("/@section\('css'\)/", $content);

    if ($hasExistingCss) {
        // Insert @include before the closing @endsection of css section
        $content = preg_replace(
            "/(@section\('css'\))(.*?)(@endsection)/s",
            "$1$2\n@include('layouts.partials.page_modern_css')\n$3",
            $content,
            1
        );
    } else {
        // Add new @section('css') before @section('content')
        $content = str_replace(
            "@section('content')",
            $cssInclude . "\n@section('content')",
            $content
        );
    }

    // 2. Parse existing h1 from content-header
    preg_match('/<section[^>]*class="[^"]*content-header[^"]*"[^>]*>(.*?)<\/section>/s', $content, $headerMatch);
    $headerInner = $headerMatch[1] ?? '';

    // Extract title - look for @lang
    $title = '@lang(\'...\')';
    preg_match('/@lang\([^)]+\)/', $headerInner, $langMatch);
    if ($langMatch) {
        $title = $langMatch[0];
    }

    // Extract subtitle - look for second @lang or small tag content
    $subtitle = "{{ session('business.name') }}";
    preg_match_all('/@lang\([^)]+\)/', $headerInner, $allLangs);
    if (!empty($allLangs[0][1])) {
        $subtitle = $allLangs[0][1] . " &middot; {{ session('business.name') }}";
    }

    // Check if content-header has no-print class
    $noPrint = (strpos($headerMatch[0] ?? '', 'no-print') !== false) ? ' no-print' : '';

    // 3. Build banner HTML
    $banner = "\n<div class=\"page-modern\">\n\n"
        . "    <section class=\"content-header{$noPrint}\"></section>\n\n"
        . "    <div class=\"pg-banner{$noPrint}\">\n"
        . "        <div class=\"pg-banner-inner\">\n"
        . "            <div class=\"pg-banner-title\">\n"
        . "                <div class=\"pg-banner-icon\">\n"
        . "                    <i class=\"fas {$icon}\"></i>\n"
        . "                </div>\n"
        . "                <div>\n"
        . "                    <h1>{$title}</h1>\n"
        . "                    <p class=\"pg-subtitle\">{$subtitle}</p>\n"
        . "                </div>\n"
        . "            </div>\n"
        . "            <div class=\"pg-banner-actions\"></div>\n"
        . "        </div>\n"
        . "    </div>\n\n";

    // 4. Replace the content-header section with banner
    $content = preg_replace(
        '/<section[^>]*class="[^"]*content-header[^"]*"[^>]*>.*?<\/section>\s*/s',
        $banner,
        $content,
        1
    );

    // 5. Add closing div before last @endsection or @stop
    if (preg_match('/\n<\/div>\{\{-- \.page-modern --\}\}/', $content)) {
        echo "SKIP (closing div already present): $file\n";
    } else {
        // Replace last @endsection or @stop
        $content = preg_replace('/(@endsection|@stop)\s*$/', "\n</div>{{-- .page-modern --}}\n$1", $content);
    }

    file_put_contents($path, $content);
    echo "DONE: $file\n";
}

echo "\nAll done!\n";
