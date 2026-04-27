<div class="pos-tab-content">
    <h4>Login Screen Branding</h4>
    <p class="text-muted" style="margin-bottom:20px;">Customise what users see on the login page for this business.</p>

    @php $ls = $login_settings ?? []; @endphp

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Headline</label>
                <input type="text" name="login_settings[headline]" class="form-control"
                    value="{{ $ls['headline'] ?? '' }}"
                    placeholder="e.g. Pharmacy Management Made Simple.">
            </div>
            <div class="form-group">
                <label>Tagline</label>
                <textarea name="login_settings[tagline]" class="form-control" rows="3"
                    placeholder="Short description shown below the headline.">{{ $ls['tagline'] ?? '' }}</textarea>
            </div>
            <div class="form-group">
                <label>Feature Bullets <small class="text-muted">(one per line, max 6)</small></label>
                <textarea name="login_settings[bullets]" class="form-control" rows="6"
                    placeholder="💊 DDA Drug Compliance&#10;📋 Prescription Tracking&#10;📦 Real-time Inventory">{{ isset($ls['bullets']) ? implode("\n", $ls['bullets']) : '' }}</textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Primary / Accent Colour</label>
                <input type="color" name="login_settings[primary_color]" class="form-control" style="height:42px;"
                    value="{{ $ls['primary_color'] ?? '#0f766e' }}">
                <p class="help-block">Used for buttons and links on the login page.</p>
            </div>
            <div class="form-group">
                <label>Left Panel Background — Top Colour</label>
                <input type="color" name="login_settings[bg_from]" class="form-control" style="height:42px;"
                    value="{{ $ls['bg_from'] ?? '#0f4c5c' }}">
            </div>
            <div class="form-group">
                <label>Left Panel Background — Bottom Colour</label>
                <input type="color" name="login_settings[bg_to]" class="form-control" style="height:42px;"
                    value="{{ $ls['bg_to'] ?? '#0a7a62' }}">
            </div>
        </div>
    </div>

    {{-- Live preview --}}
    <div class="row" style="margin-top:10px;">
        <div class="col-md-12">
            <div id="login-preview" style="border-radius:12px; padding:24px; color:#fff; background: linear-gradient(135deg, {{ $ls['bg_from'] ?? '#0f4c5c' }}, {{ $ls['bg_to'] ?? '#0a7a62' }});">
                <strong id="preview-headline" style="font-size:1.2rem;">{{ $ls['headline'] ?? 'Your Headline' }}</strong>
                <p id="preview-tagline" style="margin:8px 0; opacity:.85; font-size:.9rem;">{{ $ls['tagline'] ?? 'Your tagline here.' }}</p>
            </div>
        </div>
    </div>
</div>

@section('javascript')
@parent
<script>
$(function() {
    $('[name="login_settings[headline]"]').on('input', function() {
        $('#preview-headline').text($(this).val() || 'Your Headline');
    });
    $('[name="login_settings[tagline]"]').on('input', function() {
        $('#preview-tagline').text($(this).val() || 'Your tagline here.');
    });
    function updateBg() {
        var from = $('[name="login_settings[bg_from]"]').val();
        var to   = $('[name="login_settings[bg_to]"]').val();
        $('#login-preview').css('background', 'linear-gradient(135deg, ' + from + ', ' + to + ')');
    }
    $('[name="login_settings[bg_from]"], [name="login_settings[bg_to]"]').on('input', updateBg);
});
</script>
@endsection
