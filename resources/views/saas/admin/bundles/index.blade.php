@extends('layouts.app')
@section('title', 'SaaS Bundles')
@section('content')
<section class="content-header">
    <h1>Bundles
        <a href="{{ route('saas.admin.bundles.create') }}" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> Add Bundle</a>
    </h1>
</section>
<section class="content">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

@component('components.widget', ['header' => 'All Bundles'])
<table class="table table-bordered table-hover">
    <thead>
        <tr><th>Name</th><th>Slug</th><th>Features</th><th>Active</th><th>Actions</th></tr>
    </thead>
    <tbody>
    @forelse($bundles as $bundle)
    <tr>
        <td>
            <strong>{{ $bundle->name }}</strong><br>
            <small class="text-muted">{{ $bundle->description }}</small>
        </td>
        <td><code>{{ $bundle->slug }}</code></td>
        <td>
            @foreach($bundle->features as $f)
                <span class="label label-default">{{ $f->name }}</span>
            @endforeach
        </td>
        <td>@if($bundle->is_active)<span class="label label-success">Yes</span>@else<span class="label label-danger">No</span>@endif</td>
        <td>
            <a href="{{ route('saas.admin.bundles.edit', $bundle) }}" class="btn btn-xs btn-info">Edit</a>
            <form method="POST" action="{{ route('saas.admin.bundles.destroy', $bundle) }}" style="display:inline;" onsubmit="return confirm('Delete this bundle?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger">Delete</button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-muted">No bundles yet. <a href="{{ route('saas.admin.bundles.create') }}">Create one</a>.</td></tr>
    @endforelse
    </tbody>
</table>
@endcomponent
</section>
@endsection
