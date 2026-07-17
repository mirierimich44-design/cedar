@extends('layouts.app')
@section('title', 'Audit activity log')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.au-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden}
.au-sec h3{margin:0;padding:12px 16px;font-size:14px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.au-sec .body{padding:12px 16px;overflow-x:auto}
table.au{width:100%;font-size:12px;border-collapse:collapse}table.au th,table.au td{padding:7px 8px;border-bottom:1px solid #f1f5f9;text-align:left;vertical-align:top}
table.au th{font-size:11px;text-transform:uppercase;color:#64748b}
.au-props{max-width:280px;font-size:11px;color:#64748b;word-break:break-word}
</style>
@endsection

@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-user-secret"></i></div>
                <div>
                    <h1>Audit activity log</h1>
                    <p class="pg-subtitle">Who changed what — filter &amp; export CSV · {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important"><i class="fa fa-th"></i> All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        <form method="get" class="no-print" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;align-items:flex-end">
            <div>
                <label style="font-size:12px;font-weight:700;display:block">From</label>
                <input type="date" name="start_date" value="{{ $start }}" class="form-control">
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">To</label>
                <input type="date" name="end_date" value="{{ $end }}" class="form-control">
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">User</label>
                <select name="user_id" class="form-control select2" style="min-width:180px">
                    <option value="">All users</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" @if((string)$user_id === (string)$id) selected @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">Log name</label>
                <select name="log_name" class="form-control" style="min-width:140px">
                    <option value="">All</option>
                    @foreach($logNames as $ln)
                        <option value="{{ $ln }}" @if($log_name === $ln) selected @endif>{{ $ln }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Load</button>
            <a class="btn btn-success" href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"><i class="fa fa-download"></i> Export CSV</a>
            <button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
        </form>
        @includeIf('report.partials.export_toolbar', ['table' => '#au_export_table', 'title' => 'Audit log'])

        <div class="au-sec">
            <h3>{{ $rows->count() }} events (max 3000)</h3>
            <div class="body">
                <table class="au" id="au_export_table">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>User</th>
                            <th>Log</th>
                            <th>Description</th>
                            <th>Subject</th>
                            <th>Properties</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->created_at }}</td>
                            <td>{{ $r->user_name ?: '—' }}</td>
                            <td>{{ $r->log_name }}</td>
                            <td>{{ $r->description }}</td>
                            <td>{{ class_basename((string)$r->subject_type) }} #{{ $r->subject_id }}</td>
                            <td class="au-props">{{ \Illuminate\Support\Str::limit(is_string($r->properties) ? $r->properties : json_encode($r->properties), 180) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">No activity for this filter (or activity_log table empty)</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
