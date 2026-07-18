{{-- Shared: purchase line Details modal JS (create + edit) --}}
<script type="text/javascript">
(function () {
    function findPurchaseRow(row) {
        var $byData = $('#purchase_entry_table tbody tr[data-row="' + row + '"]');
        if ($byData.length) {
            return $byData.first();
        }
        // Fallback: edit rows may use data-row on button only
        var $btn = $('#purchase_entry_table tbody .btn-purchase-details[data-row="' + row + '"]');
        if ($btn.length) {
            return $btn.closest('tr');
        }
        return $();
    }

    function rawNumber($el) {
        if (!$el || !$el.length) return 0;
        if (typeof __read_number === 'function') {
            try {
                return __read_number($el, true) || 0;
            } catch (e) {}
        }
        var v = ($el.val() || $el.text() || '').toString().replace(/,/g, '');
        return parseFloat(v) || 0;
    }

    $(document).on('click', '.btn-purchase-details', function () {
        var $btn = $(this);
        var row = $btn.data('row');
        var $row = $btn.closest('tr');
        if (!$row.length) {
            $row = findPurchaseRow(row);
        }

        $('#details_modal_row').val(row);
        var name = $btn.data('product') || '';
        var variation = $btn.data('variation') || '';
        $('#details_modal_product_name').text(name + (variation ? ' · ' + variation : ''));

        var $sellInput = $row.find('.default_sell_price');
        if ($sellInput.length) {
            $('#modal_sell_price').val($sellInput.val());
            $('#details_modal_sell_row, #details_modal_sell_hr').show();
        } else {
            $('#modal_sell_price').val('');
            $('#details_modal_sell_row, #details_modal_sell_hr').hide();
        }

        var $marginInput = $row.find('.profit_percent');
        if ($marginInput.length) {
            $('#modal_margin').val($marginInput.val());
        } else {
            $('#modal_margin').val('');
        }

        var $lotInput = $row.find('.lot_number_input, input[name*="[lot_number]"]');
        if ($lotInput.length) {
            $('#modal_lot_number').val($lotInput.first().val());
            $('#details_modal_lot_group').show();
        } else {
            $('#modal_lot_number').val('');
            $('#details_modal_lot_group').hide();
        }

        var $mfgInput = $row.find('.mfg_date');
        if ($mfgInput.length) {
            $('#modal_mfg_date').val($mfgInput.val());
            $('#details_modal_mfg_group').show();
        } else {
            $('#modal_mfg_date').val('');
            $('#details_modal_mfg_group').hide();
        }

        var $expInput = $row.find('.exp_date');
        if ($expInput.length) {
            $('#modal_exp_date').val($expInput.val());
        } else {
            $('#modal_exp_date').val('');
        }

        if ($.fn.datepicker) {
            $('#modal_mfg_date, #modal_exp_date').datepicker({
                autoclose: true,
                format: (typeof moment_date_format !== 'undefined' && moment_date_format)
                    ? moment_date_format.toLowerCase().replace('yyyy', 'yy')
                    : 'dd/mm/yyyy'
            });
        }

        $('#purchase_line_details_modal').modal('show');
    });

    $(document).on('change input', '#modal_sell_price', function () {
        var row = $('#details_modal_row').val();
        var $row = findPurchaseRow(row);
        if (!$row.length) return;

        var purchase_after_tax = rawNumber($row.find('.purchase_unit_cost_after_tax'));
        var sell_price = parseFloat($(this).val()) || 0;
        var exchange_rate = parseFloat($('input#exchange_rate').val()) || 1;
        var sell_price_in_base_currency = sell_price / exchange_rate;

        var profit_percent = 0;
        if (typeof __get_rate === 'function') {
            profit_percent = __get_rate(purchase_after_tax, sell_price_in_base_currency);
        } else if (purchase_after_tax) {
            profit_percent = ((sell_price_in_base_currency - purchase_after_tax) * 100) / purchase_after_tax;
        }
        $('#modal_margin').val((profit_percent || 0).toFixed(2));
    });

    $(document).on('change input', '#modal_margin', function () {
        var row = $('#details_modal_row').val();
        var $row = findPurchaseRow(row);
        if (!$row.length) return;

        var purchase_after_tax = rawNumber($row.find('.purchase_unit_cost_after_tax'));
        var margin = parseFloat($(this).val()) || 0;
        var exchange_rate = parseFloat($('input#exchange_rate').val()) || 1;

        var sell_price = purchase_after_tax + (purchase_after_tax * margin / 100);
        var sell_price_in_currency = sell_price * exchange_rate;
        $('#modal_sell_price').val(sell_price_in_currency.toFixed(2));
    });

    $(document).on('click', '#btn_save_line_details', function () {
        var row = $('#details_modal_row').val();
        var $row = findPurchaseRow(row);
        if (!$row.length) {
            toastr && toastr.error('Could not find purchase line row.');
            return;
        }

        var sellPrice = $('#modal_sell_price').val();
        var $sellInput = $row.find('.default_sell_price');
        if ($sellInput.length && sellPrice !== '') {
            $sellInput.val(sellPrice).trigger('change');
            $row.find('.details-sell-badge').text('Sell: ' + sellPrice).show();
        }

        var margin = $('#modal_margin').val();
        var $marginInput = $row.find('.profit_percent');
        if ($marginInput.length && margin !== '') {
            $marginInput.val(margin).trigger('change');
        }

        var lot = $('#modal_lot_number').val();
        var $lotInput = $row.find('.lot_number_input, input[name*="[lot_number]"]');
        if ($lotInput.length) {
            $lotInput.first().val(lot);
            if (lot) {
                $row.find('.details-lot-badge').text('Lot: ' + lot).show();
            } else {
                $row.find('.details-lot-badge').hide();
            }
        }

        var mfgDate = $('#modal_mfg_date').val();
        var $mfgInput = $row.find('.mfg_date');
        if ($mfgInput.length) {
            $mfgInput.val(mfgDate);
        }

        var expDate = $('#modal_exp_date').val();
        var $expInput = $row.find('.exp_date');
        if ($expInput.length) {
            $expInput.val(expDate);
        }
        if (expDate) {
            $row.find('.details-exp-badge').text('Exp: ' + expDate).show();
        } else {
            $row.find('.details-exp-badge').hide();
        }

        if (sellPrice || lot || expDate) {
            $row.find('.details-summary-badge').show();
        }

        $row.find('.btn-purchase-details')
            .removeClass('btn-primary')
            .addClass('btn-success')
            .html('<i class="fa fa-check"></i> Details ✓');

        $('#purchase_line_details_modal').modal('hide');
    });

    $(document).on('hidden.bs.modal', '#purchase_line_details_modal', function () {
        $('#modal_sell_price, #modal_margin, #modal_lot_number, #modal_mfg_date, #modal_exp_date').val('');
    });
})();
</script>
