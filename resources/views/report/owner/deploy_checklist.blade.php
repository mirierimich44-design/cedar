@extends('layouts.app')
@section('title', 'Deploy checklist')
@section('css')
@include('layouts.partials.page_modern_css')
<style>
.ck-ok{color:#047857;font-weight:700}.ck-bad{color:#b91c1c;font-weight:700}
.ck-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin-bottom:14px}
.ck-sec h3{margin:0 0 10px;font-size:14px;font-weight:800}
.ck-ol{padding-left:18px;font-size:13px;line-height:1.6}
table.ck{width:100%;font-size:13px;border-collapse:collapse}table.ck td,table.ck th{padding:8px;border-bottom:1px solid #f1f5f9;text-align:left}
</style>
@endsection
@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-rocket"></i></div>
                <div>
                    <h1>Deploy checklist</h1>
                    <p class="pg-subtitle">Upload files → clear cache → smoke-test · {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important">All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="ck-sec">
            <h3>1. Upload these paths into <code>public_html/pos/</code></h3>
            <ol class="ck-ol">
                <li><code>app/Http/Controllers/AdvancedReportsController.php</code></li>
                <li><code>app/Http/Controllers/OwnerOpsController.php</code></li>
                <li><code>app/Http/Controllers/ReportsHubController.php</code></li>
                <li><code>app/Http/Middleware/AdminSidebarMenu.php</code></li>
                <li><code>routes/web.php</code> and <code>routes/api.php</code></li>
                <li><code>resources/views/report/</code> (hub, advanced, owner, partials)</li>
                <li><code>resources/views/home/partials/owner_ops_strip.blade.php</code></li>
                <li>Optional: <code>pos-app/</code> OTA for mobile owner pack</li>
            </ol>
        </div>
        <div class="ck-sec">
            <h3>2. After upload (SSH / Terminal)</h3>
            <pre style="background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px;font-size:12px">cd ~/domains/liyonpharmacy.co.ke/public_html/pos
php artisan view:clear
php artisan route:clear
php artisan cache:clear
php artisan config:clear</pre>
            <p style="font-size:13px;color:#64748b">Or delete <code>storage/framework/views/*</code> in File Manager.</p>
        </div>
        <div class="ck-sec">
            <h3>3. Live file presence on this server</h3>
            <table class="ck">
                <thead><tr><th>Check</th><th>Status</th><th>Updated</th></tr></thead>
                <tbody>
                @foreach($checks as $c)
                    <tr>
                        <td><code>{{ $c['path'] }}</code></td>
                        <td class="{{ $c['ok'] ? 'ck-ok' : 'ck-bad' }}">{{ $c['ok'] ? 'OK' : 'MISSING' }}</td>
                        <td>{{ $c['mtime'] ?: '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="ck-sec">
            <h3>4. Smoke test (5 minutes)</h3>
            <ol class="ck-ol">
                <li><a href="{{ route('reports.day_close') }}">Day close</a> loads without 500</li>
                <li><a href="{{ route('reports.month_end_pack') }}">Month-end pack</a> prints</li>
                <li><a href="{{ route('reports.bank_mpesa_recon') }}">Bank / M-Pesa recon</a> loads</li>
                <li>Home shows owner strip (if you have dashboard permission)</li>
                <li>Mobile admin dashboard shows owner KPIs after OTA</li>
            </ol>
        </div>
    </section>
</div>
@endsection
