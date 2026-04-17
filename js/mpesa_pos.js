$(document).ready(function () {

    // ─── Manual "Match M-Pesa Payment" button ─────────────────────────────────

    $(document).on('click', '.match_mpesa_payment', function () {
        var row_index = $(this).data('row_index');
        var amount    = $(this).closest('.row').find('.payment-amount').val();

        if ($('#match_mpesa_modal').length === 0) {
            $('body').append(
                '<div class="modal fade" id="match_mpesa_modal" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog modal-lg" role="document"><div class="modal-content">' +
                '<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>' +
                '<h4 class="modal-title">Match M-Pesa Payment</h4></div>' +
                '<div class="modal-body">' +
                '<div class="row"><div class="col-md-12"><div class="form-group"><div class="input-group">' +
                '<input type="text" id="mpesa_search_input" class="form-control" placeholder="Search by Receipt, Phone or Name">' +
                '<span class="input-group-btn"><button type="button" id="mpesa_search_btn" class="btn btn-primary"><i class="fa fa-search"></i> Search</button></span>' +
                '</div></div></div></div>' +
                '<div id="mpesa_results_div" style="max-height:400px;overflow-y:auto;"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>' +
                '</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>' +
                '</div></div></div>'
            );
        }

        $('#match_mpesa_modal').data('row_index', row_index);
        $('#match_mpesa_modal').modal('show');
        fetchUnassignedPayments(amount);
    });

    function fetchUnassignedPayments(amount, search) {
        amount = amount || null;
        search = search || '';
        $('#mpesa_results_div').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div>');
        $.get('/mpesa/get-unassigned-payments', { amount: amount, search: search }, function (r) {
            var html = '<table class="table table-bordered table-striped">' +
                '<thead><tr><th>Trans ID</th><th>Amount (KES)</th><th>Customer</th><th>Phone</th><th>Time</th><th>Action</th></tr></thead><tbody>';
            if (!r.success || r.payments.length === 0) {
                html += '<tr><td colspan="6" class="text-center text-muted">No unassigned payments found</td></tr>';
            } else {
                r.payments.forEach(function (p) {
                    html += '<tr>' +
                        '<td>' + p.trans_id + '</td>' +
                        '<td>' + parseFloat(p.amount).toFixed(2) + '</td>' +
                        '<td>' + ((p.first_name || '') + ' ' + (p.last_name || '')).trim() + '</td>' +
                        '<td>' + (p.msisdn || '—') + '</td>' +
                        '<td>' + (p.created_at || '') + '</td>' +
                        '<td><button type="button" class="btn btn-xs btn-success select_mpesa_payment" ' +
                        'data-receipt="' + p.trans_id + '" data-amount="' + p.amount + '">Select</button></td>' +
                        '</tr>';
                });
            }
            html += '</tbody></table>';
            $('#mpesa_results_div').html(html);
        });
    }

    $(document).on('click', '#mpesa_search_btn', function () {
        fetchUnassignedPayments(null, $('#mpesa_search_input').val());
    });

    $(document).on('click', '.select_mpesa_payment', function () {
        var receipt   = $(this).data('receipt');
        var amount    = $(this).data('amount');
        var row_index = $('#match_mpesa_modal').data('row_index');

        var row = $('.payment_row_index[value="' + row_index + '"]').closest('.row');
        row.find('.mpesa_receipt_number').val(receipt);
        row.find('.payment-amount').val(amount).trigger('change');

        $('#match_mpesa_modal').modal('hide');
        toastr.success('M-Pesa payment matched: ' + receipt);
    });

    // ─── Auto-detection: poll for incoming C2B payment ────────────────────────

    var autoDetectInterval  = null;
    var autoDetectSeenTrans = [];  // receipts already shown this session

    function alreadyMatchedInForm(transId) {
        var found = false;
        $('.mpesa_receipt_number').each(function () {
            if ($(this).val() === transId) { found = true; }
        });
        return found;
    }

    function startAutoDetect() {
        // Reset seen-list each time the modal opens (new sale)
        autoDetectSeenTrans = [];

        if (autoDetectInterval) {
            clearInterval(autoDetectInterval);
        }

        autoDetectInterval = setInterval(function () {
            var total_payable = __read_number($('span.total_payable_span'));
            if (total_payable <= 0) return;

            $.get('/mpesa/get-unassigned-payments', { amount: total_payable }, function (r) {
                if (!r.success || r.payments.length === 0) return;

                var p = r.payments[0]; // newest matching payment

                // Skip if already shown or already matched in a form row
                if (autoDetectSeenTrans.indexOf(p.trans_id) !== -1) return;
                if (alreadyMatchedInForm(p.trans_id)) return;

                // Mark as seen so we don't show the toast repeatedly
                autoDetectSeenTrans.push(p.trans_id);

                toastr.success('M-Pesa payment detected: ' + p.trans_id + ' (KES ' + parseFloat(p.amount).toFixed(2) + ')', '', { timeOut: 8000 });

                // Auto-fill if the first row is still on cash with 0 amount
                var first_row   = $('#payment_rows_div .row').first();
                var method_sel  = first_row.find('.payment_types_dropdown');
                var row_amount  = parseFloat(first_row.find('.payment-amount').val()) || 0;

                if (method_sel.val() === 'cash' && row_amount === 0) {
                    method_sel.val('mpesa').trigger('change');
                    first_row.find('.mpesa_receipt_number').val(p.trans_id);
                    first_row.find('.payment-amount').val(p.amount).trigger('change');
                }
            });
        }, 8000); // poll every 8 seconds
    }

    function stopAutoDetect() {
        if (autoDetectInterval) {
            clearInterval(autoDetectInterval);
            autoDetectInterval = null;
        }
    }

    $('#modal_payment').on('shown.bs.modal', startAutoDetect);
    $('#modal_payment').on('hidden.bs.modal', stopAutoDetect);
});
