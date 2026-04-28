<div class="tw-transition-all tw-mb-4 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md tw-ring-gray-200" style="overflow:hidden;">
    <div style="background:linear-gradient(135deg,var(--theme-dark,#312e81) 0%,var(--theme-main,#4f46e5) 100%); padding:10px 16px; cursor:pointer;"
         data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="width:26px;height:26px;background:rgba(255,255,255,0.15);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                @if (!empty($icon))
                    {!! $icon !!}
                @else
                    <i class="fa fa-filter" style="color:white;font-size:11px;"></i>
                @endif
            </span>
            <h3 style="color:white;font-weight:600;font-size:13px;margin:0;">{{ $title ?? __('report.filters') }}</h3>
            <i class="fa fa-chevron-down" style="color:rgba(255,255,255,0.6);font-size:10px;margin-left:auto;"></i>
        </div>
    </div>
    @php
        $closed = isMobile();
    @endphp
    <div id="collapseFilter" class="panel-collapse collapse @if(empty($closed)) in @endif tw-pt-3 tw-pb-3" aria-expanded="{{ empty($closed) ? 'true' : 'false' }}">
        <div class="box-body">
            <div class="row">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
