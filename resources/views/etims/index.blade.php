@extends('layouts.app')
@section('title', 'eTIMS Invoices')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">eTIMS Invoices</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-file-text-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Sales</span>
                    <span class="info-box-number">{{$stats->total_count}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Synced</span>
                    <span class="info-box-number">{{$stats->success_count}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Failed</span>
                    <span class="info-box-number">{{$stats->failed_count}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending</span>
                    <span class="info-box-number">{{$stats->pending_count}}</span>
                </div>
            </div>
        </div>
    </div>

    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sync_status_filter',  'Sync Status:') !!}
                {!! Form::select('sync_status_filter', ['pending' => 'Pending', 'success' => 'Success', 'failed' => 'Failed'], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary'])
        @slot('title')
            eTIMS Invoices
            <span class="pull-right">
                <button id="sync_all_btn" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-primary tw-text-white">
                    <i class="fa fa-refresh"></i> Sync All Pending
                </button>
            </span>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="etims_invoices_table">
                <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('sale.invoice_no')</th>
                        <th>@lang('contact.customer')</th>
                        <th>@lang('sale.total_amount')</th>
                        <th>eTIMS Invoice No.</th>
                        <th>Status</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        etims_invoices_table = $('#etims_invoices_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action([\App\Http\Controllers\EtimsReportController::class, "index"])}}',
                data: function(d) {
                    d.sync_status = $('#sync_status_filter').val();
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'customer_name', name: 'c.name' },
                { data: 'final_total', name: 'final_total' },
                { data: 'etims_invoice_number', name: 'etims_invoice_number' },
                { data: 'etims_sync_status', name: 'etims_sync_status' },
                { data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        $(document).on('change', '#sync_status_filter', function() {
            etims_invoices_table.ajax.reload();
        });

        $('#sync_all_btn').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-refresh fa-spin"></i> Syncing...');

            $.ajax({
                method: 'POST',
                url: '{{ route("etims.sync-all") }}',
                data: { _token: '{{ csrf_token() }}' },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        toastr.success(result.msg);
                        etims_invoices_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                    btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Sync All Pending');
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                    btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Sync All Pending');
                }
            });
        });

        $(document).on('click', 'button.sync-invoice', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-refresh fa-spin"></i> Syncing...');
            
            $.ajax({
                method: 'GET',
                url: $(this).data('href'),
                dataType: 'json',
                success: function(result) {
                    if (result.success === true) {
                        toastr.success(result.msg);
                        etims_invoices_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                    btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Sync');
                },
            });
        });
    });
</script>
@endsection
