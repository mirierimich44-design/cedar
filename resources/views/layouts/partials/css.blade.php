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

<!-- Dynamic Theme Color CSS Variables -->
@php
$__tc = session('business.theme_color', 'primary');
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
}

/* ─── Header buttons (sidebar toggles, dropdowns) ─── */
.header-theme-btn {
    background-color: var(--theme-dark) !important;
    color: white !important;
}
.header-theme-btn:hover,
.header-theme-btn:focus {
    background-color: var(--theme-hover) !important;
    color: white !important;
}

/* ─── Override hardcoded indigo/blue gradient buttons system-wide ─── */
.tw-from-indigo-600.tw-to-blue-500,
.tw-from-indigo-500.tw-to-blue-500,
.tw-from-indigo-600.tw-to-blue-600 {
    background-image: linear-gradient(to right, var(--theme-dark), var(--theme-main)) !important;
}
.hover\:tw-from-indigo-600:hover,
.hover\:tw-from-indigo-700:hover {
    background-image: linear-gradient(to right, var(--theme-hover), var(--theme-main)) !important;
}

/* ─── Override hardcoded blue step-indicator badges ─── */
.tw-bg-blue-600 {
    background-color: var(--theme-main) !important;
}
.tw-border-blue-400, .tw-border-blue-500 {
    border-color: var(--theme-main) !important;
}
.tw-text-blue-600, .tw-text-blue-700, .tw-text-indigo-600 {
    color: var(--theme-main) !important;
}
.focus\:tw-ring-blue-500:focus {
    --tw-ring-color: var(--theme-ring) !important;
}
</style>

<!-- Mobile Responsive -->
<link rel="stylesheet" href="{{ asset('css/mobile-responsive.css?v='.$asset_v) }}">

@if(isset($pos_layout) && $pos_layout)
	<link rel="stylesheet" href="{{ asset('css/pos-material.css?v='.$asset_v) }}">
	<link rel="stylesheet" href="{{ asset('css/pos-redesign.css?v=' . time()) }}">
@endif

