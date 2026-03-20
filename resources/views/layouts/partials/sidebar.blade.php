<!-- Left side column. contains the logo and sidebar -->
<aside class="side-bar tw-relative tw-hidden tw-h-full tw-bg-white tw-w-64 xl:tw-w-64 lg:tw-flex lg:tw-flex-col tw-shrink-0">

    <!-- sidebar: style can be found in sidebar.less -->

    {{-- <a href="{{route('home')}}" class="logo">
		<span class="logo-lg">{{ Session::get('business.name') }}</span>
	</a> --}}

    <a href="{{route('home')}}"
        class="tw-flex tw-items-center tw-justify-start tw-px-3 tw-pl-9 tw-w-full tw-h-24 tw-bg-white tw-shrink-0">
        <p class="tw-text-2xl tw-font-black tw-tracking-tighter tw-bg-gradient-to-r tw-from-indigo-600 tw-to-violet-600 tw-bg-clip-text tw-text-transparent side-bar-heading">
            {{ Session::get('business.name') }} <span class="tw-inline-block tw-w-2 tw-h-2 tw-bg-green-500 tw-rounded-full tw-ml-1 tw-align-middle" title="Online"></span>
        </p>
    </a>

    <!-- Sidebar Menu -->
    {!! Menu::render('admin-sidebar-menu', 'adminltecustom') !!}

    <!-- /.sidebar-menu -->
    <!-- /.sidebar -->
</aside>
