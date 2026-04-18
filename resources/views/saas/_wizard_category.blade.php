{{-- Category accordion. Expects: $catKey --}}
@if($featuresByCategory->has($catKey) && isset($categories[$catKey]))
    @php
        $catMeta = $categories[$catKey];
        $items   = $featuresByCategory[$catKey];
        $open    = $catKey === 'core';
    @endphp
    <div class="cat-box {{ $open ? 'open' : '' }}" data-cat="{{ $catKey }}" data-reco="0">
        <div class="cat-head" onclick="toggleCat(this)">
            <div class="cat-icon"><i class="fas {{ $catMeta['icon'] }}"></i></div>
            <div class="cat-title">
                <b>{{ $catMeta['label'] }}</b>
                <small>{{ $items->count() }} module{{ $items->count() === 1 ? '' : 's' }} available</small>
            </div>
            <span class="cat-badge">Recommended</span>
            <span class="cat-count">0 selected</span>
            <i class="fas fa-chevron-down cat-chevron"></i>
        </div>
        <div class="cat-body">
            @foreach($items as $feature)
            <div class="frow" data-feature-id="{{ $feature->id }}" data-cat-key="{{ $catKey }}"
                 onclick="{{ $feature->is_required ? '' : 'toggleFeature(' . $feature->id . ')' }}">
                <div class="fcheck {{ $feature->is_required ? 'checked required' : '' }}" id="check-{{ $feature->id }}">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                        <path d="M2 6.5L5 9.5L11 3.5" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="finfo">
                    <div class="fname">
                        <span class="fn">{{ $feature->name }}</span>
                        @if($feature->is_required)<span class="inc-tag">· Included</span>@endif
                        <span class="reco-tag">Recommended</span>
                    </div>
                    @if($feature->description)<div class="fdesc">{{ $feature->description }}</div>@endif
                </div>
                <div class="fprice {{ $feature->price_monthly == 0 ? 'free' : '' }}"
                     id="price-{{ $feature->id }}"
                     data-monthly="{{ $feature->price_monthly }}"
                     data-quarterly="{{ $feature->price_quarterly }}"
                     data-yearly="{{ $feature->price_yearly }}"
                     data-once="{{ $feature->price_once }}">
                    @if($feature->price_monthly == 0)
                        Free
                    @else
                        KES {{ number_format($feature->price_monthly, 0) }}<small>/mo</small>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endif
