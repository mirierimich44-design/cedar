@extends('layouts.app')
@section('title', __('Stocktake Details') . ' - ' . $stocktake->ref_no)

@section('content')
<section class="content-header">
    <h1>@lang('Stocktake Details')
        <small>{{ $stocktake->ref_no }}</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('Stocktake Information')</h3>
                    <div class="box-tools">
                        <a href="{{ action([\App\Http\Controllers\StocktakeController::class, 'varianceReport'], $stocktake->id) }}" class="btn btn-info">
                            <i class="fa fa-file-alt"></i> @lang('Variance Report')
                        </a>
                        <a href="{{ action([\App\Http\Controllers\StocktakeController::class, 'index']) }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> @lang('messages.go_back')
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>@lang('Ref No'):</strong> {{ $stocktake->ref_no }}
                        </div>
                        <div class="col-md-4">
                            <strong>@lang('Location'):</strong> {{ optional($stocktake->location)->name }}
                        </div>
                        <div class="col-md-4">
                            <strong>@lang('Status'):</strong> 
                            @if($stocktake->status == 'completed')
                                <span class="badge badge-success">@lang('Completed')</span>
                            @else
                                <span class="badge badge-warning">@lang('Draft')</span>
                            @endif
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px;">
                        <div class="col-md-4">
                            <strong>@lang('Date'):</strong> {{ \Carbon::parse($stocktake->transaction_date)->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-8">
                            <strong>@lang('Notes'):</strong> {{ $stocktake->additional_notes ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($stocktake->stocktake_lines && $stocktake->stocktake_lines->count() > 0)
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('Counted Products')</h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>@lang('sale.product')</th>
                        <th>@lang('Variation')</th>
                        <th class="text-right">@lang('System Qty')</th>
                        <th class="text-right">@lang('Counted Qty')</th>
                        <th class="text-right">@lang('Variance')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocktake->stocktake_lines as $line)
                    <tr class="{{ $line->variance != 0 ? ($line->variance < 0 ? 'danger' : 'success') : '' }}">
                        <td>{{ optional($line->product)->name ?? 'N/A' }}</td>
                        <td>{{ optional($line->variation)->name ?? 'Default' }}</td>
                        <td class="text-right">{{ number_format($line->system_qty, 2) }}</td>
                        <td class="text-right">{{ number_format($line->counted_qty, 2) }}</td>
                        <td class="text-right">
                            @if($line->variance < 0)
                                <span class="text-danger">{{ number_format($line->variance, 2) }}</span>
                            @elseif($line->variance > 0)
                                <span class="text-success">+{{ number_format($line->variance, 2) }}</span>
                            @else
                                <span class="text-muted">0.00</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="info">
                        <th colspan="2">@lang('Total')</th>
                        <th class="text-right">{{ number_format($stocktake->stocktake_lines->sum('system_qty'), 2) }}</th>
                        <th class="text-right">{{ number_format($stocktake->stocktake_lines->sum('counted_qty'), 2) }}</th>
                        <th class="text-right">{{ number_format($stocktake->stocktake_lines->sum('variance'), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <i class="fa fa-exclamation-triangle"></i> @lang('No products have been counted yet.')
    </div>
    @endif
</section>
@endsection
