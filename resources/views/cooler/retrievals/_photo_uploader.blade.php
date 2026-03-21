{{--
  Reusable photo uploader for retrieval phases.
  Variables: $phase (retrieval_photo_before|during|after), $label, $retrieval
--}}
@php
    $galleryId = 'gallery-' . str_replace('retrieval_photo_', '', $phase);
@endphp

<div class="upload-zone tw-border-2 tw-border-dashed tw-border-gray-300 tw-rounded-lg tw-p-4 tw-text-center tw-cursor-pointer hover:tw-border-blue-400 tw-transition-colors tw-mb-2"
     onclick="document.getElementById('photo-input-{{ $phase }}').click()">
    <i class="fa fa-camera tw-text-2xl tw-text-gray-400"></i>
    <p class="tw-text-sm tw-text-gray-500 tw-mt-1">Tap to capture or upload photos</p>
    <p class="tw-text-xs tw-text-gray-400">JPG, PNG, HEIC — max 10MB each — up to 10 photos</p>
</div>
<input type="file" id="photo-input-{{ $phase }}" accept="image/jpeg,image/png,image/heic"
       multiple capture="environment" class="tw-hidden"
       onchange="uploadPhotos('{{ $phase }}', this.files, '{{ $galleryId }}')">
