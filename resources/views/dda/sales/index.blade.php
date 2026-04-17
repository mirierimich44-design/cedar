@extends('layouts.app')
@section('title', 'DDA Sales Report')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">DDA Sales Report</h1>
</section>

<section class="content">

    {{-- Date filter --}}
    <form method="GET" action="{{ route('dda.sales') }}" class="form-inline" style="margin-bottom:16px">
        <div class="form-group" style="margin-right:8px">
            <label style="margin-right:4px">From:</label>
            <input type="date" name="start_date" value="{{ $start }}" class="form-control input-sm">
        </div>
        <div class="form-group" style="margin-right:8px">
            <label style="margin-right:4px">To:</label>
            <input type="date" name="end_date" value="{{ $end }}" class="form-control input-sm">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="{{ route('dda.sales') }}" class="btn btn-default btn-sm" style="margin-left:4px">This Month</a>
    </form>

    {{-- Summary cards --}}
    @if($totals)
    <div class="row" style="margin-bottom:16px">
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-blue"><i class="fa fa-file-text"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Invoices</span>
                    <span class="info-box-number">{{ number_format($totals->total_invoices) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Units Sold</span>
                    <span class="info-box-number">{{ number_format($totals->total_qty, 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Revenue</span>
                    <span class="info-box-number">KES {{ number_format($totals->total_revenue, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' => 'Sales of Controlled Substances'])

        @if($sales->total() === 0)
            <div class="alert alert-info">
                No DDA sales found for
                {{ \Carbon\Carbon::parse($start)->format('d M Y') }} –
                {{ \Carbon\Carbon::parse($end)->format('d M Y') }}.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice No.</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>DDA Drug</th>
                            <th>Class</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total (KES)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sale->transaction_date)->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('sell.printInvoice', $sale->transaction_id) }}" target="_blank">
                                    {{ $sale->invoice_no ?? '#'.$sale->transaction_id }}
                                </a>
                            </td>
                            <td>{{ $sale->customer_name ?? '—' }}</td>
                            <td>{{ $sale->product_name }}</td>
                            <td>{{ $sale->dda_drug_name ?? '—' }}</td>
                            <td>
                                @if($sale->dda_class)
                                    <span class="label label-danger">{{ strtoupper($sale->dda_class) }}</span>
                                @else —
                                @endif
                            </td>
                            <td>{{ number_format($sale->quantity, 2) }}</td>
                            <td>{{ number_format($sale->unit_price_before_discount, 2) }}</td>
                            <td><strong>{{ number_format($sale->total, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="active">
                            <th colspan="6" class="text-right">Period Totals</th>
                            <th>{{ number_format($totals->total_qty, 2) }}</th>
                            <th></th>
                            <th>KES {{ number_format($totals->total_revenue, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            {{ $sales->appends(request()->query())->links() }}
        @endif

    @endcomponent
</section>
@endsection
