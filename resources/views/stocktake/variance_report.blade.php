@extends('layouts.app')
@section('title', __('Variance Report') . ' - ' . $stocktake->ref_no)

@section('content')
<section class="content-header no-print">
    <h1>@lang('Variance Report')
        <small>{{ $stocktake->ref_no }}</small>
    </h1>
</section>

<section class="content">
    <div class="box box-warning">
        <div class="box-header with-border no-print">
            <h3 class="box-title">@lang('Stock Variance Summary')</h3>
            <div class="box-tools">
                <button class="btn btn-default" onclick="window.print()">
                    <i class="fa fa-print"></i> @lang('Print')
                </button>
                <a href="{{ action([\App\Http\Controllers\StocktakeController::class, 'show'], $stocktake->id) }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> @lang('messages.go_back')
                </a>
            </div>
        </div>
        <div class="box-body">
            <!-- Summary Stats -->
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-boxes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Products</span>
                            <span class="info-box-number">{{ $lines->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-plus"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Over Stock</span>
                            <span class="info-box-number">{{ $lines->where('variance', '>', 0)->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-red">
                        <span class="info-box-icon"><i class="fa fa-minus"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Under Stock</span>
                            <span class="info-box-number">{{ $lines->where('variance', '<', 0)->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-gray">
                        <span class="info-box-icon"><i class="fa fa-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Matched</span>
                            <span class="info-box-number">{{ $lines->where('variance', 0)->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variance Table -->
            <h4>@lang('Products with Variance')</h4>
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>@lang('SKU')</th>
                        <th>@lang('sale.product')</th>
                        <th>@lang('Variation')</th>
                        <th class="text-right">@lang('System Qty')</th>
                        <th class="text-right">@lang('Counted Qty')</th>
                        <th class="text-right">@lang('Variance')</th>
                        <th class="text-right">@lang('Unit Cost')</th>
                        <th class="text-right">@lang('Unit Price')</th>
                        <th class="text-right">@lang('Variance Value')</th>
                        <th class="text-center">@lang('Status')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lines->where('variance', '!=', 0) as $line)
                    <tr class="{{ $line->variance < 0 ? 'danger' : 'success' }}">
                        <td>{{ $line->sku }}</td>
                        <td>{{ $line->product_name }}</td>
                        <td>{{ $line->variation_name ?? 'Default' }}</td>
                        <td class="text-right">{{ number_format($line->system_qty, 2) }}</td>
                        <td class="text-right">{{ number_format($line->counted_qty, 2) }}</td>
                        <td class="text-right">
                            <strong>{{ $line->variance > 0 ? '+' : '' }}{{ number_format($line->variance, 2) }}</strong>
                        </td>
                        <td class="text-right">{{ number_format($line->default_purchase_price, 2) }}</td>
                        <td class="text-right">{{ number_format($line->default_sell_price, 2) }}</td>
                        <td class="text-right">
                            <strong>{{ number_format($line->variance * $line->default_purchase_price, 2) }}</strong>
                        </td>
                        <td class="text-center">
                            @if($line->variance < 0)
                                <span class="label label-danger">Shortage</span>
                            @else
                                <span class="label label-success">Surplus</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-success">
                            <i class="fa fa-check-circle"></i> @lang('No variances found. All counts match!')
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray font-17">
                        <td colspan="8" class="text-right"><strong>@lang('Total Variance Value'):</strong></td>
                        <td class="text-right">
                            @php
                                $total_variance_value = $lines->sum(function($line) {
                                    return $line->variance * $line->default_purchase_price;
                                });
                            @endphp
                            <strong>{{ number_format($total_variance_value, 2) }}</strong>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

<style>
@media print {
    .no-print { display: none !important; }
    .box { border: 1px solid #ddd !important; }
}
</style>
@endsection
