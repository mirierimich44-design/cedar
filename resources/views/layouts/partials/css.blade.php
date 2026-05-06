<link href="{{ asset('css/tailwind/app.css?v='.$asset_v) }}" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">

@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
@endif

@yield('css')

<!-- app css -->
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">

<!-- Modern Theme -->
<link rel="stylesheet" href="{{ asset('css/modern-theme.css?v='.$asset_v) }}">
<link rel="stylesheet" href="{{ asset('css/modern-minimal.css?v='.$asset_v) }}">
<link rel="stylesheet" href="{{ asset('css/backend-material.css?v='.$asset_v) }}">

<!-- Dynamic Theme + Font -->
@php
$__tc  = session('business.theme_color', 'primary');
$__ff  = session('business.font_family', 'system');
// Font map: key => [Google Fonts URL param, CSS stack]
$__fontMap = [
    'system'       => [null, '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif'],
    'inter'        => ['Inter:wght@400;500;600;700', '"Inter",sans-serif'],
    'poppins'      => ['Poppins:wght@400;500;600;700', '"Poppins",sans-serif'],
    'roboto'       => ['Roboto:wght@400;500;700', '"Roboto",sans-serif'],
    'dm_sans'      => ['DM+Sans:wght@400;500;600;700', '"DM Sans",sans-serif'],
    'plus_jakarta' => ['Plus+Jakarta+Sans:wght@400;500;600;700', '"Plus Jakarta Sans",sans-serif'],
    'nunito'       => ['Nunito:wght@400;500;600;700', '"Nunito",sans-serif'],
    'lato'         => ['Lato:wght@400;700', '"Lato",sans-serif'],
];
$__fontCfg   = $__fontMap[$__ff] ?? $__fontMap['system'];
$__fontUrl   = $__fontCfg[0];
$__fontStack = $__fontCfg[1];
@endphp
@if($__fontUrl)
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family={{ $__fontUrl }}&display=swap" rel="stylesheet">
@endif

<!-- Dynamic Theme Color CSS Variables -->
$__themeVars = [
    'primary' => [
        'main'   => '#4f46e5', 'hover'  => '#4338ca',
        'light'  => '#818cf8', 'dark'   => '#3730a3',
        'subtle' => '#eef2ff', 'border' => '#c7d2fe',
        'ring'   => '#a5b4fc',
    ],
    'purple' => [
        'main'   => '#9333ea', 'hover'  => '#7c3aed',
        'light'  => '#c084fc', 'dark'   => '#6d28d9',
        'subtle' => '#faf5ff', 'border' => '#ddd6fe',
        'ring'   => '#d8b4fe',
    ],
    'green' => [
        'main'   => '#059669', 'hover'  => '#047857',
        'light'  => '#34d399', 'dark'   => '#065f46',
        'subtle' => '#ecfdf5', 'border' => '#a7f3d0',
        'ring'   => '#6ee7b7',
    ],
    'red' => [
        'main'   => '#dc2626', 'hover'  => '#b91c1c',
        'light'  => '#f87171', 'dark'   => '#991b1b',
        'subtle' => '#fef2f2', 'border' => '#fecaca',
        'ring'   => '#fca5a5',
    ],
    'yellow' => [
        'main'   => '#d97706', 'hover'  => '#b45309',
        'light'  => '#fbbf24', 'dark'   => '#92400e',
        'subtle' => '#fffbeb', 'border' => '#fde68a',
        'ring'   => '#fcd34d',
    ],
    'orange' => [
        'main'   => '#ea580c', 'hover'  => '#c2410c',
        'light'  => '#fb923c', 'dark'   => '#9a3412',
        'subtle' => '#fff7ed', 'border' => '#fed7aa',
        'ring'   => '#fdba74',
    ],
    'sky' => [
        'main'   => '#0284c7', 'hover'  => '#0369a1',
        'light'  => '#38bdf8', 'dark'   => '#075985',
        'subtle' => '#f0f9ff', 'border' => '#bae6fd',
        'ring'   => '#7dd3fc',
    ],
];
$__cv = $__themeVars[$__tc] ?? $__themeVars['primary'];
@endphp
<style>
:root {
    /* pos-material / backend-material tokens */
    --pos-primary:        {{ $__cv['main'] }};
    --pos-primary-light:  {{ $__cv['light'] }};
    --pos-primary-dark:   {{ $__cv['dark'] }};
    --pos-primary-subtle: {{ $__cv['subtle'] }};
    /* modern-theme tokens */
    --primary:            {{ $__cv['main'] }};
    --primary-hover:      {{ $__cv['hover'] }};
    --primary-light:      {{ $__cv['subtle'] }};
    /* modern-minimal tokens */
    --primary-modern:       {{ $__cv['main'] }};
    --primary-modern-hover: {{ $__cv['hover'] }};
    /* shared accent helpers */
    --theme-main:   {{ $__cv['main'] }};
    --theme-hover:  {{ $__cv['hover'] }};
    --theme-light:  {{ $__cv['light'] }};
    --theme-dark:   {{ $__cv['dark'] }};
    --theme-subtle: {{ $__cv['subtle'] }};
    --theme-border: {{ $__cv['border'] }};
    --theme-ring:   {{ $__cv['ring'] }};
    /* Font */
    --apex-font: {{ $__fontStack }};
}
body, .content-wrapper, .main-header, .sidebar-menu,
input, select, textarea, button, .form-control, .select2-selection,
.modal-body, .modal-header, .modal-footer, .box, .box-body {
    font-family: var(--apex-font) !important;
}
</style>

<!-- Static overrides: header, dark-mode, modals, sidebar, tables — browser-cacheable -->
<link rel="stylesheet" href="{{ asset('css/apex-overrides.css?v='.$asset_v) }}">

<script>
// Apply dark mode immediately from localStorage to avoid flash
if(localStorage.getItem('apex_dark_mode')==='1'){document.documentElement.classList.add('dark-mode-loading');document.body && document.body.classList.add('dark-mode');}
</script>

<!-- Mobile Responsive -->
<link rel="stylesheet" href="{{ asset('css/mobile-responsive.css?v='.$asset_v) }}">

@if(isset($pos_layout) && $pos_layout)
	<link rel="stylesheet" href="{{ asset('css/pos-material.css?v='.$asset_v) }}">
	<link rel="stylesheet" href="{{ asset('css/pos-redesign.css?v=' . time()) }}">
@endif

@if(!empty($__system_settings['additional_css']))
    {!! $__system_settings['additional_css'] !!}
@endif
