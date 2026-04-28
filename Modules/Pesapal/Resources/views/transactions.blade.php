@extends('layouts.app')
@section('title', 'Pesapal Transactions')

@section('content')
<section class="content-header">
    <h1>Pesapal Transactions</h1>
</section>

<section class="content">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">All Pesapal Payments</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label>Status</label>
                    <select id="filter_status" class="form-control">
                        <option value="all">All</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="reversed">Reversed</option>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>From</label>
                    <input type="date" id="filter_start" class="form-control">
                </div>
                <div class="col-md-3 form-group">
                    <label>To</label>
                    <input type="date" id="filter_end" class="form-control">
                </div>
            </div>

            <table class="table table-striped" id="pesapal_tx_table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Tracking ID</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Confirmation</th>
                        <th>Initiated By</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

@section('javascript')
<script>
$(function(){
    var table = $('#pesapal_tx_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('pesapal.transactions') }}",
            data: function(d) {
                d.status = $('#filter_status').val();
                d.start_date = $('#filter_start').val();
                d.end_date = $('#filter_end').val();
            }
        },
        columns: [
            { data: 'created_at' },
            { data: 'merchant_reference' },
            { data: 'order_tracking_id' },
            { data: 'amount' },
            { data: 'payment_method' },
            { data: 'confirmation_code' },
            { data: 'initiated_by_name' },
            { data: 'status_label' },
        ],
        order: [[0, 'desc']]
    });

    $('#filter_status, #filter_start, #filter_end').on('change', function(){ table.ajax.reload(); });
});
</script>
@endsection
@endsection
