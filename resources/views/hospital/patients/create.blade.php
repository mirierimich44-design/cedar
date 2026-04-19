@extends('layouts.app')
@section('title', 'Register Patient')

@section('content')
<section class="content-header">
    <h1>Register Patient <small>Create a new medical record</small></h1>
</section>

<section class="content">
    <form action="{{ route('hospital.patients.store') }}" method="POST">
        @csrf

        <div class="box box-solid">
            <div class="box-header with-border"><h3 class="box-title">Identity</h3></div>
            <div class="box-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Gender *</label>
                        <select name="gender" class="form-control" required>
                            <option value="">—</option>
                            <option value="male" @selected(old('gender')=='male')>Male</option>
                            <option value="female" @selected(old('gender')=='female')>Female</option>
                            <option value="other" @selected(old('gender')=='other')>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Mobile *</label>
                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Blood Group</label>
                        <select name="blood_group" class="form-control">
                            <option value="">Unknown</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                <option value="{{ $bg }}" @selected(old('blood_group')==$bg)>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-solid">
            <div class="box-header with-border"><h3 class="box-title">Address</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', 'Nairobi') }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', 'Kenya') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-solid">
            <div class="box-header with-border"><h3 class="box-title">Medical & Emergency</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Known Allergies</label>
                        <input type="text" name="allergies" class="form-control" value="{{ old('allergies') }}"
                               placeholder="e.g. Penicillin, Peanuts">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Chronic Conditions</label>
                        <input type="text" name="chronic_conditions" class="form-control" value="{{ old('chronic_conditions') }}"
                               placeholder="e.g. Diabetes, Hypertension">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Emergency Contact Number</label>
                        <input type="text" name="emergency_contact_number" class="form-control" value="{{ old('emergency_contact_number') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Insurance Provider (NHIF/SHA/Private)</label>
                        <input type="text" name="insurance_provider" class="form-control" value="{{ old('insurance_provider') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Insurance Policy / Member Number</label>
                        <input type="text" name="insurance_policy_number" class="form-control" value="{{ old('insurance_policy_number') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Register Patient
            </button>
            <a href="{{ route('hospital.patients.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</section>
@endsection
