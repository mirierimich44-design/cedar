@extends('layouts.app')
@section('title', 'Mortuary')

@section('content')
<section class="content-header">
    <h1><i class="fa fa-bed"></i> Mortuary
        <small>Body Storage &amp; Records</small>
    </h1>
    <ol class="breadcrumb">
        <li class="active">Mortuary</li>
    </ol>
</section>

<section class="content">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Mortuary Records</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('hospital.mortuary.create') }}" class="btn btn-sm btn-danger">
                    <i class="fa fa-plus"></i> Admit Body
                </a>
            </div>
        </div>
        <div class="box-body">
            <table id="mortuary-table" class="table table-striped table-bordered" style="width:100%;">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Deceased Name</th>
                        <th>Gender</th>
                        <th>Date of Death</th>
                        <th>Storage Date</th>
                        <th>Brought By</th>
                        <th>Relationship</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

{{-- Release Modal --}}
<div class="modal fade" id="releaseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-check-circle"></i> Release Body</h4>
            </div>
            <div class="modal-body">
                <form id="release_form">
                    @csrf
                    <input type="hidden" id="release_record_id">
                    <div class="form-group">
                        <label>Released To <span class="text-danger">*</span></label>
                        <input type="text" name="released_to" class="form-control" required placeholder="Full name of recipient">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="released_to_phone" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Release Date <span class="text-danger">*</span></label>
                                <input type="date" name="release_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="btn-confirm-release">
                    <i class="fa fa-check"></i> Confirm Release
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(function () {
    var table = $('#mortuary-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('hospital.mortuary.index') }}',
        columns: [
            { data: 'body_reference',  name: 'body_reference' },
            { data: 'deceased_name',   name: 'deceased_name' },
            { data: 'gender',          name: 'gender', render: function(d){ return d==='M'?'Male':(d==='F'?'Female':d); } },
            { data: 'date_of_death',   name: 'date_of_death',  render: function(d){ return d ? moment(d).format('DD MMM YYYY') : '-'; } },
            { data: 'storage_date',    name: 'storage_date',   render: function(d){ return d ? moment(d).format('DD MMM YYYY') : '-'; } },
            { data: 'brought_by',      name: 'brought_by' },
            { data: 'relationship',    name: 'relationship' },
            { data: 'status_badge',    name: 'status',  orderable: false },
            { data: 'action',          name: 'action',  orderable: false, searchable: false },
        ],
        order: [[4, 'desc']],
        pageLength: 25,
    });

    // Open release modal
    $(document).on('click', '.btn-release-body', function () {
        var id = $(this).data('id');
        $('#release_record_id').val(id);
        $('#release_form')[0].reset();
        $('[name=release_date]').val('{{ date('Y-m-d') }}');
        $('#releaseModal').modal('show');
    });

    // Confirm release
    $('#btn-confirm-release').on('click', function () {
        var id = $('#release_record_id').val();
        if (!id) return;
        $.ajax({
            url: '/hospital/mortuary/' + id + '/release',
            method: 'POST',
            data: $('#release_form').serialize(),
            success: function (r) {
                if (r.success) {
                    $('#releaseModal').modal('hide');
                    toastr.success(r.msg);
                    table.ajax.reload(null, false);
                } else toastr.error(r.msg);
            },
            error: function (xhr) {
                var errors = xhr.responseJSON;
                if (errors && errors.errors) {
                    $.each(errors.errors, function(k, v){ toastr.error(v[0]); });
                } else toastr.error('An error occurred.');
            }
        });
    });
});
</script>
@endsection
