@extends('layouts.app')
@section('title', 'Expiry 30/60/90')

@section('css')
@include('layouts.partials.page_modern_css')
@endsection

@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <h1>Expiring medicines</h1>
                    <p class="pg-subtitle">Batches with remaining qty by 30 / 60 / 90 day window &middot; value at risk @format_currency($totalValue)</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important">All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        <form method="get" class="row" style="margin-bottom:14px">
            <div class="col-sm-3">
                <label>Location</label>
                {!! Form::select('location_id', $business_locations, $location_id, ['class'=>'form-control select2','placeholder'=>__('lang_v1.all')]) !!}
            </div>
            <div class="col-sm-3">
                <label>Window</label>
                <select name="band" class="form-control">
                    <option value="30" @if($band==='30') selected @endif>Next 30 days</option>
                    <option value="60" @if($band==='60') selected @endif>Next 60 days</option>
                    <option value="90" @if($band==='90') selected @endif>Next 90 days</option>
                    <option value="expired" @if($band==='expired') selected @endif>Already expired</option>
                </select>
            </div>
            <div class="col-sm-3" style="padding-top:22px">
                <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> Apply</button>
            </div>
        </form>

        <div class="row" style="margin-bottom:12px">
            <div class="col-sm-4"><div class="info-box bg-yellow"><span class="info-box-icon"><i class="fa fa-cubes"></i></span><div class="info-box-content"><span class="info-box-text">Lines</span><span class="info-box-number">{{ count($rows) }}</span></div></div></div>
            <div class="col-sm-4"><div class="info-box bg-aqua"><span class="info-box-icon"><i class="fa fa-sort-amount-asc"></i></span><div class="info-box-content"><span class="info-box-text">Qty left</span><span class="info-box-number">{{ @num_format($totalQty) }}</span></div></div></div>
            <div class="col-sm-4"><div class="info-box bg-red"><span class="info-box-icon"><i class="fa fa-money"></i></span><div class="info-box-content"><span class="info-box-text">Value at risk</span><span class="info-box-number">@format_currency($totalValue)</span></div></div></div>
        </div>

        @includeIf('report.partials.export_toolbar', ['table' => '#expiry_smart_table', 'title' => 'Expiry report'])

        <div class="box box-solid">
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped" id="expiry_smart_table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Lot</th>
                            <th>Expiry</th>
                            <th>Days left</th>
                            <th>Qty</th>
                            <th>Value</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr class="{{ ($r->days_left ?? 0) < 0 ? 'danger' : (($r->days_left ?? 99) <= 30 ? 'warning' : '') }}">
                            <td>{{ $r->product_name }}</td>
                            <td>{{ $r->sub_sku }}</td>
                            <td>{{ $r->lot_number ?: '—' }}</td>
                            <td>{{ $r->exp_date }}</td>
                            <td>{{ $r->days_left }}</td>
                            <td>{{ @num_format($r->qty_left) }}</td>
                            <td>@format_currency($r->value_at_risk)</td>
                            <td>{{ $r->location_name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No batches in this window.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
