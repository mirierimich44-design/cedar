@extends('layouts.app')
@section('title', 'KCB Buni Transactions')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>KCB Buni Transactions</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">All KCB Buni Transactions</h3>
            <div class="box-tools">
                <button type="button" class="btn btn-info" id="check_kcb_balance">
                    <i class="fa fa-money"></i> Check Balance
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="kcb_buni_transactions_table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Recipient</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        var kcb_buni_transactions_table = $('#kcb_buni_transactions_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ action([\Modules\KcbBuni\Http\Controllers\KcbBuniGatewayController::class, 'transactions']) }}",
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'merchant_reference', name: 'merchant_reference' },
                { data: 'type', name: 'type' },
                { data: 'amount', name: 'amount' },
                { data: 'recipient_account', name: 'recipient_account' },
                { data: 'status_label', name: 'status' }
            ]
        });

        $(document).on('click', '#check_kcb_balance', function(){
            $.ajax({
                method: "GET",
                url: "{{ action([\Modules\KcbBuni\Http\Controllers\KcbBuniGatewayController::class, 'getBalance']) }}",
                dataType: "json",
                success: function(result){
                    if(result.success == true){
                        swal('Account Balance', result.data.Primary.balance || 'Query successful', 'success');
                    } else {
                        toastr.error(result.message);
                    }
                }
            });
        });
    });
</script>
@endsection
