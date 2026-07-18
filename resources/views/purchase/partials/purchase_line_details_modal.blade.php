{{-- Shared: purchase line Details modal (create + edit) --}}
<div class="modal fade" id="purchase_line_details_modal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel">
    <div class="modal-dialog" role="document" style="max-width:520px;">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);padding:16px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:0.8;font-size:22px;">&times;</button>
                <h4 class="modal-title" id="detailsModalLabel" style="color:#fff;font-weight:700;font-size:16px;">
                    <i class="fa fa-pencil-square-o"></i>
                    Product Details &mdash; <span id="details_modal_product_name" style="font-weight:400;font-size:14px;"></span>
                </h4>
            </div>
            <div class="modal-body" style="padding:20px 24px;">
                <input type="hidden" id="details_modal_row">

                {{-- Sell Price + Margin% --}}
                @if(session('business.enable_editing_product_from_purchase'))
                <div class="row" style="margin-bottom:14px;" id="details_modal_sell_row">
                    <div class="col-sm-6">
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px;font-weight:700;color:#475569;letter-spacing:.5px;">SELL PRICE (inc. Tax)</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                                <input type="number" id="modal_sell_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px;font-weight:700;color:#475569;letter-spacing:.5px;">MARGIN %</label>
                            <div class="input-group">
                                <span class="input-group-addon">%</span>
                                <input type="number" id="modal_margin" class="form-control" step="0.01" placeholder="e.g. 30">
                            </div>
                        </div>
                    </div>
                </div>
                <hr style="margin:10px 0 16px;" id="details_modal_sell_hr">
                @endif

                {{-- Lot Number --}}
                @if(session('business.enable_lot_number'))
                <div class="form-group" style="margin-bottom:14px;" id="details_modal_lot_group">
                    <label style="font-size:11px;font-weight:700;color:#475569;letter-spacing:.5px;">LOT NUMBER</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-barcode"></i></span>
                        <input type="text" id="modal_lot_number" class="form-control" placeholder="e.g. LOT-2025-001">
                    </div>
                </div>
                @endif

                {{-- MFG Date --}}
                @if(session('business.expiry_type') == 'add_manufacturing')
                <div class="form-group" style="margin-bottom:14px;" id="details_modal_mfg_group">
                    <label style="font-size:11px;font-weight:700;color:#475569;letter-spacing:.5px;">MANUFACTURING DATE</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="modal_mfg_date" class="form-control expiry_datepicker" readonly placeholder="Select date">
                    </div>
                </div>
                @endif

                {{-- EXP Date --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label style="font-size:11px;font-weight:700;color:#475569;letter-spacing:.5px;">EXPIRY DATE</label>
                    <div class="input-group">
                        <span class="input-group-addon" style="background:#fef2f2;"><i class="fa fa-calendar text-danger"></i></span>
                        <input type="text" id="modal_exp_date" class="form-control expiry_datepicker" readonly placeholder="Select date">
                    </div>
                    <small class="text-muted">Leave blank if not applicable.</small>
                </div>
            </div>
            <div class="modal-footer" style="background:#f8fafc;padding:14px 20px;display:flex;justify-content:space-between;align-items:center;">
                <small class="text-muted"><i class="fa fa-info-circle"></i> These fields are optional and saved with the purchase line.</small>
                <div>
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="margin-right:8px;">Cancel</button>
                    <button type="button" class="btn btn-success" id="btn_save_line_details" style="border-radius:6px;padding:7px 22px;font-weight:700;">
                        <i class="fa fa-check"></i> Save Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
