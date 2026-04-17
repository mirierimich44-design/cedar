@extends('layouts.app')
@section('title', 'Invoices')
@section('content')
<section class="content-header"><h1>SaaS Invoices</h1></section>
<section class="content">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@component('components.widget', ['header' => 'All Invoices'])
<table class="table table-bordered table-hover">
    <thead><tr><th>Invoice #</th><th>Business</th><th>Amount</th><th>Method</th><th>Status</th><th>Due</th><th>Paid</th><th>Actions</th></tr></thead>
    <tbody>
    @foreach($invoices as $inv)
    <tr>
        <td><strong>{{ $inv->invoice_no }}</strong></td>
        <td>{{ optional($inv->business)->name ?? '—' }}</td>
        <td>{{ $inv->currency }} {{ number_format($inv->amount, 0) }}</td>
        <td>{{ $inv->payment_method ?? '—' }}</td>
        <td>
            @if($inv->status === 'paid')<span class="label label-success">Paid</span>
            @elseif($inv->status === 'unpaid')<span class="label label-warning">Unpaid</span>
            @elseif($inv->status === 'failed')<span class="label label-danger">Failed</span>
            @else<span class="label label-default">{{ $inv->status }}</span>@endif
        </td>
        <td>{{ $inv->due_at ? $inv->due_at->format('d M Y') : '—' }}</td>
        <td>{{ $inv->paid_at ? $inv->paid_at->format('d M Y') : '—' }}</td>
        <td>
            @if($inv->status !== 'paid')
            <form method="POST" action="{{ route('saas.admin.invoices.mark_paid', $inv) }}" style="display:inline;">
                @csrf
                <input type="hidden" name="payment_method" value="manual">
                <button class="btn btn-xs btn-success" onclick="return confirm('Mark as paid?')">Mark Paid</button>
            </form>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ $invoices->links() }}
@endcomponent
</section>
@endsection
