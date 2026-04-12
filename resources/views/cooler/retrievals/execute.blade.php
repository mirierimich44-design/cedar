@extends('layouts.app')
@section('title', 'Execute Retrieval #' . $retrieval->id)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">
        Field Execution — Retrieval #{{ $retrieval->id }}
    </h1>
    <p class="tw-text-gray-600">{{ $retrieval->dealer->outlet_name }} — {{ $retrieval->cooler->asset_number }}</p>
</section>

<section class="content">
    {{-- Progress checklist --}}
    <div class="box box-default">
        <div class="box-body">
            <div class="tw-flex tw-flex-wrap tw-gap-3">
                @php
                    $beforeCount  = $retrieval->photosBefore()->count();
                    $afterCount   = $retrieval->photosAfter()->count();
                    $hasSig       = !empty($retrieval->customer_signature_path);
                @endphp
                <div class="tw-flex tw-items-center tw-gap-1">
                    <span class="tw-w-5 tw-h-5 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-xs {{ $beforeCount > 0 ? 'tw-bg-green-500 tw-text-white' : 'tw-bg-gray-200' }}">
                        {{ $beforeCount > 0 ? '✓' : '1' }}
                    </span>
                    <span class="tw-text-sm">Before Photos</span>
                </div>
                <div class="tw-flex tw-items-center tw-gap-1">
                    <span class="tw-w-5 tw-h-5 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-xs {{ $hasSig ? 'tw-bg-green-500 tw-text-white' : 'tw-bg-gray-200' }}">
                        {{ $hasSig ? '✓' : '2' }}
                    </span>
                    <span class="tw-text-sm">Customer Signature</span>
                </div>
                <div class="tw-flex tw-items-center tw-gap-1">
                    <span class="tw-w-5 tw-h-5 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-xs {{ $afterCount > 0 ? 'tw-bg-green-500 tw-text-white' : 'tw-bg-gray-200' }}">
                        {{ $afterCount > 0 ? '✓' : '3' }}
                    </span>
                    <span class="tw-text-sm">After Photos</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Before photos --}}
        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Before Retrieval Photos <span class="label label-warning">Required</span></h3>
                </div>
                <div class="box-body">
                    @include('cooler.retrievals._photo_uploader', ['phase' => 'retrieval_photo_before', 'label' => 'Before Photos', 'retrieval' => $retrieval])
                    <div id="gallery-before" class="tw-flex tw-flex-wrap tw-gap-2 tw-mt-2">
                        @foreach($retrieval->photosBefore()->get() as $photo)
                            <img src="{{ $photo->thumbnail_url }}" class="tw-w-20 tw-h-20 tw-object-cover tw-rounded tw-border">
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- During photos --}}
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">During Retrieval Photos <small>(optional)</small></h3>
                </div>
                <div class="box-body">
                    @include('cooler.retrievals._photo_uploader', ['phase' => 'retrieval_photo_during', 'label' => 'During Photos', 'retrieval' => $retrieval])
                    <div id="gallery-during" class="tw-flex tw-flex-wrap tw-gap-2 tw-mt-2">
                        @foreach($retrieval->photosDuring()->get() as $photo)
                            <img src="{{ $photo->thumbnail_url }}" class="tw-w-20 tw-h-20 tw-object-cover tw-rounded tw-border">
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer signature --}}
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title">Customer Acknowledgement Signature</h3></div>
        <div class="box-body">
            @if($retrieval->customer_signature_path && file_exists(public_path($retrieval->customer_signature_path)))
            <div class="tw-flex tw-items-center tw-gap-4">
                <img src="{{ asset($retrieval->customer_signature_path) }}" class="tw-max-h-20 tw-border tw-rounded tw-bg-white tw-p-2">
                <span class="label label-success">Captured {{ $retrieval->acknowledgement_date?->format('d M Y H:i') }}</span>
            </div>
            @else
            <div class="row">
                <div class="col-md-8">
                    <div class="tw-mb-2 tw-flex tw-gap-2">
                        <button class="btn btn-xs btn-default btn-sig-tab-draw-cust active">Draw</button>
                        <button class="btn btn-xs btn-default btn-sig-tab-upload-cust">Upload</button>
                    </div>
                    <div id="cust-draw-area">
                        <canvas id="customer-sig-canvas" class="tw-border tw-border-gray-300 tw-rounded tw-bg-white tw-w-full" width="500" height="150" style="touch-action:none;"></canvas>
                        <button type="button" id="clear-cust-sig" class="btn btn-xs btn-default tw-mt-1">Clear</button>
                    </div>
                    <div id="cust-upload-area" class="tw-hidden">
                        <input type="file" id="cust-sig-file" class="form-control-file" accept=".png,.jpg,.jpeg">
                    </div>
                    <div class="tw-mt-2 tw-flex tw-gap-2">
                        <input type="text" id="cust-gps" class="form-control form-control-sm" placeholder="GPS coordinates (auto)" readonly style="max-width:250px;">
                        <button type="button" class="btn btn-xs btn-default" id="get-gps"><i class="fa fa-map-marker"></i></button>
                    </div>
                    <button type="button" id="save-cust-sig" class="btn btn-primary tw-mt-3">
                        <i class="fa fa-save"></i> Save Customer Signature
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- After photos --}}
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">After Retrieval Photos <span class="label label-warning">Required</span></h3>
        </div>
        <div class="box-body">
            @include('cooler.retrievals._photo_uploader', ['phase' => 'retrieval_photo_after', 'label' => 'After Photos', 'retrieval' => $retrieval])
            <div id="gallery-after" class="tw-flex tw-flex-wrap tw-gap-2 tw-mt-2">
                @foreach($retrieval->photosAfter()->get() as $photo)
                    <img src="{{ $photo->thumbnail_url }}" class="tw-w-20 tw-h-20 tw-object-cover tw-rounded tw-border">
                @endforeach
            </div>
        </div>
    </div>

    {{-- Signed letter upload --}}
    <div class="box box-default">
        <div class="box-header with-border"><h3 class="box-title">Upload Signed Retrieval Letter</h3></div>
        <div class="box-body">
            <div class="tw-flex tw-gap-4 tw-items-start">
                <div>
                    <a href="{{ route('cooler.retrievals.letter', $retrieval->id) }}" class="btn btn-default" target="_blank">
                        <i class="fas fa-file-pdf"></i> Download Retrieval Letter
                    </a>
                    <p class="tw-text-xs tw-text-gray-500 tw-mt-1">Print, get signed, then upload below</p>
                </div>
                <div>
                    <input type="file" id="signed-letter-file" accept=".pdf,.jpg,.jpeg,.png" class="form-control-file">
                    <button type="button" id="upload-signed-letter" class="btn btn-primary tw-mt-1">Upload Signed Letter</button>
                    @if($retrieval->retrieval_letter_path)
                    <span class="label label-success tw-ml-2"><i class="fa fa-check"></i> Uploaded</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Complete --}}
    <div class="box box-danger">
        <div class="box-body tw-flex tw-justify-between tw-items-center">
            <div>
                <strong>Mark Retrieval as Complete</strong>
                <p class="tw-text-sm tw-text-gray-600 tw-mb-0">This will mark the cooler as "Retrieved" and terminate the agreement.</p>
            </div>
            <button type="button" id="btn-complete-retrieval" class="btn btn-danger btn-lg">
                <i class="fa fa-check-circle"></i> Complete Retrieval
            </button>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
