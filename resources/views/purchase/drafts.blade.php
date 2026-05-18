@extends('layouts.app')
@section('title', 'Purchase Drafts')

@section('content')
<section class="content-header">
    <div class="tw-flex tw-justify-between tw-items-center">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            <i class="fa fa-clock-o" style="color:var(--theme-main);margin-right:8px;"></i> Purchase Drafts
        </h1>
        <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}"
           class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> New Purchase
        </a>
    </div>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Saved Drafts</h3>
        </div>
        <div class="box-body">
            {{-- Empty state --}}
            <div id="drafts-empty" style="display:none;text-align:center;padding:48px 20px;color:#94a3b8;">
                <i class="fa fa-inbox" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                <p style="font-size:15px;font-weight:600;margin:0 0 6px;">No drafts saved</p>
                <p style="font-size:13px;margin:0 0 16px;">Drafts are auto-saved every 30 seconds while you fill in a purchase.</p>
                <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}"
                   class="btn btn-primary btn-sm">Start a Purchase</a>
            </div>

            {{-- Drafts table --}}
            <div id="drafts-table-wrap" style="display:none;">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>Saved At</th>
                            <th>Supplier</th>
                            <th>Ref No</th>
                            <th>Location</th>
                            <th>Products</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="drafts-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function () {
    var DRAFT_KEY = 'purchase_draft_{{ $business_id }}';
    var raw = localStorage.getItem(DRAFT_KEY);

    if (!raw) {
        $('#drafts-empty').show();
        return;
    }

    var draft;
    try { draft = JSON.parse(raw); } catch(e) { $('#drafts-empty').show(); return; }
    if (!draft || !draft.saved_at) { $('#drafts-empty').show(); return; }

    var rowCount = draft.rows ? draft.rows.length : 0;

    var $tbody = $('#drafts-tbody');
    $tbody.append(
        '<tr>' +
        '<td><strong>' + draft.saved_at + '</strong></td>' +
        '<td>' + (draft.supplier_id ? 'ID: ' + draft.supplier_id : '<span class="text-muted">—</span>') + '</td>' +
        '<td>' + (draft.ref_no || '<span class="text-muted">—</span>') + '</td>' +
        '<td>' + (draft.location_id ? 'Location ' + draft.location_id : '<span class="text-muted">—</span>') + '</td>' +
        '<td><span class="badge" style="background:var(--theme-main);color:#fff;">' + rowCount + ' product' + (rowCount !== 1 ? 's' : '') + '</span></td>' +
        '<td>' +
            '<a href="{{ action([\App\Http\Controllers\PurchaseController::class, "create"]) }}" class="btn btn-primary btn-xs" id="continue-draft-btn">' +
                '<i class="fa fa-pencil"></i> Continue' +
            '</a> ' +
            '<button class="btn btn-default btn-xs" id="delete-draft-btn">' +
                '<i class="fa fa-trash text-danger"></i> Delete' +
            '</button>' +
        '</td>' +
        '</tr>'
    );

    $('#drafts-table-wrap').show();

    $('#delete-draft-btn').on('click', function () {
        if (!confirm('Delete this draft?')) return;
        localStorage.removeItem(DRAFT_KEY);
        $(this).closest('tr').remove();
        if ($('#drafts-tbody tr').length === 0) {
            $('#drafts-table-wrap').hide();
            $('#drafts-empty').show();
        }
    });
});
</script>
@endsection
