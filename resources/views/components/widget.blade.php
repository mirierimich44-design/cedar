<div class="{{ $class ?? '' }} tw-mb-4 tw-transition-all lg:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md tw-ring-gray-200"
    @if (!empty($id)) id="{{ $id }}" @endif>
    @if (empty($header))
        @if (!empty($title) || !empty($tool))
            <div class="tw-flex tw-items-center tw-justify-between tw-px-5 tw-py-3 tw-border-b tw-border-gray-100">
                <div class="tw-flex tw-items-center tw-gap-2">
                    {!! $icon ?? '' !!}
                    <h3 class="tw-text-base tw-font-semibold tw-text-gray-800 tw-m-0">{{ $title ?? '' }}</h3>
                    @if (isset($help_text))
                        <span class="tw-text-xs tw-text-gray-400 tw-font-normal">{!! $help_text !!}</span>
                    @endif
                </div>
                {!! $tool ?? '' !!}
            </div>
        @endif
    @else
        <div class="tw-flex tw-items-center tw-justify-between tw-px-5 tw-py-3 tw-border-b tw-border-gray-100">
            {!! $header !!}
        </div>
    @endif
    <div class="tw-p-2 sm:tw-p-4">
        {{ $slot }}
    </div>
</div>
