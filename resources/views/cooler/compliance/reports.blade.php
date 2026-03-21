@extends('layouts.app')
@section('title', 'Cooler Reports')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Cooler Management Reports</h1>
</section>

<section class="content">
    {{-- Asset status breakdown --}}
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title">Asset Status by Type</h3></div>
        <div class="box-body">
            <table class="table table-bordered">
                <thead><tr><th>Asset Type</th><th>Status</th><th>Count</th></tr></thead>
                <tbody>
                    @foreach($assetStatusBreakdown as $row)
                    <tr>
                        <td>{{ $row->asset_type }}</td>
                        <td><span class="label label-{{ ['available'=>'success','deployed'=>'primary','under_maintenance'=>'warning','retrieved'=>'default'][$row->status] ?? 'default' }}">{{ ucwords(str_replace('_',' ',$row->status)) }}</span></td>
                        <td>{{ $row->total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Dealer compliance table --}}
    <div class="box box-default">
        <div class="box-header with-border"><h3 class="box-title">Customer Compliance Summary</h3></div>
        <div class="box-body">
            <table class="table table-bordered table-striped dataTable">
                <thead><tr><th>Outlet</th><th>Channel</th><th>Area</th><th>Coolers</th><th>Agreements</th><th>Compliance Score</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @foreach($dealers as $dealer)
                    <tr>
                        <td>{{ $dealer->outlet_name }}</td>
                        <td>{{ ucfirst($dealer->channel) }}</td>
                        <td>{{ $dealer->area ?? '—' }}</td>
                        <td>{{ $dealer->coolers_count }}</td>
                        <td>{{ $dealer->agreements_count }}</td>
                        <td>
                            @php $s = $dealer->compliance_score; $c = $s >= 80 ? 'success' : ($s >= 50 ? 'warning' : 'danger'); @endphp
                            <div class="progress tw-mb-0" style="height:16px;">
                                <div class="progress-bar progress-bar-{{ $c }}" style="width:{{ $s }}%">{{ $s }}%</div>
                            </div>
                        </td>
                        <td>{!! $dealer->status_badge !!}</td>
                        <td><a href="{{ route('cooler.dealers.show', $dealer->id) }}" class="btn btn-xs btn-info">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Retrieval reasons --}}
    <div class="box box-danger">
        <div class="box-header with-border"><h3 class="box-title">Retrieval Reasons Summary</h3></div>
        <div class="box-body">
            <table class="table table-bordered">
                <thead><tr><th>Reason</th><th>Total Retrievals</th></tr></thead>
                <tbody>
                    @foreach($retrievalsByReason as $row)
                    <tr>
                        <td>{{ \App\CoolerRetrieval::$reasons[$row->reason] ?? ucfirst(str_replace('_', ' ', $row->reason)) }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function () { $('.dataTable').DataTable(); });
</script>
@endsection
