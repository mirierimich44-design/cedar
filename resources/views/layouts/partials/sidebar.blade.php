@php
    $__sidebarTheme = session('business.theme_color', 'primary');
    $__sidebarAccentMap = [
        'primary' => ['from' => '#3730a3', 'to' => '#1e1b4b', 'icon_from' => '#4f46e5', 'icon_to' => '#818cf8'],
        'purple'  => ['from' => '#5b21b6', 'to' => '#2e1065', 'icon_from' => '#7c3aed', 'icon_to' => '#c4b5fd'],
        'green'   => ['from' => '#065f46', 'to' => '#022c22', 'icon_from' => '#059669', 'icon_to' => '#6ee7b7'],
        'red'     => ['from' => '#991b1b', 'to' => '#450a0a', 'icon_from' => '#dc2626', 'icon_to' => '#fca5a5'],
        'yellow'  => ['from' => '#92400e', 'to' => '#451a03', 'icon_from' => '#d97706', 'icon_to' => '#fcd34d'],
        'orange'  => ['from' => '#9a3412', 'to' => '#431407', 'icon_from' => '#ea580c', 'icon_to' => '#fdba74'],
        'sky'     => ['from' => '#075985', 'to' => '#082f49', 'icon_from' => '#0369a1', 'icon_to' => '#38bdf8'],
    ];
    $__sidebarColors = $__sidebarAccentMap[$__sidebarTheme] ?? $__sidebarAccentMap['primary'];
@endphp
<!-- Left side column. contains the logo and sidebar -->
<aside class="side-bar tw-relative tw-hidden tw-h-full tw-w-64 xl:tw-w-64 lg:tw-flex lg:tw-flex-col tw-shrink-0" style="background:linear-gradient(180deg,{{ $__sidebarColors['from'] }} 0%,{{ $__sidebarColors['to'] }} 100%);border-right:1px solid rgba(255,255,255,0.06);">

    <a href="{{route('home')}}"
        class="tw-flex tw-items-center tw-shrink-0"
        style="gap:12px;padding:14px 16px;background:rgba(255,255,255,0.04);border-bottom:1px solid rgba(255,255,255,0.07);text-decoration:none;">
        <span style="width:34px;height:34px;background:linear-gradient(135deg,{{ $__sidebarColors['icon_from'] }},{{ $__sidebarColors['icon_to'] }});border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg style="width:18px;height:18px;color:white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"/><path d="M3 11h18"/>
            </svg>
        </span>
        <div style="min-width:0;">
            <p style="color:white;font-size:13px;font-weight:700;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" class="side-bar-heading">{{ Session::get('business.name') }}</p>
            <p style="color:#64748b;font-size:11px;margin:0;">
                <span style="display:inline-block;width:6px;height:6px;background:#4ade80;border-radius:50%;margin-right:4px;vertical-align:middle;"></span>Online
            </p>
        </div>
    </a>

    <!-- Sidebar Menu -->
    {!! Menu::render('admin-sidebar-menu', 'adminltecustom') !!}

</aside>
