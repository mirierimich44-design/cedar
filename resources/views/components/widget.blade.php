<div class="{{$class ?? ''}} tw-mb-4 tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md tw-ring-gray-200"
    style="overflow:hidden;"
    @if (!empty($id)) id="{{ $id }}" @endif>

    @if (empty($header))
        @if (!empty($title) || !empty($tool))
            <div style="background:linear-gradient(135deg,#0369a1 0%,#38bdf8 100%); padding:12px 16px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    @if (!empty($icon))
                        <span style="width:28px;height:28px;background:rgba(255,255,255,0.15);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            {!! $icon !!}
                        </span>
                    @else
                        <span style="width:28px;height:28px;background:rgba(255,255,255,0.15);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:14px;height:14px;color:white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8"/><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"/>
                            </svg>
                        </span>
                    @endif
                    <h3 style="color:white;font-weight:700;font-size:14px;margin:0;">{{ $title ?? '' }}</h3>
                    @if (isset($help_text))
                        <small style="color:rgba(255,255,255,0.6);font-size:11px;">{!! $help_text !!}</small>
                    @endif
                </div>
                @if (!empty($tool))
                    <div style="display:flex;align-items:center;gap:8px;">
                        {!! $tool !!}
                    </div>
                @endif
            </div>
        @endif
    @else
        <div class="box-header">
            {!! $header !!}
        </div>
    @endif

    <div class="tw-flow-root">
        <div style="padding:8px 12px;">
            {{ $slot }}
        </div>
    </div>
</div>
