@extends('layouts.app')
@section('title', 'Manage Features')
@section('content')
<section class="content-header">
    <h1 class="tw-text-2xl tw-font-bold">Manage Features
        <small><a href="{{ route('saas.admin.features.create') }}" class="btn btn-primary btn-sm" style="margin-left:12px;"><i class="fa fa-plus"></i> Add Feature</a></small>
    </h1>
</section>
<section class="content">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

@foreach($features as $category => $items)
@component('components.widget', ['header' => ucfirst($category) . ' Features'])
<table class="table table-bordered table-hover">
    <thead><tr><th>Name</th><th>Key</th><th>Monthly</th><th>Quarterly</th><th>Yearly</th><th>One-Off</th><th>Required</th><th>Active</th><th>Actions</th></tr></thead>
    <tbody>
    @foreach($items as $f)
    <tr>
        <td>{{ $f->name }}</td>
        <td><code>{{ $f->key }}</code></td>
        <td>KES {{ number_format($f->price_monthly, 0) }}</td>
        <td>KES {{ number_format($f->price_quarterly, 0) }}</td>
        <td>KES {{ number_format($f->price_yearly, 0) }}</td>
        <td>KES {{ number_format($f->price_once, 0) }}</td>
        <td>{!! $f->is_required ? '<span class="label label-primary">Yes</span>' : '—' !!}</td>
        <td>{!! $f->is_active ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>' !!}</td>
        <td>
            <a href="{{ route('saas.admin.features.edit', $f) }}" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
            <form method="POST" action="{{ route('saas.admin.features.destroy', $f) }}" style="display:inline;" onsubmit="return confirm('Delete this feature?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@endcomponent
@endforeach
</section>
@endsection
