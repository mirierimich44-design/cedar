@extends('layouts.app')
@section('title', 'Sign Agreement #' . $agreement->id)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">Sign Agreement #{{ $agreement->id }}</h1>
</section>

<section class="content">
    <div class="alert alert-info">
        <strong>{{ $agreement->getSignedCount() }}/4 signatures captured.</strong>
        {{ $agreement->isFullySigned() ? ' All signatures complete — agreement is active.' : ' Complete remaining signatures below.' }}
    </div>

    <div class="row">
        @foreach([
            ['type' => 'company_legal', 'label' => 'Company Legal Team',  'path' => $agreement->company_signature_path,     'name' => $agreement->company_signatory_name],
            ['type' => 'rsm_tsm',       'label' => 'RSM / TSM',           'path' => $agreement->rsm_tsm_signature_path,      'name' => $agreement->rsm_tsm_signatory_name],
            ['type' => 'dealer',        'label' => 'Dealer',              'path' => $agreement->dealer_signature_path,       'name' => $agreement->dealer_signatory_name],
            ['type' => 'distributor',   'label' => 'Distributor/Stockist','path' => $agreement->distributor_signature_path,  'name' => $agreement->distributor_signatory_name],
        ] as $sig)
        <div class="col-md-6 tw-mb-4">
            <div class="box {{ $sig['path'] ? 'box-success' : 'box-primary' }}">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $sig['label'] }}
                        @if($sig['path'])<span class="label label-success tw-ml-2">Signed</span>@endif
                    </h3>
                </div>
                <div class="box-body">
                    @if($sig['path'] && file_exists(public_path($sig['path'])))
                        <img src="{{ asset($sig['path']) }}" class="tw-max-h-20 tw-border tw-rounded tw-bg-white tw-p-1">
                        <p class="tw-text-sm tw-mt-1 tw-text-gray-600">{{ $sig['name'] }}</p>
                        @can('cooler.agreement.sign')
                        <button type="button" class="btn btn-xs btn-warning btn-redo-sig" data-type="{{ $sig['type'] }}" data-label="{{ $sig['label'] }}">Re-sign</button>
                        @endcan
                    @else
                    @can('cooler.agreement.sign')
                    <div class="sig-capture-area" data-type="{{ $sig['type'] }}">
                        <div class="form-group">
                            <label>Signatory Name</label>
                            <input type="text" class="form-control sig-name" placeholder="Full name of signatory">
                        </div>
                        <div class="tw-mb-2">
                            <div class="tw-flex tw-gap-2 tw-mb-1">
                                <button type="button" class="btn btn-xs btn-default btn-tab-draw active">Draw</button>
                                <button type="button" class="btn btn-xs btn-default btn-tab-upload">Upload Image</button>
                            </div>

                            {{-- Draw tab --}}
                            <div class="sig-draw-tab">
                                <canvas class="sig-canvas tw-border tw-border-gray-300 tw-rounded tw-bg-white tw-w-full" width="360" height="120" style="touch-action:none;"></canvas>
                                <div class="tw-flex tw-gap-2 tw-mt-1">
                                    <button type="button" class="btn btn-xs btn-default btn-clear-sig">Clear</button>
                                </div>
                            </div>

                            {{-- Upload tab --}}
                            <div class="sig-upload-tab tw-hidden">
                                <input type="file" class="sig-file-input form-control-file" accept=".png,.jpg,.jpeg">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-save-sig" data-agreement="{{ $agreement->id }}" data-type="{{ $sig['type'] }}">
                            <i class="fa fa-save"></i> Save Signature
                        </button>
                    </div>
                    @endcan
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="tw-flex tw-gap-2 tw-mt-4">
        <a href="{{ route('cooler.agreements.show', $agreement->id) }}" class="btn btn-default">← Back to Agreement</a>
        @if($agreement->isFullySigned())
        <a href="{{ route('cooler.agreements.pdf', $agreement->id) }}" class="btn btn-success" target="_blank"><i class="fas fa-file-pdf"></i> Download PDF</a>
        @endif
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function () {
    // Tab switching
    $(document).on('click', '.btn-tab-draw', function () {
        var box = $(this).closest('.sig-capture-area');
        box.find('.sig-draw-tab').removeClass('tw-hidden');
        box.find('.sig-upload-tab').addClass('tw-hidden');
        $(this).addClass('active');
        box.find('.btn-tab-upload').removeClass('active');
    });
    $(document).on('click', '.btn-tab-upload', function () {
        var box = $(this).closest('.sig-capture-area');
        box.find('.sig-upload-tab').removeClass('tw-hidden');
        box.find('.sig-draw-tab').addClass('tw-hidden');
        $(this).addClass('active');
        box.find('.btn-tab-draw').removeClass('active');
    });

    // Signature pad drawing
    $(document).on('mousedown touchstart', '.sig-canvas', function (e) {
        var canvas = this;
        var ctx = canvas.getContext('2d');
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#000';
        ctx.lineCap = 'round';
        var drawing = true;

        function getPos(event) {
            var rect = canvas.getBoundingClientRect();
            var src = event.touches ? event.touches[0] : event;
            return { x: (src.clientX - rect.left) * (canvas.width / rect.width), y: (src.clientY - rect.top) * (canvas.height / rect.height) };
        }

        ctx.beginPath();
        var pos = getPos(e.originalEvent);
        ctx.moveTo(pos.x, pos.y);

        $(canvas).on('mousemove touchmove', function (ev) {
            ev.preventDefault();
            if (!drawing) return;
            pos = getPos(ev.originalEvent);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        });

        $(document).one('mouseup touchend', function () {
            drawing = false;
            $(canvas).off('mousemove touchmove');
        });
    });

    // Clear
    $(document).on('click', '.btn-clear-sig', function () {
        var canvas = $(this).closest('.sig-capture-area').find('.sig-canvas')[0];
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    });

    // Save signature
    $(document).on('click', '.btn-save-sig', function () {
        var area          = $(this).closest('.sig-capture-area');
        var type          = $(this).data('type');
        var agreementId   = $(this).data('agreement');
        var sigName       = area.find('.sig-name').val().trim();
        var isUploadMode  = !area.find('.sig-upload-tab').hasClass('tw-hidden');

        if (!sigName) { alert('Please enter the signatory name.'); return; }

        if (isUploadMode) {
            var file = area.find('.sig-file-input')[0].files[0];
            if (!file) { alert('Please select a signature image.'); return; }
            var reader = new FileReader();
            reader.onload = function (e) { submitSignature(agreementId, type, sigName, e.target.result); };
            reader.readAsDataURL(file);
        } else {
            var canvas = area.find('.sig-canvas')[0];
            submitSignature(agreementId, type, sigName, canvas.toDataURL('image/png'));
        }
    });

    function submitSignature(agreementId, type, name, dataUrl) {
        $.ajax({
            url: '/cooler/agreements/' + agreementId + '/sign',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', signatory_type: type, signature_data: dataUrl, signatory_name: name },
            success: function (res) {
                if (res.success) {
                    location.reload();
                }
            },
            error: function (xhr) {
                alert('Error saving signature. Please try again.');
            }
        });
    }
});
</script>
@endsection
