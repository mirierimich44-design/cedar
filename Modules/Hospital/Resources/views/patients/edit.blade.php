@extends('layouts.app')
@section('title', 'Edit Patient — ' . $patient->full_name)

@section('content')
<section class="content-header">
    <h1><i class="fa fa-user-edit"></i> Edit Patient
        <small>{{ $patient->patient_no }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('hospital.patients.index') }}">Patients</a></li>
        <li><a href="{{ route('hospital.patients.show', $patient->id) }}">{{ $patient->full_name }}</a></li>
        <li class="active">Edit</li>
    </ol>
</section>

<section class="content">
    @component('components.widget')
        <form id="edit_patient_form">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" required value="{{ $patient->first_name }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control" required value="{{ $patient->last_name }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ $patient->dob ? \Carbon\Carbon::parse($patient->dob)->format('Y-m-d') : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="M" {{ $patient->gender === 'M' ? 'selected' : '' }}>Male</option>
                            <option value="F" {{ $patient->gender === 'F' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ $patient->gender === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Blood Group</label>
                        <select name="blood_group" class="form-control">
                            <option value="">-- Unknown --</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                <option value="{{ $bg }}" {{ $patient->blood_group === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ $patient->phone }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $patient->email }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2">{{ $patient->address }}</textarea>
            </div>

            <div class="form-group">
                <label>Allergies</label>
                <textarea name="allergies" class="form-control" rows="2" placeholder="List known allergies…">{{ $patient->allergies }}</textarea>
            </div>

            <hr>
            <h5><i class="fa fa-phone-square"></i> Emergency Contact</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="form-control" value="{{ $patient->emergency_contact_name }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" class="form-control" value="{{ $patient->emergency_contact_phone }}">
                    </div>
                </div>
            </div>

            <div class="tw-flex tw-gap-2 tw-mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update Patient
                </button>
                <a href="{{ route('hospital.patients.show', $patient->id) }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    @endcomponent
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function () {
    $('#edit_patient_form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route('hospital.patients.update', $patient->id) }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    toastr.success(response.msg);
                    setTimeout(function () {
                        window.location.href = '{{ route('hospital.patients.show', $patient->id) }}';
                    }, 1000);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON;
                if (errors && errors.errors) {
                    $.each(errors.errors, function (key, val) { toastr.error(val[0]); });
                } else {
                    toastr.error('An error occurred.');
                }
            }
        });
    });
});
</script>
@endsection
