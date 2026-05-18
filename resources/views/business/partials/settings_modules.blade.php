<div class="pos-tab-content">
	<div class="row">
	@if(!empty($modules))
		<h4>@lang('lang_v1.enable_disable_modules')</h4>
		@foreach($modules as $k => $v)
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br>
                      <label>
                        {!! Form::checkbox('enabled_modules[]', $k,  in_array($k, $enabled_modules) ,
                        ['class' => 'input-icheck']); !!} {{$v['name']}}
                      </label>
                      @if(!empty($v['tooltip'])) @show_tooltip($v['tooltip']) @endif
                    </div>
                </div>
            </div>
        @endforeach
	@endif
	</div>

    {{-- ── Security Features ─────────────────────────────────────────── --}}
    {{-- NOTE: Cannot use a nested <form> here — this partial is inside the main settings form.
         We use fetch() via JS to POST to the toggle route instead. --}}
    @php $ipEnabled = isset($ip_restriction_enabled) && $ip_restriction_enabled; @endphp
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-12">
            <h4 style="border-top:1px solid #e2e8f0;padding-top:16px;">
                <i class="fa fa-shield" style="color:#6b7280;margin-right:6px;"></i> Security Features
            </h4>
        </div>
        <div class="col-sm-6">
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                    <strong style="font-size:13px;">
                        <i class="fa fa-shield" style="color:#6b7280;margin-right:5px;"></i> IP Access Control
                    </strong>
                    <div id="ip-module-status" style="font-size:11px;color:#6b7280;margin-top:3px;">
                        {{ $ipEnabled ? 'Currently enabled — menu item is visible.' : 'Currently disabled — menu item is hidden.' }}
                    </div>
                </div>
                <button type="button"
                        id="ip-module-toggle-btn"
                        data-enabled="{{ $ipEnabled ? '1' : '0' }}"
                        data-url="{{ route('ip-access.toggle-module') }}"
                        data-token="{{ csrf_token() }}"
                        class="btn btn-sm {{ $ipEnabled ? 'btn-danger' : 'btn-success' }}"
                        onclick="toggleIpModule(this)">
                    <i class="fa fa-{{ $ipEnabled ? 'toggle-off' : 'toggle-on' }}"></i>
                    <span>{{ $ipEnabled ? 'Disable' : 'Enable' }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleIpModule(btn) {
    var enabled = btn.dataset.enabled === '1';
    var action  = enabled ? 'Disable' : 'Enable';
    if (!confirm(action + ' IP Access Control module?')) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving…';

    fetch(btn.dataset.url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': btn.dataset.token,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ enable: enabled ? 0 : 1 })
    })
    .then(function(res) {
        if (res.ok || res.redirected) {
            // Flip the UI
            var nowEnabled = !enabled;
            btn.dataset.enabled = nowEnabled ? '1' : '0';
            btn.className = 'btn btn-sm ' + (nowEnabled ? 'btn-danger' : 'btn-success');
            btn.innerHTML = '<i class="fa fa-' + (nowEnabled ? 'toggle-off' : 'toggle-on') + '"></i> <span>' + (nowEnabled ? 'Disable' : 'Enable') + '</span>';
            document.getElementById('ip-module-status').textContent = nowEnabled
                ? 'Currently enabled — menu item is visible.'
                : 'Currently disabled — menu item is hidden.';
            btn.disabled = false;

            // Show a brief success notice
            var notice = document.createElement('div');
            notice.className = 'alert alert-success alert-dismissible';
            notice.style = 'margin-top:10px;';
            notice.innerHTML = '<button type="button" class="close" data-dismiss="alert">&times;</button>'
                + 'IP Access Control module ' + (nowEnabled ? 'enabled' : 'disabled') + ' successfully.';
            btn.closest('.row').after(notice);
        } else {
            alert('Failed to toggle. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-' + (enabled ? 'toggle-off' : 'toggle-on') + '"></i> <span>' + action + '</span>';
        }
    })
    .catch(function() {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-' + (enabled ? 'toggle-off' : 'toggle-on') + '"></i> <span>' + action + '</span>';
    });
}
</script>