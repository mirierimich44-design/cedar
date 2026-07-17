@extends('layouts.app')
@section('title', 'Roles & permissions')
@section('css')
@include('layouts.partials.page_modern_css')
<style>
.rg-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:14px;overflow:hidden}
.rg-sec h3{margin:0;padding:12px 16px;font-size:14px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.rg-sec .body{padding:14px 16px}
table.rg{width:100%;font-size:13px;border-collapse:collapse}table.rg th,table.rg td{padding:8px;border-bottom:1px solid #f1f5f9;text-align:left;vertical-align:top}
table.rg th{font-size:11px;text-transform:uppercase;color:#64748b}
.rg-note{font-size:12px;color:#64748b}
</style>
@endsection
@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-user-shield"></i></div>
                <div>
                    <h1>Roles &amp; permissions</h1>
                    <p class="pg-subtitle">Who should see finance vs floor tools · set discount caps · {{ session('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="rg-sec">
            <h3>Recommended matrix</h3>
            <div class="body">
                <table class="rg">
                    <thead><tr><th>Role</th><th>Can</th><th>Should not</th><th>Key perms</th></tr></thead>
                    <tbody>
                    @foreach($matrix as $m)
                        <tr>
                            <td><strong>{{ $m['role'] }}</strong></td>
                            <td>{{ $m['can'] }}</td>
                            <td>{{ $m['cannot'] }}</td>
                            <td><code style="font-size:11px">{{ implode(', ', $m['perms']) }}</code></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <p class="rg-note" style="margin-top:10px">Edit roles under <strong>User management → Roles</strong>. Finance reports need <code>profit_loss_report.view</code> or <code>account.access</code>.</p>
            </div>
        </div>
        <div class="rg-sec">
            <h3>User max discount % (live)</h3>
            <div class="body">
                <p class="rg-note">POS blocks invoice discounts above this % (enforced on sell). Empty = no cap (risky).</p>
                <table class="rg">
                    <thead><tr><th>User</th><th>Username</th><th>Max discount %</th></tr></thead>
                    <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>{{ trim($u->first_name.' '.$u->last_name) }}</td>
                            <td>{{ $u->username }}</td>
                            <td>
                                @if($u->max_sales_discount_percent === null || $u->max_sales_discount_percent === '')
                                    <span class="text-danger">No cap</span>
                                @else
                                    {{ $u->max_sales_discount_percent }}%
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
