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
</style>

