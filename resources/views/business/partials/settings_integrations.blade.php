<div class="pos-tab-content">

    @php
    if (!function_exists('stgIntVal')) {
        function stgIntVal($common_settings, $key) {
            return !empty($common_settings[$key]) ? $common_settings[$key] : '';
        }
    }
    @endphp

    {{-- ── AI ── --}}
    <div class="row">
        <div class="col-sm-12">
            <h5 class="stg-int-heading"><i class="fas fa-robot"></i> Artificial Intelligence</h5>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>OpenAI API Key</label>
                <input type="password" name="common_settings[openai_api_key]" class="form-control"
                    placeholder="sk-..." autocomplete="off"
                    value="{{ stgIntVal($common_settings, 'openai_api_key') }}">
                <p class="help-block">GPT models &mdash; <a href="https://platform.openai.com" target="_blank">platform.openai.com</a></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Anthropic (Claude) API Key</label>
                <input type="password" name="common_settings[anthropic_api_key]" class="form-control"
                    placeholder="sk-ant-..." autocomplete="off"
                    value="{{ stgIntVal($common_settings, 'anthropic_api_key') }}">
                <p class="help-block">Claude models &mdash; <a href="https://console.anthropic.com" target="_blank">console.anthropic.com</a></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Google Gemini API Key</label>
                <input type="password" name="common_settings[gemini_api_key]" class="form-control"
                    placeholder="AIza..." autocomplete="off"
                    value="{{ stgIntVal($common_settings, 'gemini_api_key') }}">
                <p class="help-block">AI Analytics &amp; Hospital Intelligence &mdash; <a href="https://aistudio.google.com" target="_blank">aistudio.google.com</a></p>
            </div>
        </div>
    </div>

    <hr style="border-color:#e2e8f0;margin:8px 0 20px;">

    {{-- ── Payments ── --}}
    <div class="row">
        <div class="col-sm-12">
            <h5 class="stg-int-heading"><i class="fas fa-mobile-alt"></i> Payment Gateways</h5>
        </div>
        @php
            $mpesaConfigured = false;
            $mpesaUrl = '#';
            try {
                if (class_exists(\App\MpesaSetting::class)) {
                    $mpesaConfigured = \App\MpesaSetting::where('business_id', session('user.business_id'))->exists();
                }
                if (\Route::has('mpesa.settings')) { $mpesaUrl = route('mpesa.settings'); }
            } catch (\Exception $e) {}

            $kcbConfigured = false;
            $kcbUrl = '#';
            try {
                if (class_exists(\Modules\KcbBuni\Entities\KcbBuniSetting::class)) {
                    $kcbConfigured = \Modules\KcbBuni\Entities\KcbBuniSetting::where('business_id', session('user.business_id'))->exists();
                }
                if (\Route::has('kcb-buni.settings')) { $kcbUrl = route('kcb-buni.settings'); }
            } catch (\Exception $e) {}
        @endphp
        <div class="col-sm-6">
            <div class="stg-int-module-card">
                <div class="stg-int-module-icon"><i class="fas fa-money-bill-wave" style="color:#22c55e;"></i></div>
                <div class="stg-int-module-body">
                    <div class="stg-int-module-title">M-Pesa (Daraja)</div>
                    <div class="stg-int-module-desc">Consumer key, secret, passkey, shortcode &amp; till number</div>
                    <span class="stg-int-status {{ $mpesaConfigured ? 'stg-int-status--ok' : 'stg-int-status--none' }}">
                        {{ $mpesaConfigured ? 'Configured' : 'Not configured' }}
                    </span>
                </div>
                <a href="{{ $mpesaUrl }}" class="btn btn-default btn-xs stg-int-configure-btn">
                    <i class="fas fa-cog"></i> Configure
                </a>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="stg-int-module-card">
                <div class="stg-int-module-icon"><i class="fas fa-university" style="color:#0284c7;"></i></div>
                <div class="stg-int-module-body">
                    <div class="stg-int-module-title">KCB Buni</div>
                    <div class="stg-int-module-desc">App key, secret, shortcodes &amp; environment</div>
                    <span class="stg-int-status {{ $kcbConfigured ? 'stg-int-status--ok' : 'stg-int-status--none' }}">
                        {{ $kcbConfigured ? 'Configured' : 'Not configured' }}
                    </span>
                </div>
                <a href="{{ $kcbUrl }}" class="btn btn-default btn-xs stg-int-configure-btn">
                    <i class="fas fa-cog"></i> Configure
                </a>
            </div>
        </div>
    </div>

    <hr style="border-color:#e2e8f0;margin:8px 0 20px;">

    {{-- ── Maps ── --}}
    <div class="row">
        <div class="col-sm-12">
            <h5 class="stg-int-heading"><i class="fas fa-map-marker-alt"></i> Maps &amp; Location</h5>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Google Maps API Key</label>
                <input type="password" name="common_settings[google_map_api_key]" class="form-control"
                    placeholder="AIza..." autocomplete="off"
                    value="{{ stgIntVal($common_settings, 'google_map_api_key') }}">
                <p class="help-block">Location lookups &mdash; <a href="https://console.cloud.google.com" target="_blank">console.cloud.google.com</a></p>
            </div>
        </div>
    </div>

    <hr style="border-color:#e2e8f0;margin:8px 0 20px;">

    {{-- ── Real-time ── --}}
    <div class="row">
        <div class="col-sm-12">
            <h5 class="stg-int-heading"><i class="fas fa-bolt"></i> Real-time Notifications (Pusher)</h5>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>App ID</label>
                <input type="text" name="common_settings[pusher_app_id]" class="form-control"
                    autocomplete="off" value="{{ stgIntVal($common_settings, 'pusher_app_id') }}">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>App Key</label>
                <input type="text" name="common_settings[pusher_app_key]" class="form-control"
                    autocomplete="off" value="{{ stgIntVal($common_settings, 'pusher_app_key') }}">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>App Secret</label>
                <input type="password" name="common_settings[pusher_app_secret]" class="form-control"
                    autocomplete="off" value="{{ stgIntVal($common_settings, 'pusher_app_secret') }}">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>Cluster</label>
                <input type="text" name="common_settings[pusher_app_cluster]" class="form-control"
                    placeholder="mt1" autocomplete="off"
                    value="{{ stgIntVal($common_settings, 'pusher_app_cluster') }}">
            </div>
        </div>
    </div>

    <hr style="border-color:#e2e8f0;margin:8px 0 20px;">

    {{-- ── Security ── --}}
    <div class="row">
        <div class="col-sm-12">
            <h5 class="stg-int-heading"><i class="fas fa-shield-alt"></i> Security (reCAPTCHA)</h5>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>reCAPTCHA Site Key</label>
                <input type="text" name="common_settings[recaptcha_key]" class="form-control"
                    autocomplete="off" value="{{ stgIntVal($common_settings, 'recaptcha_key') }}">
                <p class="help-block"><a href="https://www.google.com/recaptcha/admin" target="_blank">google.com/recaptcha</a></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>reCAPTCHA Secret Key</label>
                <input type="password" name="common_settings[recaptcha_secret]" class="form-control"
                    autocomplete="off" value="{{ stgIntVal($common_settings, 'recaptcha_secret') }}">
            </div>
        </div>
    </div>

    <hr style="border-color:#e2e8f0;margin:8px 0 20px;">

    {{-- ── Storage & Backup ── --}}
    <div class="row">
        <div class="col-sm-12">
            <h5 class="stg-int-heading"><i class="fas fa-cloud-upload-alt"></i> Storage &amp; Backup</h5>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Dropbox Access Token</label>
                <input type="password" name="common_settings[dropbox_token]" class="form-control"
                    autocomplete="off" value="{{ stgIntVal($common_settings, 'dropbox_token') }}">
                <p class="help-block"><a href="https://www.dropbox.com/developers" target="_blank">dropbox.com/developers</a></p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <label>AWS Access Key ID</label>
                <input type="text" name="common_settings[aws_access_key_id]" class="form-control"
                    autocomplete="off" value="{{ stgIntVal($common_settings, 'aws_access_key_id') }}">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>AWS Secret Key</label>
                <input type="password" name="common_settings[aws_secret_key]" class="form-control"
                    autocomplete="off" value="{{ stgIntVal($common_settings, 'aws_secret_key') }}">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>AWS Region</label>
                <input type="text" name="common_settings[aws_region]" class="form-control"
                    placeholder="us-east-1" autocomplete="off"
                    value="{{ stgIntVal($common_settings, 'aws_region') }}">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>AWS Bucket</label>
                <input type="text" name="common_settings[aws_bucket]" class="form-control"
                    autocomplete="off" value="{{ stgIntVal($common_settings, 'aws_bucket') }}">
            </div>
        </div>
    </div>

</div>

<style>
.stg-int-heading {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748b;
    margin: 0 0 14px;
    display: flex;
    align-items: center;
    gap: 7px;
}
.stg-int-heading i {
    color: var(--theme-main, #4f46e5);
    font-size: 13px;
}
.stg-int-module-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 14px;
}
.stg-int-module-icon {
    font-size: 22px;
    flex-shrink: 0;
    width: 36px;
    text-align: center;
}
.stg-int-module-body {
    flex: 1;
    min-width: 0;
}
.stg-int-module-title {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
}
.stg-int-module-desc {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 2px;
}
.stg-int-status {
    display: inline-block;
    font-size: 10px;
    font-weight: 700;
    border-radius: 20px;
    padding: 2px 8px;
    margin-top: 4px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.stg-int-status--ok   { background: #dcfce7; color: #16a34a; }
.stg-int-status--none { background: #fef9c3; color: #854d0e; }
.stg-int-configure-btn {
    flex-shrink: 0;
}
</style>
