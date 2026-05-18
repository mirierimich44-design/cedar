@extends('layouts.app')
@section('title', 'Admit Body — Mortuary')

@section('content')
<section class="content-header">
    <h1><i class="fa fa-plus-circle"></i> Admit Body</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('hospital.mortuary.index') }}">Mortuary</a></li>
        <li class="active">Admit Body</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @component('components.widget')
                <form id="mortuary_form">
                    @csrf

                    <h5 style="margin-top:0;"><i class="fa fa-user"></i> Deceased Information</h5>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="deceased_name" class="form-control" required placeholder="Full legal name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="deceased_dob" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date of Death <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_death" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Time of Death</label>
                                <input type="time" name="time_of_death" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Cause of Death</label>
                        <textarea name="cause_of_death" class="form-control" rows="2" placeholder="As certified by attending doctor…"></textarea>
                    </div>

                    <hr>
                    <h5><i class="fa fa-archive"></i> Storage Details</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Storage Date <span class="text-danger">*</span></label>
                                <input type="date" name="storage_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Storage Location / Shelf</label>
                                <input type="text" name="storage_location" class="form-control" placeholder="e.g. Refrigerator A, Bay 3">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5><i class="fa fa-users"></i> Next of Kin / Brought By</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="brought_by" class="form-control" required placeholder="Full name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" name="brought_by_phone" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Relationship to Deceased <span class="text-danger">*</span></label>
                        <input type="text" name="relationship" class="form-control" required placeholder="e.g. Spouse, Son, Daughter">
                    </div>

                    <div class="form-group">
                        <label>Additional Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="tw-flex tw-gap-2 tw-mt-4">
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-save"></i> Admit &amp; Record
                        </button>
                        <a href="{{ route('hospital.mortuary.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            @endcomponent
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function () {
    $('#mortuary_form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route('hospital.mortuary.store') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function (r) {
                if (r.success) {
                    toastr.success(r.msg + ' — Ref: ' + r.data.body_reference);
                    setTimeout(function () {
                        window.location.href = '{{ route('hospital.mortuary.index') }}';
                    }, 1200);
                } else {
                    toastr.error(r.msg);
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON;
                if (errors && errors.errors) {
                    $.each(errors.errors, function (k, v) { toastr.error(v[0]); });
                } else {
                    toastr.error('An error occurred.');
                }
            }
        });
    });
});
</script>
@endsection
