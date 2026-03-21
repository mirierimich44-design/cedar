{{--
  Reusable drag-drop upload field.
  Variables: $field, $label, $accept, $hint, $required (bool, optional)
--}}
<div class="form-group {{ $errors->has($field) ? 'has-error' : '' }}">
    <label for="{{ $field }}">{{ $label }}</label>
    <div class="upload-zone tw-border-2 tw-border-dashed tw-border-gray-300 tw-rounded-lg tw-p-4 tw-text-center tw-cursor-pointer hover:tw-border-blue-400 tw-transition-colors"
         onclick="document.getElementById('{{ $field }}').click()">
        <i class="fa fa-cloud-upload tw-text-2xl tw-text-gray-400"></i>
        <p class="tw-text-sm tw-text-gray-500 tw-mt-1">Drag &amp; drop or click to upload</p>
        <p class="upload-preview tw-text-xs tw-mt-1 tw-text-gray-400">{{ $hint }}</p>
    </div>
    <input type="file" id="{{ $field }}" name="{{ $field }}" accept="{{ $accept }}"
           class="doc-upload-input tw-hidden" {{ ($required ?? false) ? 'required' : '' }}>
    @error($field)
        <span class="help-block">{{ $message }}</span>
    @enderror
</div>

<script>
(function () {
    var zone = document.querySelector('[onclick="document.getElementById(\'{{ $field }}\').click()"]');
    if (!zone) return;
    zone.addEventListener('dragover', function (e) { e.preventDefault(); zone.classList.add('tw-border-blue-500'); });
    zone.addEventListener('dragleave', function ()  { zone.classList.remove('tw-border-blue-500'); });
    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('tw-border-blue-500');
        var input = document.getElementById('{{ $field }}');
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
    });
})();
</script>
