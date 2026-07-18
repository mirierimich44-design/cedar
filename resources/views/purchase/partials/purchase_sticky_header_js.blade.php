{{--
  JS sticky header for #purchase_entry_table (clone approach).
  Works with AdminLTE overflow + wide tables; does not clip rows.
--}}
<style>
#purchase_sticky_header_bar {
    display: none;
    position: fixed;
    z-index: 1040; /* above content, below modals (1050+) */
    left: 0;
    top: 0;
    overflow: hidden;
    pointer-events: none;
    background: #f1f5f9;
    border-bottom: 2px solid #3b82f6;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
}
#purchase_sticky_header_bar table {
    margin: 0;
    background: #f1f5f9;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
}
#purchase_sticky_header_bar thead th {
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
    overflow: hidden;
    text-overflow: ellipsis;
}
#purchase_sticky_header_bar thead th:nth-child(2) {
    text-align: left !important;
}
/* Original thead still in document for a11y; clone is visual only when scrolled */
</style>
<script type="text/javascript">
(function ($) {
    if (typeof $ === 'undefined') {
        return;
    }

    var $bar = null;
    var $cloneTable = null;
    var $table = null;
    var $scrollParent = null;
    var raf = null;

    function getStickyTopOffset() {
        var top = 0;
        var $nav = $('.main-header').first();
        if ($nav.length && ($nav.css('position') === 'fixed' || $nav.css('position') === 'sticky')) {
            top += $nav.outerHeight() || 0;
        }
        // Sticky product search strip on purchase create/edit
        var $search = $('#add_purchase_form .tw-sticky, #add_purchase_form .purchase-search-strip').first();
        if ($search.length) {
            var r = $search[0].getBoundingClientRect();
            // If search strip is at/near top of viewport (stuck), sit under it
            if (r.top <= top + 2 && r.bottom > top) {
                top = Math.max(top, Math.ceil(r.bottom));
            }
        }
        return top;
    }

    function ensureBar() {
        if ($bar && $bar.length) {
            return;
        }
        $bar = $('<div id="purchase_sticky_header_bar" aria-hidden="true"></div>');
        $cloneTable = $('<table class="table table-condensed table-bordered table-th-green text-center"></table>');
        $bar.append($cloneTable);
        $('body').append($bar);
    }

    function rebuildClone() {
        $table = $('#purchase_entry_table');
        if (!$table.length) {
            if ($bar) {
                $bar.hide();
            }
            return false;
        }
        ensureBar();
        $scrollParent = $table.closest('.purchase-lines-scroll, .table-responsive');
        if (!$scrollParent.length) {
            $scrollParent = $(window);
        }
        var $thead = $table.children('thead').first();
        if (!$thead.length) {
            return false;
        }
        $cloneTable.empty().append($thead.clone(false, false));
        // strip ids from clone to avoid duplicates
        $cloneTable.find('[id]').removeAttr('id');
        syncWidths();
        return true;
    }

    function syncWidths() {
        if (!$table || !$table.length || !$cloneTable) {
            return;
        }
        var $origTh = $table.children('thead').first().find('th');
        var $cloneTh = $cloneTable.find('th');
        if (!$origTh.length || $origTh.length !== $cloneTh.length) {
            // column count changed — rebuild
            rebuildClone();
            $origTh = $table.children('thead').first().find('th');
            $cloneTh = $cloneTable.find('th');
        }
        var tableW = $table.outerWidth();
        $cloneTable.css({
            width: tableW + 'px',
            minWidth: tableW + 'px'
        });
        $origTh.each(function (i) {
            var w = $(this).outerWidth();
            $cloneTh.eq(i).css({
                width: w + 'px',
                minWidth: w + 'px',
                maxWidth: w + 'px',
                boxSizing: 'border-box'
            });
        });
    }

    function updateSticky() {
        if (!$table || !$table.length) {
            if (!rebuildClone()) {
                return;
            }
        }
        // Table removed from DOM
        if (!$table.closest('body').length) {
            if ($bar) {
                $bar.hide();
            }
            return;
        }

        var tableEl = $table[0];
        var rect = tableEl.getBoundingClientRect();
        var theadH = $table.children('thead').outerHeight() || 44;
        var offset = getStickyTopOffset();
        var tbodyBottom = rect.bottom;
        var theadBottom = rect.top + theadH;

        // Show clone when original thead has scrolled above sticky offset
        // and table body is still on screen
        var shouldShow = theadBottom < offset && tbodyBottom > offset + 20;

        if (!shouldShow) {
            $bar.hide();
            return;
        }

        syncWidths();

        var scrollLeft = 0;
        if ($scrollParent && $scrollParent.length && $scrollParent[0] !== window) {
            scrollLeft = $scrollParent.scrollLeft() || 0;
        }

        $bar.css({
            display: 'block',
            top: offset + 'px',
            left: rect.left + 'px',
            width: rect.width + 'px'
        });
        $cloneTable.css({
            marginLeft: (-scrollLeft) + 'px'
        });
    }

    function scheduleUpdate() {
        if (raf) {
            cancelAnimationFrame(raf);
        }
        raf = requestAnimationFrame(function () {
            raf = null;
            updateSticky();
        });
    }

    function bind() {
        $(window).off('.purchaseStickyHdr');
        $(document).off('.purchaseStickyHdr');

        if (!rebuildClone()) {
            return;
        }

        $(window).on('scroll.purchaseStickyHdr resize.purchaseStickyHdr', scheduleUpdate);

        var $wrap = $table.closest('.purchase-lines-scroll, .table-responsive');
        if ($wrap.length) {
            $wrap.off('.purchaseStickyHdr').on('scroll.purchaseStickyHdr', scheduleUpdate);
        }

        // After product rows are added/removed
        if (window.MutationObserver && $table.find('tbody').length) {
            if (window._purchaseStickyObs) {
                window._purchaseStickyObs.disconnect();
            }
            window._purchaseStickyObs = new MutationObserver(function () {
                rebuildClone();
                scheduleUpdate();
            });
            window._purchaseStickyObs.observe($table.find('tbody')[0], {
                childList: true,
                subtree: false
            });
        }

        // purchase.js appends rows asynchronously
        $(document).on('ajaxComplete.purchaseStickyHdr', function () {
            setTimeout(function () {
                rebuildClone();
                scheduleUpdate();
            }, 50);
        });

        scheduleUpdate();
    }

    $(function () {
        // Delay slightly so layout/sidebar finished
        setTimeout(bind, 100);
        setTimeout(bind, 500);
    });

    // Public re-init if needed after draft restore
    window.reinitPurchaseStickyHeader = function () {
        setTimeout(bind, 50);
    };
})(jQuery);
</script>
