<!-- Left side column. contains the logo and sidebar -->
<aside class="side-bar tw-relative tw-hidden tw-h-full tw-w-64 xl:tw-w-64 lg:tw-flex lg:tw-flex-col tw-shrink-0">

    <a href="{{route('home')}}"
        class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-4 tw-shrink-0"
        style="background:rgba(255,255,255,0.04);border-bottom:1px solid rgba(255,255,255,0.07);">
        <span style="width:34px;height:34px;background:linear-gradient(135deg,#0369a1,#38bdf8);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg style="width:18px;height:18px;color:white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"/><path d="M3 11h18"/>
            </svg>
        </span>
        <div class="tw-min-w-0">
            <p class="tw-text-sm tw-font-bold tw-text-white tw-m-0 tw-truncate side-bar-heading">{{ Session::get('business.name') }}</p>
            <p class="tw-text-xs tw-text-slate-400 tw-m-0">
                <span class="tw-inline-block tw-w-1.5 tw-h-1.5 tw-bg-green-400 tw-rounded-full tw-mr-1 tw-align-middle"></span>Online
            </p>
        </div>
    </a>

    <!-- Sidebar Menu -->
    {!! Menu::render('admin-sidebar-menu', 'adminltecustom') !!}

</aside>
