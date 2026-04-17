@extends('layouts.app')
@section('title', 'Subscriptions')
@section('content')
<section class="content-header"><h1>Subscriptions</h1></section>
<section class="content">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

@component('components.widget', ['header' => 'All Subscriptions'])
<form method="GET" class="row" style="margin-bottom:16px;">
    <div class="col-md-3"><select name="status" class="form-control"><option value="">All Statuses</option><option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option><option value="grace" {{ request('status') == 'grace' ? 'selected' : '' }}>Grace</option><option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option><option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option></select></div>
    <div class="col-md-3"><select name="cycle" class="form-control"><option value="">All Cycles</option><option value="monthly">Monthly</option><option value="quarterly">Quarterly</option><option value="yearly">Yearly</option><option value="once">One-Off</option></select></div>
    <div class="col-md-2"><button class="btn btn-default btn-block">Filter</button></div>
</form>
<table class="table table-bordered table-hover">
    <thead><tr><th>Business</th><th>Features</th><th>Cycle</th><th>Amount</th><th>Status</th><th>Expires</th><th>Actions</th></tr></thead>
    <tbody>
    @foreach($subscriptions as $sub)
    <tr>
        <td>{{ optional($sub->business)->name ?? '—' }}</td>
        <td>{{ $sub->features->pluck('name')->join(', ') }}</td>
        <td>{{ ucfirst($sub->billing_cycle) }}</td>
        <td>KES {{ number_format($sub->total_amount, 0) }}</td>
        <td>
            @if($sub->status === 'active')<span class="label label-success">Active</span>
            @elseif($sub->status === 'grace')<span class="label label-warning">Grace</span>
            @elseif($sub->status === 'suspended')<span class="label label-danger">Suspended</span>
            @elseif($sub->status === 'pending')<span class="label label-info">Pending</span>
            @else<span class="label label-default">{{ $sub->status }}</span>@endif
        </td>
        <td>{{ $sub->ends_at ? $sub->ends_at->format('d M Y') : '—' }}</td>
        <td>
            <a href="{{ route('saas.admin.subscriptions.show', $sub) }}" class="btn btn-xs btn-info">View</a>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ $subscriptions->links() }}
@endcomponent
</section>
@endsection
