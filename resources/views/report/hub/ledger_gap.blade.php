@extends('layouts.app')
@section('title', 'Stock vs purchase ledger')

@section('css')
@include('layouts.partials.page_modern_css')
@endsection

@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-unlink"></i></div>
                <div>
                    <h1>Stock vs purchase ledger</h1>
                    <p class="pg-subtitle">System stock higher than free purchase qty — these can block stock adjustments / sales mapping &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important">All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        <p class="apex-page-help"><i class="fa fa-info-circle"></i>
            <strong>Shortfall</strong> = system stock − free purchase ledger. Fix with purchase / opening stock or the ledger repair tool. Physical stock is not changed by this report.
        </p>
        <form method="get" class="form-inline" style="margin-bottom:14px">
            <label>Location &nbsp;</label>
            <select name="location_id" class="form-control select2" style="min-width:220px" onchange="this.form.submit()">
                @foreach($business_locations as $id => $name)
                    <option value="{{ $id }}" @if((string)$location_id === (string)$id) selected @endif>{{ $name }}</option>
                @endforeach
            </select>
        </form>

        @include('report.partials.export_toolbar', ['table' => '#ledger_gap_table', 'title' => 'Stock ledger gap'])

        <div class="box box-solid">
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped" id="ledger_gap_table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>System stock</th>
                            <th>Free purchase qty</th>
                            <th>Shortfall</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->sub_sku ?: $r->sku }}</td>
                            <td>{{ @num_format($r->system_qty) }}</td>
                            <td>{{ @num_format($r->free_qty) }}</td>
                            <td class="text-danger"><strong>{{ @num_format($r->shortfall) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No gaps — free purchase qty covers system stock for stocked products.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-muted">Showing up to 500 products with shortfall &gt; 0 at this location.</p>
    </section>
</div>
@endsection
