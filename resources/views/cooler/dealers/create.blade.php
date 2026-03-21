@extends('layouts.app')
@section('title', 'Register Dealer')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">Register Dealer</h1>
</section>

<section class="content">
    {!! Form::open(['route' => 'cooler.dealers.store', 'method' => 'POST', 'files' => true, 'id' => 'dealer-form']) !!}

    {{-- Step indicator --}}
    <div class="tw-flex tw-gap-2 tw-mb-6">
        @foreach(['Personal Details', 'Business Details', 'Documents'] as $i => $step)
        <div class="tw-flex tw-items-center tw-gap-2">
            <div class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-sm tw-font-bold step-circle" data-step="{{ $i+1 }}">{{ $i+1 }}</div>
            <span class="tw-text-sm">{{ $step }}</span>
            @if(!$loop->last)<div class="tw-flex-1 tw-h-px tw-bg-gray-300 tw-mx-2 tw-w-12"></div>@endif
        </div>
        @endforeach
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="tw-list-disc tw-pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Step 1: Personal --}}
    <div class="dealer-step" data-step="1">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Step 1 — Personal Details</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            {!! Form::label('name', 'Full Name *') !!}
                            {!! Form::text('name', old('name'), ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('id_number') ? 'has-error' : '' }}">
                            {!! Form::label('id_number', 'ID Number *') !!}
                            {!! Form::text('id_number', old('id_number'), ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('kra_pin') ? 'has-error' : '' }}">
                            {!! Form::label('kra_pin', 'KRA PIN *') !!}
                            {!! Form::text('kra_pin', old('kra_pin'), ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                            {!! Form::label('phone', 'Phone Number *') !!}
                            {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => '0712345678', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('postal_address', 'Postal Address') !!}
                            {!! Form::text('postal_address', old('postal_address'), ['class' => 'form-control', 'placeholder' => 'P.O. Box 1234, Nairobi']) !!}
                        </div>
                    </div>
                </div>

                {{-- Required document uploads --}}
                <h4 class="tw-font-semibold tw-mt-4 tw-mb-3">Required Documents</h4>
                <div class="row">
                    <div class="col-md-4">
                        @include('cooler.dealers._upload_field', ['field' => 'id_copy', 'label' => 'ID Copy *', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB', 'required' => true])
                    </div>
                    <div class="col-md-4">
                        @include('cooler.dealers._upload_field', ['field' => 'kra_pin_certificate', 'label' => 'KRA PIN Certificate *', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB', 'required' => true])
                    </div>
                    <div class="col-md-4">
                        @include('cooler.dealers._upload_field', ['field' => 'passport_photo', 'label' => 'Passport Photo *', 'accept' => '.jpg,.jpeg,.png', 'hint' => 'Image only, max 2MB, min 300×300px', 'required' => true])
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="button" class="btn btn-primary btn-next-step">Next: Business Details →</button>
            </div>
        </div>
    </div>

    {{-- Step 2: Business --}}
    <div class="dealer-step tw-hidden" data-step="2">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Step 2 — Business Details</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('outlet_name') ? 'has-error' : '' }}">
                            {!! Form::label('outlet_name', 'Outlet Name *') !!}
                            {!! Form::text('outlet_name', old('outlet_name'), ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('channel') ? 'has-error' : '' }}">
                            {!! Form::label('channel', 'Channel Type *') !!}
                            {!! Form::select('channel', $channels, old('channel'), ['class' => 'form-control select2', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('building', 'Building') !!}
                            {!! Form::text('building', old('building'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('road', 'Road') !!}
                            {!! Form::text('road', old('road'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('area', 'Area') !!}
                            {!! Form::text('area', old('area'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('years_in_business') ? 'has-error' : '' }}">
                            {!! Form::label('years_in_business', 'Years in Business *') !!}
                            {!! Form::number('years_in_business', old('years_in_business', 0), ['class' => 'form-control', 'min' => 0, 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Brands Currently Stocked</label>
                            <div class="tw-flex tw-flex-wrap tw-gap-3">
                                @foreach($brands as $brand)
                                <label class="tw-flex tw-items-center tw-gap-1 tw-font-normal">
                                    <input type="checkbox" name="brands_stocked[]" value="{{ $brand }}"
                                        {{ in_array($brand, old('brands_stocked', [])) ? 'checked' : '' }}>
                                    {{ $brand }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer tw-flex tw-gap-2">
                <button type="button" class="btn btn-default btn-prev-step">← Back</button>
                <button type="button" class="btn btn-primary btn-next-step">Next: Documents →</button>
            </div>
        </div>
    </div>

    {{-- Step 3: Legal Documents --}}
    <div class="dealer-step tw-hidden" data-step="3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Step 3 — Legal Documents <small>(At least ONE required)</small></h3>
            </div>
            <div class="box-body">
                @if($errors->has('legal_docs'))
                <div class="alert alert-danger">{{ $errors->first('legal_docs') }}</div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        @include('cooler.dealers._upload_field', ['field' => 'county_business_permit', 'label' => 'County Business Permit', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB'])
                        <div class="form-group">
                            {!! Form::label('county_business_permit_expiry', 'Permit Expiry Date') !!}
                            {!! Form::date('county_business_permit_expiry', old('county_business_permit_expiry'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        @include('cooler.dealers._upload_field', ['field' => 'certificate_of_registration', 'label' => 'Certificate of Registration', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB'])
                    </div>
                    <div class="col-md-6">
                        @include('cooler.dealers._upload_field', ['field' => 'certificate_of_incorporation', 'label' => 'Certificate of Incorporation', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB'])
                    </div>
                    <div class="col-md-6">
                        @include('cooler.dealers._upload_field', ['field' => 'tax_certificate', 'label' => 'Tax Compliance Certificate', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB'])
                        <div class="form-group">
                            {!! Form::label('tax_certificate_expiry', 'Certificate Expiry Date') !!}
                            {!! Form::date('tax_certificate_expiry', old('tax_certificate_expiry'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer tw-flex tw-gap-2">
                <button type="button" class="btn btn-default btn-prev-step">← Back</button>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Register Dealer</button>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script>
$(function () {
    var currentStep = 1;

    function showStep(step) {
        $('.dealer-step').addClass('tw-hidden');
        $('.dealer-step[data-step="' + step + '"]').removeClass('tw-hidden');
        $('.step-circle').each(function () {
            var s = parseInt($(this).data('step'));
            $(this).toggleClass('tw-bg-blue-600 tw-text-white', s === step)
                   .toggleClass('tw-bg-gray-200 tw-text-gray-600', s !== step);
        });
        currentStep = step;
    }

    showStep(1);

    $(document).on('click', '.btn-next-step', function () { showStep(currentStep + 1); });
    $(document).on('click', '.btn-prev-step', function () { showStep(currentStep - 1); });

    // Preview uploaded files
    $(document).on('change', '.doc-upload-input', function () {
        var file = this.files[0];
        var preview = $(this).siblings('.upload-preview');
        if (!file) return;
        preview.text(file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)');
        preview.addClass('tw-text-green-600').removeClass('tw-text-red-500');
    });
});
</script>
@endsection
