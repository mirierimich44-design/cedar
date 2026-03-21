<!-- Left side column. contains the logo and sidebar -->
<aside class="side-bar tw-relative tw-hidden tw-h-full tw-w-64 xl:tw-w-64 lg:tw-flex lg:tw-flex-col tw-shrink-0" style="background:linear-gradient(180deg,#0f172a 0%,#1e293b 100%);border-right:1px solid rgba(255,255,255,0.06);">

    <a href="{{route('home')}}"
        class="tw-flex tw-items-center tw-shrink-0"
        style="gap:12px;padding:14px 16px;background:rgba(255,255,255,0.04);border-bottom:1px solid rgba(255,255,255,0.07);text-decoration:none;">
        <span style="width:34px;height:34px;background:linear-gradient(135deg,#0369a1,#38bdf8);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
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
