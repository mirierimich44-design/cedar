{{-- Slim purchase UI — desktop table + mobile cards + sticky footer --}}
<style>
/* ── Meta header: tighter ─────────────────────────────────────── */
.purchase-meta-card .form-group { margin-bottom: 10px; }
.purchase-meta-card #supplier_address_div {
    font-size: 12px;
    color: #64748b;
    max-height: 2.6em;
    overflow: hidden;
    line-height: 1.3;
}
.purchase-meta-card #supplier_address_div:empty { display: none; }
.purchase-meta-card .purchase-addr-label { font-size: 11px; color: #94a3b8; font-weight: 600; }

/* ── Slim table ───────────────────────────────────────────────── */
#purchase_entry_table.purchase-slim {
    min-width: 0 !important;
    width: 100%;
    table-layout: auto;
}
#purchase_entry_table.purchase-slim > thead > tr > th {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #475569;
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
    vertical-align: middle;
    padding: 10px 8px;
}
#purchase_entry_table.purchase-slim > tbody > tr > td {
    vertical-align: middle;
    padding: 10px 8px;
    border-color: #eef2f7;
}
#purchase_entry_table.purchase-slim .purchase-product-name {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    display: block;
    line-height: 1.3;
}
#purchase_entry_table.purchase-slim .purchase-product-sku {
    font-size: 11px;
    color: #64748b;
}
#purchase_entry_table.purchase-slim .purchase-product-meta {
    font-size: 10px;
    color: #94a3b8;
    margin-top: 2px;
}
#purchase_entry_table.purchase-slim .details-summary-badge .label {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 10px;
    display: inline-block;
    margin: 2px 2px 0 0;
}
#purchase_entry_table.purchase-slim .details-sell-badge { background: #06b6d4 !important; }
#purchase_entry_table.purchase-slim .details-lot-badge  { background: #f59e0b !important; color: #fff !important; }
#purchase_entry_table.purchase-slim .details-exp-badge  { background: #64748b !important; color: #fff !important; }

#purchase_entry_table.purchase-slim .purchase_quantity,
#purchase_entry_table.purchase-slim .purchase_unit_cost_without_discount,
#purchase_entry_table.purchase-slim .purchase_unit_cost {
    font-weight: 700;
    font-size: 14px;
    height: 38px;
    text-align: center;
    border-radius: 8px;
    border-color: #cbd5e1;
}
#purchase_entry_table.purchase-slim .purchase_unit_cost_without_discount,
#purchase_entry_table.purchase-slim .purchase_unit_cost {
    text-align: right;
}
#purchase_entry_table.purchase-slim .row_subtotal_after_tax {
    font-size: 14px;
    font-weight: 700;
    color: #1d4ed8;
    white-space: nowrap;
}
#purchase_entry_table.purchase-slim .btn-purchase-details {
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
#purchase_entry_table.purchase-slim .btn-purchase-details.btn-success {
    background: #16a34a;
    border-color: #16a34a;
}
#purchase_entry_table.purchase-slim .remove_purchase_entry_row {
    font-size: 18px;
    padding: 6px;
}

/* Search bar strip */
.purchase-search-strip {
    background: #fff;
    border-radius: 10px;
    padding: 10px 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, .06);
    margin-bottom: 12px;
}
.purchase-search-strip .form-group { margin-bottom: 0; }

