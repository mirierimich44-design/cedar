@extends('layouts.app')
@section('title', 'Upload Prescription')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Upload DDA Prescription</h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'New Prescription'])
        {!! Form::open(['route' => 'dda.prescriptions.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}

        <div class="row">
            <div class="col-md-6">
                <h5><strong>Patient Details</strong></h5>
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" name="customer_name" class="form-control" placeholder="Full name">
                </div>
                <div class="form-group">
                    <label>National ID / Passport No.</label>
                    <input type="text" name="customer_id_number" class="form-control" placeholder="ID number">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="customer_phone" class="form-control" placeholder="07XXXXXXXX">
                </div>
            </div>
            <div class="col-md-6">
                <h5><strong>Prescriber Details</strong></h5>
                <div class="form-group">
                    <label>Prescriber Name *</label>
                    <input type="text" name="prescriber_name" class="form-control" required placeholder="Dr. John Doe">
                </div>
                <div class="form-group">
                    <label>License / Registration No.</label>
                    <input type="text" name="prescriber_license" class="form-control" placeholder="Kenya Medical Practitioners Board No.">
                </div>
                <div class="form-group">
                    <label>Hospital / Clinic</label>
                    <input type="text" name="prescriber_hospital" class="form-control" placeholder="Hospital name">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>DDA Product (optional)</label>
                    <select name="product_id" class="form-control">
                        <option value="">-- Select Product --</option>
                        @foreach($dda_products as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Prescription Image * <small class="text-muted">(JPG, PNG, max 5MB)</small></label>
                    <input type="file" name="prescription_image" class="form-control" accept="image/*" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-danger"><i class="fa fa-upload"></i> Upload Prescription</button>
                <a href="{{ route('dda.prescriptions') }}" class="btn btn-default" style="margin-left:8px">Cancel</a>
            </div>
        </div>

        {!! Form::close() !!}
    @endcomponent
</section>
@endsection
