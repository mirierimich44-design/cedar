{{-- Purchase table: classic multi-column, well spaced + sticky headers (page scroll) --}}
<style>
/* Search strip */
.purchase-search-strip,
#add_purchase_form .tw-sticky {
    border-radius: 10px;
}

/* Table base — show ALL rows (no max-height clip) */
#purchase_entry_table {
    width: 100%;
    min-width: 980px;
    border-collapse: separate !important; /* sticky needs separate, not collapse */
    border-spacing: 0;
    margin-bottom: 0;
}
#purchase_entry_table > thead > tr > th {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #475569;
    background: #f1f5f9 !important;
    border-bottom: 2px solid #e2e8f0 !important;
    vertical-align: middle !important;
    padding: 12px 10px !important;
    white-space: nowrap;
}
#purchase_entry_table > tbody > tr > td {
    vertical-align: middle !important;
    padding: 12px 10px !important;
    border-color: #eef2f7 !important;
    background: #fff;
}
#purchase_entry_table > tbody > tr:nth-child(even) > td {
    background: #fafbfc;
}
#purchase_entry_table > tbody > tr:hover > td {
    background: #f1f5f9;
}

/* Product column: left-aligned */
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

/* Inputs */
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
    display: block;
    box-sizing: border-box;
}

#purchase_entry_table .row_subtotal_before_tax,
#purchase_entry_table .row_subtotal_after_tax {
    font-weight: 700;
    font-size: 13px;
    white-space: nowrap;
}
#purchase_entry_table .row_subtotal_after_tax {
    color: #1d4ed8;
}

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

.purchase-meta-card .form-group { margin-bottom: 12px; }
.purchase-meta-card #supplier_address_div {
    font-size: 12px;
    color: #64748b;
    line-height: 1.35;
    max-height: 2.8em;
    overflow: hidden;
}

/*
 * Sticky headers — stick to the WINDOW while the PAGE scrolls.
 * Do NOT use max-height/overflow:auto on a wrapper (that hid rows).
 * Horizontal overflow only, and overflow-y: visible so sticky works.
 */
.purchase-lines-scroll,
#add_purchase_form .table-responsive:has(#purchase_entry_table) {
    max-height: none !important;
    overflow-x: auto;
    overflow-y: visible;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    /* isolate so sticky is relative to viewport scroll, not a clipped box */
    position: relative;
}

/* Float header under sticky search strip (~56–80px). Adjust if needed. */
#purchase_entry_table > thead > tr > th {
    position: -webkit-sticky;
    position: sticky;
    top: 72px;
    z-index: 30;
    background: #f1f5f9 !important;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
}

/* When sticky search strip is present, keep header below it */
body .content-wrapper #purchase_entry_table > thead > tr > th {
    top: 72px;
}

/* Mobile: less offset (no big sticky search) */
@media (max-width: 767px) {
    #purchase_entry_table > thead > tr > th {
        top: 0;
    }
}
</style>
<script>
(function () {
    // Recalculate sticky top so header sits under sticky search bar if present
    function purchaseStickyHeaderOffset() {
        var ths = document.querySelectorAll('#purchase_entry_table thead th');
        if (!ths.length) return;
        var bar = document.querySelector('#add_purchase_form .tw-sticky, #add_purchase_form .purchase-search-strip');
        var top = 0;
        if (bar) {
            var r = bar.getBoundingClientRect();
            // if bar is stuck at top of viewport
            if (r.top <= 1 && r.height) {
                top = Math.ceil(r.height);
            } else {
                top = 0;
            }
        }
        // also account for fixed admin navbar if any
        var nav = document.querySelector('.main-header, .navbar-static-top, nav.navbar');
        if (nav && window.getComputedStyle(nav).position === 'fixed') {
            top += Math.ceil(nav.getBoundingClientRect().height);
        }
        ths.forEach(function (th) {
            th.style.top = top + 'px';
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', purchaseStickyHeaderOffset);
    } else {
        purchaseStickyHeaderOffset();
    }
    window.addEventListener('scroll', purchaseStickyHeaderOffset, { passive: true });
    window.addEventListener('resize', purchaseStickyHeaderOffset);
})();
</script>
