@extends('layouts.app')
@section('title', 'Parcel Routes')

@section('content')
<section class="content-header">
    <h1>Routes & Pricing <small>Manage inter-station routes</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ route('parcel.index') }}">Parcels</a></li>
        <li class="active">Routes</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">All Routes ({{ $routes->count() }})</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('parcel.routes.create') }}" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i> Add Route
                        </a>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    @if($routes->isEmpty())
                    <div class="text-center text-muted" style="padding:40px;">
                        <i class="fa fa-road fa-3x"></i>
                        <h4>No routes yet</h4>
                        <p>Routes define how parcels travel between stations and how they are priced.</p>
                        <a href="{{ route('parcel.routes.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Create First Route
                        </a>
                    </div>
                    @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Origin → Destination</th>
                                <th>Base Rate (per kg)</th>
                                <th>Min Charge</th>
                                <th>Est. Time</th>
                                <th>Pricing Rules</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $route)
                            <tr>
                                <td>
                                    <strong>{{ $route->origin?->name ?? '?' }}</strong>
                                    <span class="text-muted"> ({{ $route->origin?->town }})</span>
                                    <i class="fa fa-arrow-right text-muted"></i>
                                    <strong>{{ $route->destination?->name ?? '?' }}</strong>
                                    <span class="text-muted"> ({{ $route->destination?->town }})</span>
                                </td>
                                <td>KES {{ number_format($route->base_price_per_kg, 2) }}/kg</td>
                                <td>KES {{ number_format($route->min_price, 2) }}</td>
                                <td>{{ $route->estimated_hours ? $route->estimated_hours.' hrs' : '—' }}</td>
                                <td>
                                    @if($route->pricing_rules->isNotEmpty())
                                        <span class="label label-info">{{ $route->pricing_rules->count() }} rules</span>
                                        <small class="text-muted">
                                            @foreach($route->pricing_rules->take(2) as $r)
                                                {{ $r->weight_min_kg }}–{{ $r->weight_max_kg }}kg: KES {{ $r->flat_fee > 0 ? number_format($r->flat_fee).' flat' : number_format($r->price_per_kg).'/kg' }}
                                            @endforeach
                                        </small>
                                    @else
                                        <span class="text-muted">Base rate only</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="label {{ $route->is_active ? 'label-success' : 'label-default' }}">
                                        {{ $route->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('parcel.routes.destroy', $route->id) }}" style="display:inline;"
                                          onsubmit="return confirm('Delete this route?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
