@extends('layouts.app')
@section('title', 'Cooler Compliance Dashboard')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Cooler Management Dashboard</h1>
</section>

<section class="content">
    {{-- Asset summary --}}
    <div class="row">
        @foreach(['available' => ['success','fa-check-circle','Available'], 'deployed' => ['primary','fa-truck','Deployed'], 'under_maintenance' => ['warning','fa-wrench','Maintenance'], 'retrieved' => ['default','fa-inbox','Retrieved']] as $status => [$color, $icon, $label])
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-{{ $color }}"><i class="fa {{ $icon }}"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ $label }}</span>
                    <span class="info-box-number">{{ $assetStats[$status] ?? 0 }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        {{-- Dealer stats --}}
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Customers</span>
                    <span class="info-box-number">{{ $dealerStats['active'] }} / {{ $dealerStats['total'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fa fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Low Compliance</span>
                    <span class="info-box-number">{{ $dealerStats['low_score'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Critical Customers</span>
                    <span class="info-box-number">{{ $dealerStats['critical'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-calendar-times-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Expired Docs</span>
                    <span class="info-box-number">{{ $expiredDocs }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Expiring documents --}}
        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-clock-o"></i> Documents Expiring in 30 Days ({{ $expiringDocs->count() }})</h3>
                </div>
                <div class="box-body" style="max-height:300px;overflow-y:auto;">
                    @forelse($expiringDocs as $doc)
                    <div class="tw-flex tw-justify-between tw-items-center tw-py-2 tw-border-b">
                        <div>
                            <strong>{{ $doc->documentable->outlet_name ?? $doc->documentable->name ?? 'Unknown' }}</strong>
                            <br><small class="text-muted">{{ \App\CoolerDocument::$typeLabels[$doc->document_type] ?? $doc->document_type }}</small>
                        </div>
                        <span class="label {{ $doc->isExpired() ? 'label-danger' : 'label-warning' }}">
                            {{ $doc->expires_at->format('d M Y') }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted tw-py-4 tw-text-center">No documents expiring soon.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Non-compliant dealers --}}
        <div class="col-md-6">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Recent Non-Compliance Issues</h3>
                </div>
                <div class="box-body" style="max-height:300px;overflow-y:auto;">
                    @forelse($nonCompliantLogs as $log)
                    <div class="tw-flex tw-justify-between tw-items-center tw-py-2 tw-border-b">
                        <div>
                            <strong>{{ $log->dealer->outlet_name ?? '—' }}</strong>
                            <br><small class="text-muted">{{ \App\CoolerComplianceLog::$checkTypes[$log->check_type] ?? $log->check_type }}</small>
                        </div>
                        <div class="text-right">
                            {!! $log->status_badge !!}
                            <br><small class="text-muted">{{ $log->checked_at->format('d M') }}</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted tw-py-4 tw-text-center">No non-compliance issues found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Retrieval reasons chart (simple table) --}}
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Retrieval Reasons Breakdown</h3></div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <thead><tr><th>Reason</th><th>Count</th></tr></thead>
                        <tbody>
                            @forelse($retrievalReasons as $reason => $count)
                            <tr>
                                <td>{{ \App\CoolerRetrieval::$reasons[$reason] ?? ucfirst(str_replace('_', ' ', $reason)) }}</td>
                                <td><span class="badge bg-red">{{ $count }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-muted text-center">No retrievals yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent retrievals --}}
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Recent Retrievals</h3>
                    <div class="box-tools"><a href="{{ route('cooler.retrievals.index') }}" class="btn btn-xs btn-default">View All</a></div>
                </div>
                <div class="box-body">
                    @forelse($recentRetrievals as $r)
                    <div class="tw-flex tw-justify-between tw-items-center tw-py-2 tw-border-b">
                        <div>
                            <strong>{{ $r->dealer->outlet_name ?? '—' }}</strong>
                            <br><small class="text-muted">{{ $r->cooler->asset_number ?? '—' }} — {{ $r->retrieval_date->format('d M Y') }}</small>
                        </div>
                        <div class="text-right">
                            {!! $r->status_badge !!}
                            <br><a href="{{ route('cooler.retrievals.show', $r->id) }}" class="btn btn-xs btn-info">View</a>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted tw-py-4 tw-text-center">No retrievals yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
