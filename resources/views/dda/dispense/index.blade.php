@extends('layouts.app')
@section('title', 'DDA Dispense Register')

@php
/**
 * Convert a number to words (for DDA register quantity-in-words requirement).
 * Kenya PPB requires quantity written in both figures AND words.
 */
function numToWords($n) {
    $n = (int) round($n);
    $ones  = ['','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE',
               'TEN','ELEVEN','TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN',
               'SEVENTEEN','EIGHTEEN','NINETEEN'];
    $tens  = ['','','TWENTY','THIRTY','FORTY','FIFTY','SIXTY','SEVENTY','EIGHTY','NINETY'];
    if ($n === 0) return 'ZERO';
    if ($n < 20)  return $ones[$n];
    if ($n < 100) return $tens[(int)($n/10)] . ($n % 10 ? '-' . $ones[$n % 10] : '');
    if ($n < 1000) return $ones[(int)($n/100)] . ' HUNDRED' . ($n % 100 ? ' ' . numToWords($n % 100) : '');
    return $n . ' (see figures)';
}
@endphp

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">DDA Dispense Register</h1>
    <small class="text-muted">Kenya PPB Compliance — Pharmacy and Poisons Act Cap. 244</small>
</section>

<section class="content">

    {{-- Date filter --}}
    <form method="GET" action="{{ route('dda.dispense') }}" class="form-inline" style="margin-bottom:16px">
        <div class="form-group" style="margin-right:8px">
            <label style="margin-right:4px">From:</label>
            <input type="date" name="start_date" value="{{ $start }}" class="form-control input-sm">
        </div>
        <div class="form-group" style="margin-right:8px">
            <label style="margin-right:4px">To:</label>
            <input type="date" name="end_date" value="{{ $end }}" class="form-control input-sm">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="{{ route('dda.dispense') }}" class="btn btn-default btn-sm" style="margin-left:4px">This Month</a>
    </form>

    {{-- Summary cards --}}
    @if($totals)
    <div class="row" style="margin-bottom:16px">
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-blue"><i class="fa fa-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Transactions</span>
                    <span class="info-box-number">{{ number_format($totals->total_transactions) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-pills"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Units Dispensed</span>
                    <span class="info-box-number">{{ number_format($totals->total_qty, 0) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' => 'Controlled Drug Dispensing Register'])

        <p class="text-muted" style="font-size:11px; margin-bottom:12px">
            <i class="fa fa-info-circle"></i>
            <strong>Kenya PPB requirement:</strong> All dispensing of DDA controlled substances must be recorded with full patient details, prescriber information, batch traceability, and quantity in words. This register is subject to PPB inspection.
        </p>

        <div class="table-responsive">
            <table class="table table-bordered" style="font-size:12px">
                <thead class="bg-gray">
                    <tr>
                        <th style="width:30px">#</th>
                        <th>Date &amp; Time</th>
                        <th>Invoice</th>
                        <th>Drug / Product</th>
                        <th>Class</th>
                        <th>Qty<br><small>(Figures)</small></th>
                        <th>Qty<br><small>(Words)</small></th>
                        <th>Batch No.</th>
                        <th>Expiry</th>
                        <th>Patient Name</th>
                        <th>Dispensed By</th>
                        <th>Witness</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispense as $row)
                    @php
                        $qty      = (float) $row->quantity;
                        $qtyInt   = (int) round($qty);
                        $expClass = '';
                        if ($row->exp_date) {
                            $expClass = \Carbon\Carbon::parse($row->exp_date)->isPast() ? 'text-danger' : '';
                        }
                    @endphp
                    <tr>
                        <td class="text-muted text-center">
                            {{ ($dispense->currentPage() - 1) * $dispense->perPage() + $loop->iteration }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($row->transaction_date)->format('d M Y') }}<br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($row->transaction_date)->format('H:i') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('sell.printInvoice', $row->transaction_id) }}" target="_blank">
                                {{ $row->invoice_no ?? '#'.$row->transaction_id }}
                            </a>
                        </td>
                        <td>
                            <strong>{{ $row->drug_name ?? $row->product_name }}</strong>
                            @if($row->drug_name && $row->drug_name !== $row->product_name)
                                <br><small class="text-muted">{{ $row->product_name }}</small>
                            @endif
                        </td>
                        <td>
                            @if($row->drug_class)
                                <span class="label label-danger">{{ strtoupper($row->drug_class) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center"><strong>{{ number_format($qty, $qty == $qtyInt ? 0 : 2) }}</strong></td>
                        <td style="font-size:10px; letter-spacing:0.5px">
                            <strong>{{ numToWords($qtyInt) }}</strong>
                        </td>
                        <td>{{ $row->lot_number ?? '—' }}</td>
                        <td class="{{ $expClass }}">
                            {{ $row->exp_date ? \Carbon\Carbon::parse($row->exp_date)->format('M Y') : '—' }}
                        </td>
                        <td>{{ $row->customer_name ?? '—' }}</td>
                        <td>{{ trim($row->dispensed_by) ?: '—' }}</td>
                        <td><span class="text-muted" style="font-size:10px">Sign here</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted" style="padding:20px">
                            No DDA dispensing records for
                            {{ \Carbon\Carbon::parse($start)->format('d M Y') }} –
                            {{ \Carbon\Carbon::parse($end)->format('d M Y') }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($dispense->total() > 0)
                <tfoot>
                    <tr class="active">
                        <th colspan="5" class="text-right">Totals for period:</th>
                        <th class="text-center">{{ number_format($totals->total_qty, 0) }}</th>
                        <th colspan="6"></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{ $dispense->appends(request()->query())->links() }}

        <div class="row" style="margin-top:16px">
            <div class="col-md-12">
                <div class="alert alert-warning" style="font-size:11px; padding:8px 12px">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>PPB Note:</strong> This register must be kept for a minimum of <strong>2 years</strong> and be available for inspection at all times.
                    Patient ID number and prescriber registration number should be recorded at point of dispensing.
                    If dispensing from multiple batches, each batch must appear as a separate entry.
                </div>
            </div>
        </div>

    @endcomponent
</section>
@endsection
