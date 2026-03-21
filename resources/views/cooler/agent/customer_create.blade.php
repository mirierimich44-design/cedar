@extends('layouts.app')
@section('title', 'Register Customer')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">Register Customer
        <small class="tw-text-sm tw-text-gray-500 tw-font-normal">Onboard a new customer</small>
    </h1>
</section>

<section class="content">
    {!! Form::open(['route' => 'cooler.agent.customers.store', 'method' => 'POST', 'files' => true, 'id' => 'agent-customer-form']) !!}

    {{-- Step indicator --}}
    <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2 tw-mb-6 tw-overflow-x-auto">
        @foreach(['Personal & ID', 'Business Info', 'Legal Docs'] as $i => $step)
        <div class="tw-flex tw-items-center tw-gap-2">
            <div class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-sm tw-font-bold step-circle" data-step="{{ $i+1 }}">{{ $i+1 }}</div>
            <span class="tw-text-sm tw-text-gray-600">{{ $step }}</span>
            @if(!$loop->last)<div class="tw-h-px tw-bg-gray-300 tw-w-8 tw-mx-1"></div>@endif
        </div>
        @endforeach
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="tw-list-disc tw-pl-4 tw-mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Step 1: Personal --}}
    <div class="agent-step" data-step="1">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Step 1 — Personal Details &amp; ID Documents</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            {!! Form::label('name', 'Full Name *') !!}
                            {!! Form::text('name', old('name'), ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                            {!! Form::label('phone', 'Phone Number *') !!}
                            {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => '0712345678', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('id_number') ? 'has-error' : '' }}">
                            {!! Form::label('id_number', 'ID Number *') !!}
                            {!! Form::text('id_number', old('id_number'), ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('kra_pin', 'KRA PIN') !!}
                            {!! Form::text('kra_pin', old('kra_pin'), ['class' => 'form-control', 'placeholder' => 'A000000000Z']) !!}
                        </div>
                    </div>
                </div>
                <h4 class="tw-font-semibold tw-mt-4 tw-mb-3 tw-text-gray-700">Required Documents</h4>
                <div class="row">
                    <div class="col-md-4">
                        @include('cooler.dealers._upload_field', ['field' => 'id_copy', 'label' => 'ID Copy *', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB', 'required' => true])
                    </div>
                    <div class="col-md-4">
                        @include('cooler.dealers._upload_field', ['field' => 'kra_pin_certificate', 'label' => 'KRA PIN Certificate *', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB', 'required' => true])
                    </div>
                    <div class="col-md-4">
                        @include('cooler.dealers._upload_field', ['field' => 'passport_photo', 'label' => 'Passport Photo *', 'accept' => '.jpg,.jpeg,.png', 'hint' => 'Image only, max 2MB', 'required' => true])
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="button" class="btn btn-primary btn-next-step">Next: Business Info →</button>
            </div>
        </div>
    </div>

    {{-- Step 2: Business --}}
    <div class="agent-step tw-hidden" data-step="2">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Step 2 — Business Details</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('outlet_name') ? 'has-error' : '' }}">
                            {!! Form::label('outlet_name', 'Outlet / Shop Name *') !!}
                            {!! Form::text('outlet_name', old('outlet_name'), ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('channel') ? 'has-error' : '' }}">
                            {!! Form::label('channel', 'Channel Type *') !!}
                            {!! Form::select('channel', array_combine(\App\CoolerDealer::$channels, array_map('ucfirst', \App\CoolerDealer::$channels)), old('channel'), ['class' => 'form-control select2', 'required', 'placeholder' => '— Select —']) !!}
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
                            {!! Form::label('road', 'Road / Street') !!}
                            {!! Form::text('road', old('road'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('area', 'Area / Estate') !!}
                            {!! Form::text('area', old('area'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('years_in_business', 'Years in Business') !!}
                            {!! Form::number('years_in_business', old('years_in_business', 0), ['class' => 'form-control', 'min' => 0]) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Brands Currently Stocked</label>
                            <div class="tw-flex tw-flex-wrap tw-gap-3">
                                @foreach(\App\CoolerDealer::$brandOptions as $brand)
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
                <button type="button" class="btn btn-primary btn-next-step">Next: Legal Docs →</button>
            </div>
        </div>
    </div>

    {{-- Step 3: Legal docs (optional) --}}
    <div class="agent-step tw-hidden" data-step="3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Step 3 — Legal Documents <small class="tw-text-gray-500">(at least one recommended)</small></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        @include('cooler.dealers._upload_field', ['field' => 'county_business_permit', 'label' => 'County Business Permit', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB'])
                    </div>
                    <div class="col-md-6">
                        @include('cooler.dealers._upload_field', ['field' => 'certificate_of_registration', 'label' => 'Certificate of Registration', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB'])
                    </div>
                    <div class="col-md-6">
                        @include('cooler.dealers._upload_field', ['field' => 'certificate_of_incorporation', 'label' => 'Certificate of Incorporation', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB'])
                    </div>
                    <div class="col-md-6">
                        @include('cooler.dealers._upload_field', ['field' => 'tax_certificate', 'label' => 'Tax Compliance Certificate', 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF or Image, max 5MB'])
                    </div>
                </div>
            </div>
            <div class="box-footer tw-flex tw-gap-2">
                <button type="button" class="btn btn-default btn-prev-step">← Back</button>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Register Customer</button>
            </div>
        </div>
    </div>

    {!! Form::close() !!}

    @include('cooler.agent._mobile_nav')
</section>
@endsection

@section('javascript')
<script>
$(function () {
    var currentStep = 1;

    function showStep(step) {
        $('.agent-step').addClass('tw-hidden');
        $('.agent-step[data-step="' + step + '"]').removeClass('tw-hidden');
        $('.step-circle').each(function () {
            var s = parseInt($(this).data('step'));
            if (s < step) {
                $(this).css('background', 'var(--theme-main)').css('color', 'white');
            } else if (s === step) {
                $(this).css('background', 'var(--theme-dark)').css('color', 'white');
            } else {
                $(this).css('background', '#e5e7eb').css('color', '#6b7280');
            }
        });
        currentStep = step;
    }

    showStep(1);
    $(document).on('click', '.btn-next-step', function () { showStep(currentStep + 1); });
    $(document).on('click', '.btn-prev-step', function () { showStep(currentStep - 1); });

    $(document).on('change', '.doc-upload-input', function () {
        var file = this.files[0];
        if (!file) return;
        $(this).siblings('.upload-preview').text(file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)')
            .addClass('tw-text-green-600').removeClass('tw-text-red-500');
    });
});
</script>
@endsection
