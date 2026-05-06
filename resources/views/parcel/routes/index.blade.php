@extends('layouts.app')
@section('title', 'Parcel Routes')


@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')

<div class="page-modern">

    <section class="content-header"></section>

    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-route"></i>
                </div>
                <div>
                    <h1>Parcel Routes</h1>
                    <p class="pg-subtitle">{{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'All Routes'])
        @slot('tool')
            <div class="box-tools">
                <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full"
                    id="add_route_btn">
                    <i class="fa fa-plus"></i> Add Route
                </button>
            </div>
        @endslot

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="routes_table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Distance (km)</th>
                        <th>Base Price</th>
                        <th>Per Kg</th>
                        <th>Min Price</th>
                        <th>Express x</th>
                        <th>Transit Days</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>

<!-- Add / Edit Route Modal -->
<div class="modal fade" id="routeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="routeModalTitle">Add Route</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="route_modal_id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>From Town <span class="text-danger">*</span></label>
                            <input type="text" id="rm_from_town" class="form-control" placeholder="e.g. Nairobi" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>To Town <span class="text-danger">*</span></label>
                            <input type="text" id="rm_to_town" class="form-control" placeholder="e.g. Mombasa" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Distance (km)</label>
                            <input type="number" id="rm_distance" class="form-control" placeholder="0" min="0" step="1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Transit Days</label>
                            <input type="text" id="rm_transit_days" class="form-control" placeholder="e.g. 1-2 days">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Base Price (KES) <span class="text-danger">*</span></label>
                            <input type="number" id="rm_base_price" class="form-control" placeholder="0.00" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Price per Kg (KES) <span class="text-danger">*</span></label>
                            <input type="number" id="rm_price_per_kg" class="form-control" placeholder="0.00" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Minimum Price (KES) <span class="text-danger">*</span></label>
                            <input type="number" id="rm_min_price" class="form-control" placeholder="0.00" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Express Multiplier</label>
                            <input type="number" id="rm_express_multiplier" class="form-control" value="1.5" min="1" step="0.1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea id="rm_notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="rm_is_active" checked> Active
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Price preview -->
                <div class="alert alert-info tw-mt-2">
                    <strong>Price preview:</strong> For 5 kg → KES <span id="preview_price">0.00</span>
                    &nbsp;|&nbsp; Express 5 kg → KES <span id="preview_express">0.00</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveRouteBtn">Save Route</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
function previewPrice() {
    var base  = parseFloat($('#rm_base_price').val()) || 0;
    var pkg   = parseFloat($('#rm_price_per_kg').val()) || 0;
    var minP  = parseFloat($('#rm_min_price').val()) || 0;
    var expM  = parseFloat($('#rm_express_multiplier').val()) || 1.5;
    var p     = Math.max(base + pkg * 5, minP);
    $('#preview_price').text(p.toFixed(2));
    $('#preview_express').text((p * expM).toFixed(2));
}

$(function() {
    var table = $('#routes_table').DataTable({
        processing: true, serverSide: true,
        ajax: '{{ route("parcel-routes.index") }}',
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'from_town' },
            { data: 'to_town' },
            { data: 'distance_km' },
            { data: 'base_price' },
            { data: 'price_per_kg' },
            { data: 'min_price' },
            { data: 'express_multiplier' },
            { data: 'transit_days' },
            { data: 'is_active', orderable: false },
        ]
    });

    $('#rm_base_price, #rm_price_per_kg, #rm_min_price, #rm_express_multiplier').on('keyup change', previewPrice);

    $('#add_route_btn').on('click', function() {
        $('#routeModalTitle').text('Add Route');
        $('#route_modal_id').val('');
        $('#rm_from_town, #rm_to_town, #rm_distance, #rm_transit_days, #rm_notes').val('');
        $('#rm_base_price, #rm_price_per_kg, #rm_min_price').val('');
        $('#rm_express_multiplier').val('1.5');
        $('#rm_is_active').prop('checked', true);
        previewPrice();
        $('#routeModal').modal('show');
    });

    $(document).on('click', '.edit-route-btn', function() {
        var id = $(this).data('id');
        $.get('{{ route("parcel-routes.index") }}/' + id, function(r) {
            $('#routeModalTitle').text('Edit Route');
            $('#route_modal_id').val(r.id);
            $('#rm_from_town').val(r.from_town);
            $('#rm_to_town').val(r.to_town);
            $('#rm_distance').val(r.distance_km);
            $('#rm_transit_days').val(r.transit_days);
            $('#rm_base_price').val(r.base_price);
            $('#rm_price_per_kg').val(r.price_per_kg);
            $('#rm_min_price').val(r.min_price);
            $('#rm_express_multiplier').val(r.express_multiplier);
            $('#rm_notes').val(r.notes);
            $('#rm_is_active').prop('checked', r.is_active);
            previewPrice();
            $('#routeModal').modal('show');
        });
    });

    $('#saveRouteBtn').on('click', function() {
        var id   = $('#route_modal_id').val();
        var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            from_town: $('#rm_from_town').val(),
            to_town: $('#rm_to_town').val(),
            distance_km: $('#rm_distance').val(),
            transit_days: $('#rm_transit_days').val(),
            base_price: $('#rm_base_price').val(),
            price_per_kg: $('#rm_price_per_kg').val(),
            min_price: $('#rm_min_price').val(),
            express_multiplier: $('#rm_express_multiplier').val(),
            notes: $('#rm_notes').val(),
            is_active: $('#rm_is_active').is(':checked') ? 1 : 0,
        };
        var url    = id ? '{{ route("parcel-routes.index") }}/' + id : '{{ route("parcel-routes.store") }}';
        var method = id ? 'PUT' : 'POST';
        if (method === 'PUT') data['_method'] = 'PUT';

        $.ajax({ url: url, type: 'POST', data: data,
            success: function(r) {
                if (r.success) {
                    toastr.success(r.msg);
                    $('#routeModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error(r.msg || 'Error saving route.');
                }
            },
            error: function(xhr) { toastr.error('Validation error. Check all required fields.'); }
        });
    });

    $(document).on('click', '.delete-route-btn', function() {
        var id = $(this).data('id');
        if (confirm('Delete this route?')) {
            $.ajax({
                url: '{{ route("parcel-routes.index") }}/' + id,
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                success: function(r) { toastr.success(r.msg); table.ajax.reload(); }
            });
        }
    });
});
</script>

</div>{{-- .page-modern --}}
@endsection