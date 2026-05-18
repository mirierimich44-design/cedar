@extends('layouts.app')
@section('title', 'Lab Orders')

@section('content')
<section class="content-header">
    <h1><i class="fa fa-flask"></i> Lab Orders
        <small>Pending &amp; Active</small>
    </h1>
    <ol class="breadcrumb">
        <li class="active">Lab Orders</li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Lab Orders</h3>
        </div>
        <div class="box-body">
            <table id="lab-table" class="table table-striped table-bordered" style="width:100%;">
                <thead>
                    <tr>
                        <th>Visit No.</th>
                        <th>Patient</th>
                        <th>Test Name</th>
                        <th>Code</th>
                        <th>Ordered By</th>
                        <th>Ordered At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

{{-- Enter Result Modal --}}
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-edit"></i> Enter Lab Result</h4>
            </div>
            <div class="modal-body">
                <form id="result_form">
                    @csrf
                    <input type="hidden" id="result_order_id">
                    <div class="form-group">
                        <label>Result Value <span class="text-danger">*</span></label>
                        <input type="text" name="result_value" class="form-control" required placeholder="e.g. 13.5 or Negative">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Unit</label>
                                <input type="text" name="result_unit" class="form-control" placeholder="e.g. g/dL">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reference Range</label>
                                <input type="text" name="reference_range" class="form-control" placeholder="e.g. 12-16 g/dL">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notes / Interpretation</label>
                        <textarea name="result_notes" class="form-control" rows="3" placeholder="Additional comments…"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="btn-save-result">
                    <i class="fa fa-save"></i> Save Result
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(function () {
    var table = $('#lab-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('hospital.lab.index') }}',
        columns: [
            { data: 'visit_no',      name: 'visit_no',     orderable: false },
            { data: 'patient_name',  name: 'patient_name', orderable: false },
            { data: 'test_name',     name: 'test_name' },
            { data: 'test_code',     name: 'test_code',    defaultContent: '-' },
            { data: 'ordered_by',    name: 'ordered_by' },
            { data: 'ordered_at',    name: 'ordered_at',   render: function(d){ return d ? moment(d).format('DD MMM YYYY HH:mm') : '-'; } },
            { data: 'status_badge',  name: 'status',       orderable: false },
            { data: 'action',        name: 'action',       orderable: false, searchable: false },
        ],
        order: [[5, 'desc']],
        pageLength: 25,
    });

    // Open result modal
    $(document).on('click', '.btn-enter-result', function () {
        var id = $(this).data('id');
        $('#result_order_id').val(id);
        $('#result_form')[0].reset();
        $('#resultModal').modal('show');
    });

    // Save result
    $('#btn-save-result').on('click', function () {
        var id = $('#result_order_id').val();
        if (!id) return;
        $.ajax({
            url: '/hospital/lab-orders/' + id + '/result',
            method: 'POST',
            data: $('#result_form').serialize(),
            success: function (r) {
                if (r.success) {
                    $('#resultModal').modal('hide');
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