var retrievalId = {{ $retrieval->id }};

$(function () {
    // GPS auto-capture
    $('#get-gps').on('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (pos) {
                $('#cust-gps').val(pos.coords.latitude + ',' + pos.coords.longitude);
            });
        }
    });

    // Try auto-get GPS on load
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (pos) {
            $('#cust-gps').val(pos.coords.latitude + ',' + pos.coords.longitude);
        }, function(){});
    }

    // Signature pad
    var canvas = document.getElementById('customer-sig-canvas');
    if (canvas) {
        var ctx = canvas.getContext('2d');
        ctx.lineWidth = 2; ctx.strokeStyle = '#000'; ctx.lineCap = 'round';
        var drawing = false;

        function getPos(e) {
            var r = canvas.getBoundingClientRect();
            var s = e.touches ? e.touches[0] : e;
            return { x: (s.clientX - r.left) * (canvas.width / r.width), y: (s.clientY - r.top) * (canvas.height / r.height) };
        }

        canvas.addEventListener('mousedown', function (e) { drawing = true; ctx.beginPath(); var p = getPos(e); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('mousemove', function (e) { if (!drawing) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('mouseup', function () { drawing = false; });
        canvas.addEventListener('touchstart', function (e) { e.preventDefault(); drawing = true; ctx.beginPath(); var p = getPos(e); ctx.moveTo(p.x, p.y); }, {passive:false});
        canvas.addEventListener('touchmove',  function (e) { e.preventDefault(); if (!drawing) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); ctx.beginPath(); ctx.moveTo(p.x, p.y); }, {passive:false});
        canvas.addEventListener('touchend',   function () { drawing = false; });

        $('#clear-cust-sig').on('click', function () { ctx.clearRect(0, 0, canvas.width, canvas.height); });
    }

    $('.btn-sig-tab-draw-cust').on('click', function () {
        $('#cust-draw-area').show(); $('#cust-upload-area').addClass('tw-hidden');
        $(this).addClass('active'); $('.btn-sig-tab-upload-cust').removeClass('active');
    });
    $('.btn-sig-tab-upload-cust').on('click', function () {
        $('#cust-upload-area').removeClass('tw-hidden'); $('#cust-draw-area').hide();
        $(this).addClass('active'); $('.btn-sig-tab-draw-cust').removeClass('active');
    });

    $('#save-cust-sig').on('click', function () {
        var isUpload = !$('#cust-upload-area').hasClass('tw-hidden');
        var gps = $('#cust-gps').val();

        if (isUpload) {
            var file = $('#cust-sig-file')[0].files[0];
            if (!file) { alert('Please select a signature image.'); return; }
            var reader = new FileReader();
            reader.onload = function (e) { postSignature(e.target.result, gps); };
            reader.readAsDataURL(file);
        } else {
            postSignature(canvas.toDataURL('image/png'), gps);
        }
    });

    function postSignature(dataUrl, gps) {
        $.post('/cooler/retrievals/' + retrievalId + '/capture-signature', {
            _token: '{{ csrf_token() }}', signature_data: dataUrl, gps_coordinates: gps
        }, function (res) {
            if (res.success) location.reload();
        });
    }

    // Upload signed letter
    $('#upload-signed-letter').on('click', function () {
        var file = $('#signed-letter-file')[0].files[0];
        if (!file) { alert('Please select a file.'); return; }
        var form = new FormData();
        form.append('_token', '{{ csrf_token() }}');
        form.append('signed_letter', file);
        $.ajax({ url: '/cooler/retrievals/' + retrievalId + '/upload-letter', type: 'POST', data: form, processData: false, contentType: false,
            success: function () { location.reload(); }
        });
    });

    // Complete
    $('#btn-complete-retrieval').on('click', function () {
        if (!confirm('Mark this retrieval as complete? This will:\n- Mark cooler as Retrieved\n- Terminate the active agreement\n\nThis cannot be undone.')) return;
        $.post('/cooler/retrievals/' + retrievalId + '/complete', { _token: '{{ csrf_token() }}' }, function (res) {
            if (res.success) window.location = res.redirect;
        });
    });
});

// Photo upload helpers for each phase
function uploadPhotos(phase, files, galleryId) {
    var form = new FormData();
    form.append('_token', '{{ csrf_token() }}');
    form.append('photo_type', phase);
    $.each(files, function (i, f) { form.append('photos[]', f); });

    $.ajax({
        url: '/cooler/retrievals/' + retrievalId + '/upload-photo',
        type: 'POST', data: form, processData: false, contentType: false,
        success: function (res) {
            $.each(res.uploaded, function (i, p) {
                $('#' + galleryId).append('<img src="' + p.thumbnail_url + '" class="tw-w-20 tw-h-20 tw-object-cover tw-rounded tw-border">');
            });
        }
    });
}
</script>
@endsection
