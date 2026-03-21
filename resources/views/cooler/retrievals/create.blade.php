@extends('layouts.app')
@section('title', 'Initiate Retrieval')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">Initiate Cooler Retrieval</h1>
</section>

<section class="content">
    <div class="box box-danger">
        <div class="box-header with-border"><h3 class="box-title">Retrieval Details</h3></div>
        <div class="box-body">
            {!! Form::open(['route' => 'cooler.retrievals.store', 'method' => 'POST']) !!}
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="tw-list-disc tw-pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('dealer_id', 'Dealer *') !!}
                        {!! Form::select('dealer_id', $dealers, old('dealer_id'), ['class' => 'form-control select2', 'required', 'id' => 'dealer-select', 'placeholder' => '— Select dealer —']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('cooler_id', 'Cooler to Retrieve *') !!}
                        {!! Form::select('cooler_id', [], old('cooler_id'), ['class' => 'form-control select2', 'required', 'id' => 'cooler-select', 'placeholder' => '— Select customer first —']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('retrieval_date', 'Retrieval Date *') !!}
                        {!! Form::date('retrieval_date', now()->toDateString(), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('reason', 'Reason *') !!}
                        {!! Form::select('reason', $reasons, old('reason'), ['class' => 'form-control select2', 'required', 'placeholder' => '— Select reason —']) !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('reason_notes', 'Additional Notes') !!}
                        {!! Form::textarea('reason_notes', old('reason_notes'), ['class' => 'form-control', 'rows' => 2]) !!}
                    </div>
                </div>
            </div>

            <h4 class="tw-font-semibold tw-mt-2 tw-mb-3">Authorized Staff Details</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('authorized_staff_name', 'Staff Name *') !!}
                        {!! Form::text('authorized_staff_name', old('authorized_staff_name', $user->name), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('authorized_staff_id_no', 'Staff ID Number') !!}
                        {!! Form::text('authorized_staff_id_no', old('authorized_staff_id_no'), ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('authorized_staff_tel', 'Staff Phone') !!}
                        {!! Form::text('authorized_staff_tel', old('authorized_staff_tel'), ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <input type="hidden" name="agreement_id" id="agreement-id-hidden">

            <div class="tw-flex tw-gap-2 tw-mt-4">
                <button type="submit" class="btn btn-danger"><i class="fa fa-truck"></i> Initiate Retrieval</button>
                <a href="{{ route('cooler.retrievals.index') }}" class="btn btn-default">Cancel</a>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function () {
    $('#dealer-select').on('change', function () {
        var dealerId = $(this).val();
        if (!dealerId) return;

        $.get('/cooler/dealer/' + dealerId + '/coolers', function (data) {
            var select = $('#cooler-select');
            select.empty().append('<option value="">— Select cooler —</option>');
            $.each(data.coolers, function (i, c) {
                select.append('<option value="' + c.id + '">' + c.asset_number + ' — ' + c.asset_type + ' (' + c.serial_number + ')</option>');
            });
            select.trigger('change.select2');
            $('#agreement-id-hidden').val(data.agreement_id || '');
        });
    });
});
</script>
@endsection
