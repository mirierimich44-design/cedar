@extends('layouts.app')
@section('title', 'Add Route')

@section('content')
<section class="content-header">
    <h1>Add Route <small>Set up a route with pricing</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ route('parcel.routes.index') }}">Routes</a></li>
        <li class="active">Add Route</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <form method="POST" action="{{ route('parcel.routes.store') }}">
                @csrf

                <div class="box box-primary">
                    <div class="box-header with-border"><h3 class="box-title">Route Details</h3></div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Origin Station <span class="text-red">*</span></label>
                                    <select name="origin_station_id" class="form-control select2" required>
                                        <option value="">-- Select origin --</option>
                                        @foreach($stations as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Destination Station <span class="text-red">*</span></label>
                                    <select name="destination_station_id" class="form-control select2" required>
                                        <option value="">-- Select destination --</option>
                                        @foreach($stations as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Base Price per kg (KES) <span class="text-red">*</span></label>
                                    <input type="number" name="base_price_per_kg" class="form-control" step="0.01" min="0" required placeholder="e.g. 50">
                                    <span class="help-block">Used when no pricing rule matches</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Minimum Charge (KES) <span class="text-red">*</span></label>
                                    <input type="number" name="min_price" class="form-control" step="0.01" min="0" required placeholder="e.g. 200">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Estimated Transit (hours)</label>
                                    <input type="number" name="estimated_hours" class="form-control" min="1" placeholder="e.g. 6">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="create_reverse" value="1">
                                    Also create reverse route (Destination → Origin) with same pricing
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Weight-Based Pricing Rules <small>(optional)</small></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-success btn-sm" id="add_rule_row">
                                <i class="fa fa-plus"></i> Add Rule
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <p class="text-muted">Define tiered pricing for different weight ranges. If a parcel's weight falls in a range, that rule is used instead of the base rate.</p>
                        <table class="table table-bordered" id="pricing_table">
                            <thead>
                                <tr class="bg-gray">
                                    <th>Weight From (kg)</th>
                                    <th>Weight To (kg)</th>
                                    <th>Per kg Rate (KES)</th>
                                    <th>Flat Fee (KES)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="rule_rows">
                                <tr class="rule-row">
                                    <td><input type="number" name="rules[0][weight_min_kg]" class="form-control input-sm" value="0" step="0.1" min="0"></td>
                                    <td><input type="number" name="rules[0][weight_max_kg]" class="form-control input-sm" step="0.1" min="0.1" placeholder="e.g. 5"></td>
                                    <td><input type="number" name="rules[0][price_per_kg]" class="form-control input-sm" step="0.01" value="0"></td>
                                    <td><input type="number" name="rules[0][flat_fee]" class="form-control input-sm" step="0.01" value="0" placeholder="e.g. 200"></td>
                                    <td><button type="button" class="btn btn-danger btn-xs remove_rule"><i class="fa fa-times"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="text-muted small"><strong>Example:</strong> 0–5kg: flat KES 200 | 5–20kg: KES 45/kg | 20–50kg: KES 35/kg</p>
                    </div>
                </div>

                <div class="box-footer text-right">
                    <a href="{{ route('parcel.routes.index') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Route</button>
                </div>
            </form>
        </div>
    </div>
</section>

@section('javascript')
<script>
var rule_idx = 1;
$('#add_rule_row').click(function() {
    var html = '<tr class="rule-row">' +
        '<td><input type="number" name="rules['+rule_idx+'][weight_min_kg]" class="form-control input-sm" value="0" step="0.1" min="0"></td>' +
        '<td><input type="number" name="rules['+rule_idx+'][weight_max_kg]" class="form-control input-sm" step="0.1" min="0.1"></td>' +
        '<td><input type="number" name="rules['+rule_idx+'][price_per_kg]" class="form-control input-sm" step="0.01" value="0"></td>' +
        '<td><input type="number" name="rules['+rule_idx+'][flat_fee]" class="form-control input-sm" step="0.01" value="0"></td>' +
        '<td><button type="button" class="btn btn-danger btn-xs remove_rule"><i class="fa fa-times"></i></button></td>' +
        '</tr>';
    $('#rule_rows').append(html);
    rule_idx++;
});
$(document).on('click', '.remove_rule', function() { $(this).closest('tr').remove(); });
</script>
@endsection
@endsection
