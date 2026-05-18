{{--
  Reusable form field partial for the contact modal.
  Props:
    $label       - field label string
    $field       - rendered form field HTML (pass as Form:: helper result)
    $icon        - (optional) FontAwesome class e.g. 'fa-user'
    $prefix_text - (optional) text prefix e.g. 'KES'
--}}
<div class="contact-form-field" style="margin-bottom:8px;">
  <label style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; display:block;">{{ $label }}</label>
  <div style="position:relative; display:flex; align-items:center;">
    @if(!empty($icon))
      <span style="position:absolute; left:10px; z-index:5; color:#94a3b8; font-size:13px; pointer-events:none;"><i class="fa {{ $icon }}"></i></span>
      <div class="has-prefix" style="width:100%;">{!! $field !!}</div>
    @elseif(!empty($prefix_text))
      <span style="position:absolute; left:10px; z-index:5; color:#64748b; font-size:12px; font-weight:600; pointer-events:none;">{{ $prefix_text }}</span>
      <div class="has-prefix" style="width:100%;">{!! $field !!}</div>
    @else
      <div style="width:100%;">{!! $field !!}</div>
    @endif
  </div>
</div>
