@extends('layouts.app')
@section('title', __('Stocktake'))

@section('content')
<section class="content-header">
    <h1>@lang('Stocktake')
        <small>@lang('Physical Inventory Count')</small>
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('All Stocktakes')</h3>
            <div class="box-tools">
                <a class="btn btn-primary" href="{{ action([\App\Http\Controllers\StocktakeController::class, 'create']) }}">
                    <i class="fa fa-plus"></i> @lang('New Stocktake')
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('filter_location', __('business.location') . ':') !!}
                        {!! Form::select('filter_location', $locations, null, [
                            'class' => 'form-control select2',
                            'placeholder' => __('lang_v1.all'),
                            'id' => 'filter_location'
                        ]) !!}
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped" id="stocktake_table">
                <thead>
                    <tr>
                        <th>@lang('Ref No')</th>
                        <th>@lang('Location')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var stocktake_table = $('#stocktake_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ action([\App\Http\Controllers\StocktakeController::class, "index"]) }}',
            data: function(d) {
                d.location_id = $('#filter_location').val();
            }
        },
        columns: [
            { data: 'ref_no', name: 'ref_no' },
            { data: 'location', name: 'location' },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $('#filter_location').change(function() {
        stocktake_table.ajax.reload();
    });
});
</script>
@endsection
