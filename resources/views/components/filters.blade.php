<div class="tw-mb-4 tw-bg-white tw-rounded-xl tw-ring-1 tw-ring-gray-200 tw-shadow-sm">
    <div class="tw-flex tw-items-center tw-justify-between tw-px-5 tw-py-3 tw-border-b tw-border-gray-100 tw-cursor-pointer"
         data-toggle="collapse" data-target="#collapseFilter" aria-expanded="true">
        <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm tw-font-semibold tw-text-gray-600">
            @if (!empty($icon))
                {!! $icon !!}
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
            @endif
            {{ $title ?? __('report.filters') }}
        </div>
        <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-transition-transform" xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </div>
    <div id="collapseFilter" class="collapse in">
        <div class="tw-px-5 tw-py-4">
            <div class="row">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