<style type="text/css">
/* Disabled conflicting global overrides to allow redesign to take full effect
    .product_row td, 
    ...
*/
</style>
<style type="text/css">
	/*
	* Pattern lock css
	* Pattern direction
	* http://ignitersworld.com/lab/patternLock.html
	*/
	.patt-wrap {
	  z-index: 10;
	}
	.patt-circ.hovered {
	  background-color: #cde2f2;
	  border: none;
	}
	.patt-circ.hovered .patt-dots {
	  display: none;
	}
	.patt-circ.dir {
	  background-image: url("{{asset('/img/pattern-directionicon-arrow.png')}}");
	  background-position: center;
	  background-repeat: no-repeat;
	}
	.patt-circ.e {
	  -webkit-transform: rotate(0);
	  transform: rotate(0);
	}
	.patt-circ.s-e {
	  -webkit-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	.patt-circ.s {
	  -webkit-transform: rotate(90deg);
	  transform: rotate(90deg);
	}
	.patt-circ.s-w {
	  -webkit-transform: rotate(135deg);
	  transform: rotate(135deg);
	}
	.patt-circ.w {
	  -webkit-transform: rotate(180deg);
	  transform: rotate(180deg);
	}
	.patt-circ.n-w {
	  -webkit-transform: rotate(225deg);
	   transform: rotate(225deg);
	}
	.patt-circ.n {
	  -webkit-transform: rotate(270deg);
	  transform: rotate(270deg);
	}
	.patt-circ.n-e {
	  -webkit-transform: rotate(315deg);
	  transform: rotate(315deg);
	}
</style>
@if(!empty($__system_settings['additional_css']))
    {!! $__system_settings['additional_css'] !!}
@endif

{{-- SIDEBAR LOCK — must win over everything including system additional_css --}}
<style>
.side-bar {
    background: linear-gradient(180deg,#0f172a 0%,#1e293b 100%) !important;
    border-right: 1px solid rgba(255,255,255,0.06) !important;
}
.side-bar a,
.side-bar .sidebar-nav-link,
.side-bar .sidebar-child-link {
    color: rgba(255,255,255,0.72) !important;
}
.side-bar .sidebar-nav-link.active-item,
.side-bar a[style*="border-left:3px solid"] {
    color: #ffffff !important;
}
</style>

{{-- GLOBAL TABLE STYLE — loaded last, beats everything --}}
<style>
/* ─── Table reset ─────────────────────────────────────────── */
table.table,
table.dataTable,
.dataTables_wrapper table {
    border: none !important;
    border-collapse: collapse !important;
    border-spacing: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background: #fff !important;
    font-size: 13px !important;
}

/* ─── Header ──────────────────────────────────────────────── */
table.table thead th,
table.table thead td,
table.dataTable thead th,
table.dataTable thead td,
.dataTables_wrapper table thead th,
.dataTables_wrapper table thead td {
    background: #f8fafc !important;
    color: #64748b !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.06em !important;
    padding: 6px 10px !important;
    border: none !important;
    border-bottom: 2px solid #e2e8f0 !important;
    white-space: nowrap;
}

/* ─── Body cells ──────────────────────────────────────────── */
table.table tbody td,
table.table tbody th,
table.dataTable tbody td,
table.dataTable tbody th,
.dataTables_wrapper table tbody td {
    padding: 5px 10px !important;
    border: none !important;
    border-bottom: 1px solid #f1f5f9 !important;
    vertical-align: middle !important;
    font-size: 13px !important;
    line-height: 1.4 !important;
}

/* ─── Last row: no divider ────────────────────────────────── */
table.table tbody tr:last-child td,
table.table tbody tr:last-child th,
table.dataTable tbody tr:last-child td {
    border-bottom: none !important;
}

/* ─── No striping ─────────────────────────────────────────── */
table.table tbody tr,
table.table tbody tr:nth-child(odd),
table.table tbody tr:nth-child(even),
table.table-striped tbody tr:nth-of-type(odd),
table.table-striped tbody tr:nth-of-type(even),
table.dataTable tbody tr,
table.dataTable tbody tr:nth-child(odd),
table.dataTable tbody tr:nth-child(even),
.dataTables_wrapper table tbody tr,
.dataTables_wrapper table tbody tr:nth-child(odd),
.dataTables_wrapper table tbody tr:nth-child(even) {
    background: #ffffff !important;
}

/* ─── Hover ───────────────────────────────────────────────── */
table.table tbody tr:hover td,
table.table tbody tr:hover th,
table.dataTable tbody tr:hover td,
.dataTables_wrapper table tbody tr:hover td {
    background: #f8fafc !important;
}

/* ─── Footer ──────────────────────────────────────────────── */
table.table tfoot th,
table.table tfoot td,
table.dataTable tfoot th,
table.dataTable tfoot td {
    background: #f8fafc !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    padding: 5px 10px !important;
    border: none !important;
    border-top: 2px solid #e2e8f0 !important;
}

/* ─── Remove all borders from bordered variant ────────────── */
table.table-bordered td,
table.table-bordered th {
    border: none !important;
    border-bottom: 1px solid #f1f5f9 !important;
}

/* ─── Column Visibility button: match CSV/Excel/Print size ── */
button.buttons-colvis,
a.buttons-colvis,
.dt-buttons .buttons-colvis,
.dt-buttons button.buttons-colvis {
    background: #14b8a6 !important;
    color: #fff !important;
    padding: 6px 12px !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    border: none !important;
    border-radius: 4px !important;
    line-height: 1.42857143 !important;
    height: auto !important;
    min-height: 0 !important;
    display: inline-block !important;
    box-sizing: border-box !important;
}
button.buttons-colvis:hover,
.dt-buttons .buttons-colvis:hover {
    background: #0d9488 !important;
}

/* ─── Compact action button for datatables ───────────────── */
button.product-action-btn,
.product-action-btn.btn,
.product-action-btn {
    padding: 1px 7px !important;
    font-size: 10px !important;
    line-height: 1.5 !important;
    border-radius: 4px !important;
    box-shadow: none !important;
    transform: none !important;
    font-weight: 500 !important;
    height: auto !important;
    min-height: 0 !important;
}

/* ─── DataTables: Show X entries select ──────────────────── */
.dataTables_wrapper .dataTables_length {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}
.dataTables_wrapper .dataTables_length label {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: nowrap !important;
    margin: 0 !important;
    white-space: nowrap !important;
}
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_length select.form-control {
    width: auto !important;
    min-width: 0 !important;
    max-width: none !important;
    display: inline-block !important;
    flex-shrink: 0 !important;
    height: 32px !important;
    padding: 0 8px !important;
    font-size: 13px !important;
    line-height: 1 !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 6px !important;
    background: #fff !important;
    box-shadow: none !important;
    cursor: pointer !important;
    appearance: auto !important;
    -webkit-appearance: auto !important;
}

/* ─── Mobile: prevent dark overlay on sidebar toggle tap ─── */
.sidebar-overlay,
.control-sidebar-bg,
body.sidebar-open::after {
    display: none !important;
}
@media (max-width: 991px) {
    .main-sidebar { z-index: 1050; }
    body.sidebar-open .content-wrapper,
    body.sidebar-open .main-header { opacity: 1 !important; pointer-events: auto !important; }
}
</style>

