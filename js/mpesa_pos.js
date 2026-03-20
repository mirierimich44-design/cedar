$(document).ready(function () {
    $(document).on('click', '.match_mpesa_payment', function () {
        var row_index = $(this).data('row_index');
        var amount = $(this).closest('.row').find('.payment-amount').val();

        // Create match modal if it doesn't exist
        if ($('#match_mpesa_modal').length == 0) {
            $('body').append('<div class="modal fade" id="match_mpesa_modal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Match M-Pesa Payment</h4></div><div class="modal-body"><div class="row"><div class="col-md-12"><div class="form-group"><div class="input-group"><input type="text" id="mpesa_search_input" class="form-control" placeholder="Search by Receipt, Phone or Name"><span class="input-group-btn"><button type="button" id="mpesa_search_btn" class="btn btn-primary"><i class="fa fa-search"></i> Search</button></span></div></div></div></div><div id="mpesa_results_div" style="max-height: 400px; overflow-y: auto;"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>');
        }

        $('#match_mpesa_modal').data('row_index', row_index);
        $('#match_mpesa_modal').modal('show');
        fetchUnassignedPayments(amount);
    });

    function fetchUnassignedPayments(amount = null, search = '') {
        $('#mpesa_results_div').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div>');
        $.get('/mpesa/get-unassigned-payments', { amount: amount, search: search }, function (r) {
            if (r.success) {
                var html = '<table class="table table-bordered table-striped"><thead><tr><th>Trans ID</th><th>Amount</th><th>Customer</th><th>Phone</th><th>Time</th><th>Action</th></tr></thead><tbody>';
                if (r.payments.length == 0) {
                    html += '<tr><td colspan="6" class="text-center">No unassigned payments found</td></tr>';
                } else {
                    r.payments.forEach(function (p) {
                        html += '<tr><td>' + p.trans_id + '</td><td>' + p.amount + '</td><td>' + (p.first_name || '') + ' ' + (p.last_name || '') + '</td><td>' + p.msisdn + '</td><td>' + p.created_at + '</td><td><button type="button" class="btn btn-xs btn-success select_mpesa_payment" data-receipt="' + p.trans_id + '" data-amount="' + p.amount + '">Select</button></td></tr>';
                    });
                }
                html += '</tbody></table>';
                $('#mpesa_results_div').html(html);
            }
        });
    }

    $(document).on('click', '#mpesa_search_btn', function () {
        var search = $('#mpesa_search_input').val();
        fetchUnassignedPayments(null, search);
    });

    $(document).on('click', '.select_mpesa_payment', function () {
        var receipt = $(this).data('receipt');
        var amount = $(this).data('amount');
        var row_index = $('#match_mpesa_modal').data('row_index');

        var row = $('.payment_row_index[value="' + row_index + '"]').closest('.row');
        row.find('.mpesa_receipt_number').val(receipt);
        row.find('.payment-amount').val(amount).trigger('change');

        $('#match_mpesa_modal').modal('hide');
        toastr.success('M-Pesa payment matched!');
    });

    // Auto-detection logic
    var autoDetectInterval = null;
    $('#modal_payment').on('shown.bs.modal', function () {
        if (!autoDetectInterval) {
            autoDetectInterval = setInterval(function () {
                var total_payable = __read_number($('span.total_payable_span'));
                if (total_payable > 0) {
                    $.get('/mpesa/get-unassigned-payments', { amount: total_payable }, function (r) {
                        if (r.success && r.payments.length > 0) {
                            var p = r.payments[0];
                            // Check if receipt already exists in any row
                            var exists = false;
                            $('.mpesa_receipt_number').each(function () {
                                if ($(this).val() == p.trans_id) exists = true;
                            });

                            if (!exists) {
                                toastr.success('New M-Pesa payment detected: ' + p.trans_id + ' (KES ' + p.amount + ')');
                                // If only one payment row and its cash/empty, switch it to mpesa
                                var first_row = $('#payment_rows_div .row').first();
                                var method_select = first_row.find('.payment_types_dropdown');
                                if (method_select.val() == 'cash' && first_row.find('.payment-amount').val() == 0) {
                                    method_select.val('mpesa').trigger('change');
                                    first_row.find('.mpesa_receipt_number').val(p.trans_id);
                                    first_row.find('.payment-amount').val(p.amount).trigger('change');
                                }
                            }
                        }
                    });
                }
            }, 10000); // Check every 10 seconds
        }
    });

    $('#modal_payment').on('hidden.bs.modal', function () {
        if (autoDetectInterval) {
            clearInterval(autoDetectInterval);
            autoDetectInterval = null;
        }
    });
});