/* ── Sticky purchase footer ───────────────────────────────────── */
.purchase-sticky-footer {
    position: sticky;
    bottom: 0;
    z-index: 90;
    background: #fff;
    border-top: 1px solid #e2e8f0;
    box-shadow: 0 -6px 20px rgba(15, 23, 42, .08);
    padding: 12px 16px;
    margin: 16px -15px 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.purchase-sticky-footer .psf-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 16px 22px;
    align-items: baseline;
    font-size: 13px;
    color: #475569;
}
.purchase-sticky-footer .psf-stats strong {
    color: #0f172a;
    font-size: 14px;
}
.purchase-sticky-footer .psf-total {
    font-size: 22px;
    font-weight: 800;
    color: #1d4ed8;
    line-height: 1.1;
}
.purchase-sticky-footer .psf-total small {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.purchase-sticky-footer .psf-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* ── Mobile: card layout ──────────────────────────────────────── */
@media (max-width: 767px) {
    #purchase_entry_table.purchase-slim,
    #purchase_entry_table.purchase-slim thead,
    #purchase_entry_table.purchase-slim tbody,
    #purchase_entry_table.purchase-slim th,
    #purchase_entry_table.purchase-slim td,
    #purchase_entry_table.purchase-slim tr {
        display: block;
        width: 100%;
    }
    #purchase_entry_table.purchase-slim thead { display: none; }
    #purchase_entry_table.purchase-slim > tbody > tr {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 12px;
        padding: 12px 12px 8px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .05);
    }
    #purchase_entry_table.purchase-slim > tbody > tr > td {
        border: none !important;
        padding: 4px 0 !important;
        text-align: left !important;
    }
    #purchase_entry_table.purchase-slim > tbody > tr > td.hide,
    #purchase_entry_table.purchase-slim > tbody > tr > td[style*="display: none"] {
        display: none !important;
    }
    /* # column */
    #purchase_entry_table.purchase-slim td.ps-col-num {
        position: absolute;
        right: 14px;
        top: 10px;
        width: auto;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 700;
    }
    #purchase_entry_table.purchase-slim > tbody > tr { position: relative; }
    #purchase_entry_table.purchase-slim td.ps-col-product {
        padding-right: 36px !important;
        margin-bottom: 8px;
    }
    #purchase_entry_table.purchase-slim td.ps-col-qty,
    #purchase_entry_table.purchase-slim td.ps-col-cost,
    #purchase_entry_table.purchase-slim td.ps-col-total {
        display: inline-block !important;
        width: 32%;
        vertical-align: top;
        padding-right: 4px !important;
    }
    #purchase_entry_table.purchase-slim td.ps-col-qty::before,
    #purchase_entry_table.purchase-slim td.ps-col-cost::before,
    #purchase_entry_table.purchase-slim td.ps-col-total::before {
        content: attr(data-label);
        display: block;
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    #purchase_entry_table.purchase-slim td.ps-col-total {
        width: 34%;
        text-align: right !important;
        padding-top: 18px !important;
    }
    #purchase_entry_table.purchase-slim td.ps-col-details {
        display: block !important;
        margin-top: 10px;
    }
    #purchase_entry_table.purchase-slim td.ps-col-details .btn-purchase-details {
        width: 100%;
        padding: 10px;
    }
    #purchase_entry_table.purchase-slim td.ps-col-remove {
        text-align: center !important;
        margin-top: 4px;
        padding-bottom: 4px !important;
    }
    #purchase_entry_table.purchase-slim td.ps-col-remove .remove_purchase_entry_row::after {
        content: ' Remove';
        font-size: 12px;
        font-family: inherit;
        font-style: normal;
        font-weight: 600;
    }

    .purchase-sticky-footer {
        margin-left: -10px;
        margin-right: -10px;
        flex-direction: column;
        align-items: stretch;
    }
    .purchase-sticky-footer .psf-actions .btn,
    .purchase-sticky-footer .psf-actions button {
        flex: 1;
    }
    .purchase-sticky-footer .psf-total {
        text-align: center;
    }
    .purchase-sticky-footer .psf-stats {
        justify-content: center;
    }
}

/* Desktop: hide mobile-only bits */
@media (min-width: 768px) {
    #purchase_entry_table.purchase-slim td.ps-col-qty::before,
    #purchase_entry_table.purchase-slim td.ps-col-cost::before,
    #purchase_entry_table.purchase-slim td.ps-col-total::before {
        display: none;
    }
}
</style>
