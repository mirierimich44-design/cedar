@extends('layouts.app')
@section('title', 'Expired Drugs')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Expired Drugs</h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-danger', 'title' => 'Expired Stock'])

        {{-- Filter --}}
        <form method="GET" action="{{ route('dda.expired') }}" class="form-inline" style="margin-bottom:16px">
            <div class="form-group" style="margin-right:8px">
                <label style="margin-right:6px">Show:</label>
                <select name="filter" class="form-control input-sm" onchange="this.form.submit()">
                    <option value="all"  {{ request('filter','all') == 'all'  ? 'selected' : '' }}>All Expired Drugs</option>
                    <option value="dda"  {{ request('filter') == 'dda'  ? 'selected' : '' }}>DDA Controlled Only</option>
                </select>
            </div>
            <span class="text-muted" style="font-size:12px">Showing drugs with remaining stock past their expiry date.</span>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Batch / Lot</th>
                        <th>Expiry Date</th>
                        <th>Qty Remaining</th>
                        <th>Unit</th>
                        <th>Location</th>
                        <th>DDA?</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expired as $row)
                    <tr>
                        <td>{{ $row->product_name }}</td>
                        <td><code>{{ $row->sku }}</code></td>
                        <td>{{ $row->lot_number ?? '—' }}</td>
                        <td>
                            <span class="text-danger">
                                <i class="fa fa-exclamation-triangle"></i>
                                {{ \Carbon\Carbon::parse($row->exp_date)->format('d M Y') }}
                            </span>
                            <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($row->exp_date)->diffForHumans() }}
                            </small>
                        </td>
                        <td><strong>{{ number_format($row->qty_remaining, 2) }}</strong></td>
                        <td>{{ $row->unit_name ?? '—' }}</td>
                        <td>{{ $row->location_name ?? '—' }}</td>
                        <td>
                            @if($row->is_dda)
                                <span class="label label-danger">DDA</span>
                            @else
                                <span class="label label-default">No</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('dda.destruction') }}" class="btn btn-xs btn-warning">
                                <i class="fa fa-trash"></i> Log Disposal
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-success">
                            <i class="fa fa-check-circle"></i> No expired stock found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expired->count() > 0)
        <div class="row" style="margin-top:8px">
            <div class="col-md-12">
                <p class="text-muted" style="font-size:12px">
                    <i class="fa fa-info-circle"></i>
                    Expired drugs must be quarantined and disposed of through a PPB-approved NEMA-licensed company.
                    Click <strong>"Log Disposal"</strong> to start a disposal request.
                </p>
            </div>
        </div>
        @endif

    @endcomponent
</section>
@endsection
