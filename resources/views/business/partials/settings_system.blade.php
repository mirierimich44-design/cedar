<div class="pos-tab-content">
     <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('theme_color', __('lang_v1.theme_color')); !!}
                @php
                    $__colorSwatches = [
                        'primary' => ['label' => 'Blue',   'bg' => '#4f46e5', 'dark' => '#3730a3'],
                        'purple'  => ['label' => 'Purple', 'bg' => '#7c3aed', 'dark' => '#5b21b6'],
                        'green'   => ['label' => 'Green',  'bg' => '#059669', 'dark' => '#065f46'],
                        'red'     => ['label' => 'Red',    'bg' => '#dc2626', 'dark' => '#991b1b'],
                        'yellow'  => ['label' => 'Yellow', 'bg' => '#d97706', 'dark' => '#92400e'],
                        'orange'  => ['label' => 'Orange', 'bg' => '#ea580c', 'dark' => '#9a3412'],
                        'sky'     => ['label' => 'Sky',    'bg' => '#0284c7', 'dark' => '#075985'],
                    ];
                    $__currentColor = $business->theme_color ?? 'primary';
                @endphp

                {{-- Hidden select for form submission --}}
                {!! Form::select('theme_color', $theme_colors, $__currentColor,
                    ['class' => 'form-control', 'id' => 'theme_color_select', 'style' => 'display:none;']); !!}

                {{-- Visual swatch picker --}}
                <div class="theme-color-swatches" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:6px;">
                    @foreach($__colorSwatches as $colorKey => $colorData)
                        @if(isset($theme_colors[$colorKey]))
                        <label class="theme-swatch-label" style="cursor:pointer;text-align:center;">
                            <input type="radio" name="_theme_color_swatch" value="{{ $colorKey }}"
                                {{ $__currentColor === $colorKey ? 'checked' : '' }}
                                style="display:none;"
                                onchange="document.getElementById('theme_color_select').value=this.value;">
                            <span class="theme-swatch-circle" data-color="{{ $colorKey }}"
                                style="display:block;width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,{{ $colorData['bg'] }},{{ $colorData['dark'] }});
                                    margin:0 auto 4px;
                                    border:3px solid {{ $__currentColor === $colorKey ? '#fff' : 'transparent' }};
                                    box-shadow:{{ $__currentColor === $colorKey ? '0 0 0 2px ' . $colorData['bg'] . ', 0 2px 8px rgba(0,0,0,0.2)' : '0 2px 4px rgba(0,0,0,0.15)' }};
                                    transition:all 0.2s;">
                            </span>
                            <span style="font-size:11px;color:#64748b;">{{ $colorData['label'] }}</span>
                        </label>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                @php
                    $page_entries = [25 => 25, 50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000, -1 => __('lang_v1.all')];
                @endphp
                {!! Form::label('default_datatable_page_entries', __('lang_v1.default_datatable_page_entries')); !!}
                {!! Form::select('common_settings[default_datatable_page_entries]', $page_entries, !empty($common_settings['default_datatable_page_entries']) ? $common_settings['default_datatable_page_entries'] : 25 ,
                    ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'default_datatable_page_entries']); !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_tooltip', 1, $business->enable_tooltip ,
                    [ 'class' => 'input-icheck']); !!} {{ __( 'business.show_help_text' ) }}
                  </label>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var swatches = document.querySelectorAll('.theme-swatch-label input[type=radio]');
    swatches.forEach(function(radio) {
        radio.addEventListener('change', function() {
            // Reset all circles
            document.querySelectorAll('.theme-swatch-circle').forEach(function(circle) {
                circle.style.border = '3px solid transparent';
                circle.style.boxShadow = '0 2px 4px rgba(0,0,0,0.15)';
            });
            // Highlight selected
            var selected = this.closest('label').querySelector('.theme-swatch-circle');
            var bg = window.getComputedStyle(selected).backgroundImage;
            selected.style.border = '3px solid #fff';
            selected.style.boxShadow = '0 0 0 2px currentColor, 0 2px 8px rgba(0,0,0,0.2)';
        });
    });
})();
</script>