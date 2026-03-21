@extends('layouts.app')
@section('title', 'Edit Dealer — ' . $dealer->outlet_name)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">Edit Dealer — {{ $dealer->outlet_name }}</h1>
</section>

<section class="content">
    {!! Form::model($dealer, ['route' => ['cooler.dealers.update', $dealer->id], 'method' => 'PUT', 'files' => true]) !!}
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="tw-list-disc tw-pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Personal Details</h3></div>
                <div class="box-body">
                    <div class="form-group"><label>Full Name *</label>{!! Form::text('name', null, ['class'=>'form-control','required']) !!}</div>
                    <div class="form-group"><label>ID Number *</label>{!! Form::text('id_number', null, ['class'=>'form-control','required']) !!}</div>
                    <div class="form-group"><label>KRA PIN *</label>{!! Form::text('kra_pin', null, ['class'=>'form-control','required']) !!}</div>
                    <div class="form-group"><label>Phone *</label>{!! Form::text('phone', null, ['class'=>'form-control','required']) !!}</div>
                    <div class="form-group"><label>Postal Address</label>{!! Form::text('postal_address', null, ['class'=>'form-control']) !!}</div>
                    <div class="form-group"><label>Status</label>{!! Form::select('status', ['active'=>'Active','inactive'=>'Inactive','suspended'=>'Suspended'], null, ['class'=>'form-control select2']) !!}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Business Details</h3></div>
                <div class="box-body">
                    <div class="form-group"><label>Outlet Name *</label>{!! Form::text('outlet_name', null, ['class'=>'form-control','required']) !!}</div>
                    <div class="form-group"><label>Channel *</label>{!! Form::select('channel', $channels, null, ['class'=>'form-control select2']) !!}</div>
                    <div class="form-group"><label>Building</label>{!! Form::text('building', null, ['class'=>'form-control']) !!}</div>
                    <div class="form-group"><label>Road</label>{!! Form::text('road', null, ['class'=>'form-control']) !!}</div>
                    <div class="form-group"><label>Area</label>{!! Form::text('area', null, ['class'=>'form-control']) !!}</div>
                    <div class="form-group"><label>Years in Business *</label>{!! Form::number('years_in_business', null, ['class'=>'form-control','min'=>0,'required']) !!}</div>
                    <div class="form-group">
                        <label>Brands Stocked</label>
                        <div class="tw-flex tw-flex-wrap tw-gap-3">
                            @foreach($brands as $brand)
                            <label class="tw-flex tw-items-center tw-gap-1 tw-font-normal">
                                <input type="checkbox" name="brands_stocked[]" value="{{ $brand }}"
                                    {{ in_array($brand, $dealer->brands_stocked ?? []) ? 'checked' : '' }}>
                                {{ $brand }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-success">
        <div class="box-header with-border"><h3 class="box-title">Upload New Documents (optional — replaces existing)</h3></div>
        <div class="box-body">
            <div class="row">
                @foreach(['id_copy' => 'ID Copy', 'kra_pin_certificate' => 'KRA PIN Certificate', 'passport_photo' => 'Passport Photo', 'county_business_permit' => 'County Business Permit', 'certificate_of_registration' => 'Certificate of Registration', 'certificate_of_incorporation' => 'Certificate of Incorporation', 'tax_certificate' => 'Tax Certificate'] as $field => $label)
                <div class="col-md-3 tw-mb-3">
                    <div class="form-group">
                        <label>{{ $label }}</label>
                        <input type="file" name="{{ $field }}" class="form-control-file"
                            accept="{{ in_array($field, ['passport_photo']) ? '.jpg,.jpeg,.png' : '.pdf,.jpg,.jpeg,.png' }}">
                        @php $existing = $dealer->getDocumentByType($field); @endphp
                        @if($existing)
                        <small class="tw-text-green-600"><i class="fa fa-check"></i> Uploaded: {{ $existing->file_name }}</small>
                        @else
                        <small class="tw-text-gray-400">Not yet uploaded</small>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Update Dealer</button>
        <a href="{{ route('cooler.dealers.show', $dealer->id) }}" class="btn btn-default">Cancel</a>
    </div>

    {!! Form::close() !!}
</section>
@endsection
