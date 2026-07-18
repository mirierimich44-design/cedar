{{-- Purchase table: classic multi-column layout, well spaced (not sparse slim) --}}
<style>
/* Search strip */
.purchase-search-strip,
#add_purchase_form .tw-sticky {
    border-radius: 10px;
}

/* Table base */
#purchase_entry_table {
    width: 100%;
    min-width: 980px;
    border-collapse: separate;
    border-spacing: 0;
}
#purchase_entry_table > thead > tr > th {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #475569;
    background: #f8fafc !important;
    border-bottom: 2px solid #e2e8f0 !important;
    vertical-align: middle !important;
    padding: 12px 10px !important;
    white-space: nowrap;
}
#purchase_entry_table > tbody > tr > td {
    vertical-align: middle !important;
    padding: 12px 10px !important;
    border-color: #eef2f7 !important;
}
#purchase_entry_table > tbody > tr:hover {
    background: #f8fafc;
}

/* Product column: left-aligned, readable */
#purchase_entry_table td:nth-child(2),
#purchase_entry_table th:nth-child(2) {
    text-align: left !important;
    min-width: 180px;
    max-width: 280px;
}
#purchase_entry_table td:nth-child(2) strong,
#purchase_entry_table .purchase-product-name {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    display: block;
    line-height: 1.35;
}
#purchase_entry_table .details-summary-badge {
    margin-top: 6px;
}
#purchase_entry_table .details-summary-badge .label {
    font-size: 10px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 10px;
    margin: 2px 3px 0 0;
    display: inline-block;
}
#purchase_entry_table .details-sell-badge { background: #06b6d4 !important; }
#purchase_entry_table .details-lot-badge  { background: #f59e0b !important; color: #fff !important; }
#purchase_entry_table .details-exp-badge  { background: #64748b !important; color: #fff !important; }

/* Inputs: even height & spacing */
#purchase_entry_table .purchase_quantity,
#purchase_entry_table .purchase_unit_cost_without_discount,
#purchase_entry_table .purchase_unit_cost,
#purchase_entry_table .inline_discounts,
#purchase_entry_table .row_tax_percent,
#purchase_entry_table .profit_percent,
#purchase_entry_table .default_sell_price {
    height: 38px;
    border-radius: 8px;
    border-color: #cbd5e1;
    font-weight: 600;
    font-size: 13px;
    padding: 6px 10px;
}
#purchase_entry_table .purchase_quantity {
    text-align: center;
    max-width: 90px;
    margin: 0 auto;
}
#purchase_entry_table .purchase_unit_cost_without_discount,
#purchase_entry_table .purchase_unit_cost,
#purchase_entry_table .default_sell_price {
    text-align: right;
    min-width: 100px;
}
#purchase_entry_table .inline_discounts,
#purchase_entry_table .row_tax_percent,
#purchase_entry_table .profit_percent {
    text-align: center;
    width: 100%;
    min-width: 72px;
    max-width: 100%;
}
/* Tax/Disc: full cell width — no % addon squeezing the box */
#purchase_entry_table td .row_tax_percent,
#purchase_entry_table td .inline_discounts {
    display: block;
    box-sizing: border-box;
}

/* Money columns */
#purchase_entry_table .row_subtotal_before_tax,
#purchase_entry_table .row_subtotal_after_tax {
    font-weight: 700;
    font-size: 13px;
    white-space: nowrap;
}
#purchase_entry_table .row_subtotal_after_tax {
    color: #1d4ed8;
}

/* Details button */
#purchase_entry_table .btn-purchase-details {
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
#purchase_entry_table .btn-purchase-details.btn-success {
    background: #16a34a;
    border-color: #16a34a;
}
#purchase_entry_table .remove_purchase_entry_row {
    font-size: 18px;
    padding: 4px 8px;
}

/* Tighter header meta */
.purchase-meta-card .form-group { margin-bottom: 12px; }
.purchase-meta-card #supplier_address_div {
    font-size: 12px;
    color: #64748b;
    line-height: 1.35;
    max-height: 2.8em;
    overflow: hidden;
}

/* ── Sticky column headers (float while scrolling many lines) ──
   Scroll lives on .purchase-lines-scroll so thead sticky works. */
.purchase-lines-scroll {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    max-height: min(62vh, 720px);
    overflow: auto;
    -webkit-overflow-scrolling: touch;
    background: #fff;
    position: relative;
}
.purchase-lines-scroll > #purchase_entry_table,
.purchase-lines-scroll > .table {
    margin-bottom: 0;
}
#purchase_entry_table > thead > tr > th {
    position: sticky;
    top: 0;
    z-index: 20;
    background: #f1f5f9 !important;
    box-shadow: 0 2px 0 #e2e8f0, 0 4px 10px rgba(15, 23, 42, 0.06);
}
/* Keep first product column readable while scrolling horizontally */
#purchase_entry_table > thead > tr > th:nth-child(2) {
    z-index: 21;
    left: 0; /* only sticks vertically unless we also sticky left on tbody */
}
/* Optional: pin # column slightly when scrolling sideways */
#purchase_entry_table > thead > tr > th:first-child {
    z-index: 22;
    left: 0;
}

/* Fallback if wrapper class missing (create/edit older markup) */
#add_purchase_form .table-responsive:has(#purchase_entry_table) {
    max-height: min(62vh, 720px);
    overflow: auto;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}
</style>
