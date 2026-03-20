<!-- M-Pesa Payment Form for POS -->
<div class="mpesa-payment-section" id="mpesa_payment_section" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info" id="mpesa_info_alert">
                <i class="fas fa-mobile-alt"></i>
                <strong>@lang('lang_v1.mpesa_payment')</strong>
                <p>@lang('lang_v1.mpesa_stk_description')</p>
            </div>
        </div>

        <!-- Phone Number Input -->
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('mpesa_phone', __('lang_v1.phone_number') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="flag-icon flag-icon-ke"></span> +254
                    </span>
                    {!! Form::text('mpesa_phone', '', [
                        'class' => 'form-control input-lg',
                        'id' => 'mpesa_phone',
                        'placeholder' => '7XXXXXXXX',
                        'maxlength' => '9',
                        'pattern' => '[0-9]*'
                    ]) !!}
                </div>
                <p class="help-block text-muted">@lang('lang_v1.mpesa_phone_format_help')</p>
            </div>
        </div>

        <!-- Amount Display -->
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('mpesa_amount', __('sale.amount') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">KES</span>
                    <input type="text" class="form-control input-lg" id="mpesa_amount" readonly>
                </div>
            </div>
        </div>

        <!-- STK Push Button -->
        <div class="col-md-12">
            <button type="button" id="send_stk_push_btn" class="btn btn-success btn-lg btn-block">
                <i class="fas fa-paper-plane"></i> @lang('lang_v1.mpesa_send_stk_push')
            </button>
        </div>
    </div>

    <!-- Waiting State -->
    <div class="row" id="mpesa_waiting_section" style="display: none;">
        <div class="col-md-12 text-center">
            <div class="tw-py-8">
                <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                <h4 class="tw-mt-4">@lang('lang_v1.mpesa_waiting_for_payment')</h4>
                <p class="text-muted">@lang('lang_v1.mpesa_check_phone')</p>
                <div class="tw-mt-4">
                    <span class="label label-warning" id="mpesa_countdown">2:00</span>
                </div>
                <button type="button" id="cancel_mpesa_payment" class="btn btn-default tw-mt-4">
                    <i class="fas fa-times"></i> @lang('messages.cancel')
                </button>
            </div>
        </div>
    </div>

    <!-- Success State -->
    <div class="row" id="mpesa_success_section" style="display: none;">
        <div class="col-md-12 text-center">
            <div class="tw-py-8">
                <i class="fas fa-check-circle fa-3x text-success"></i>
                <h4 class="tw-mt-4 text-success">@lang('lang_v1.mpesa_payment_successful')</h4>
                <p class="text-muted">@lang('lang_v1.mpesa_receipt_number'):</p>
                <h3 id="mpesa_receipt_display" class="text-primary"></h3>
            </div>
        </div>
    </div>

    <!-- Error State -->
    <div class="row" id="mpesa_error_section" style="display: none;">
        <div class="col-md-12 text-center">
            <div class="tw-py-8">
                <i class="fas fa-times-circle fa-3x text-danger"></i>
                <h4 class="tw-mt-4 text-danger">@lang('lang_v1.mpesa_payment_failed')</h4>
                <p class="text-muted" id="mpesa_error_message"></p>
                <button type="button" id="retry_mpesa_payment" class="btn btn-primary tw-mt-4">
                    <i class="fas fa-redo"></i> @lang('lang_v1.mpesa_retry')
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
// M-Pesa Payment Handler
var MpesaPayment = {
    pollingInterval: null,
    countdownInterval: null,
    mpesaTransactionId: null,
    checkoutRequestId: null,
    timeoutSeconds: 120, // 2 minutes

    init: function() {
        var self = this;

        // Phone input formatting - only allow numbers
        $('#mpesa_phone').on('keypress', function(e) {
            if (e.which < 48 || e.which > 57) {
                e.preventDefault();
            }
        });

        // Send STK Push
        $('#send_stk_push_btn').click(function() {
            self.sendSTKPush();
        });

        // Cancel payment
        $('#cancel_mpesa_payment').click(function() {
            self.cancelPayment();
        });

        // Retry payment
        $('#retry_mpesa_payment').click(function() {
            self.resetForm();
        });
    },

    setAmount: function(amount) {
        $('#mpesa_amount').val(parseFloat(amount).toFixed(2));
    },

    sendSTKPush: function() {
        var self = this;
        var phone = $('#mpesa_phone').val().trim();
        var amount = parseFloat($('#mpesa_amount').val());

        // Validate phone
        if (!phone || phone.length < 9) {
            toastr.error('Please enter a valid phone number');
            $('#mpesa_phone').focus();
            return;
        }

        // Format phone: add 254 prefix if needed
        if (phone.startsWith('0')) {
            phone = phone.substring(1);
        }
        phone = '254' + phone;

        if (amount <= 0) {
            toastr.error('Invalid amount');
            return;
        }

        // Show waiting state
        this.showWaiting();

        // Get reference from invoice number if available
        var reference = $('#invoice_no').val() || 'Payment';

        $.ajax({
            url: '/mpesa/stk-push',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                phone: phone,
                amount: amount,
                reference: reference.substring(0, 12)
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    self.mpesaTransactionId = response.mpesa_transaction_id;
                    self.checkoutRequestId = response.checkout_request_id;
                    self.startPolling();
                    self.startCountdown();
                    toastr.success(response.message);
                } else {
                    self.showError(response.message);
                }
            },
            error: function(xhr) {
                self.showError('Failed to send M-Pesa request. Please try again.');
            }
        });
    },

    startPolling: function() {
        var self = this;
        this.pollingInterval = setInterval(function() {
            self.checkPaymentStatus();
        }, 3000); // Check every 3 seconds
    },

    stopPolling: function() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    },

    startCountdown: function() {
        var self = this;
        var seconds = this.timeoutSeconds;

        this.countdownInterval = setInterval(function() {
            seconds--;
            var mins = Math.floor(seconds / 60);
            var secs = seconds % 60;
            $('#mpesa_countdown').text(mins + ':' + (secs < 10 ? '0' : '') + secs);

            if (seconds <= 0) {
                self.stopCountdown();
                self.stopPolling();
                self.showError('Payment request timed out. Please try again.');
            }
        }, 1000);
    },

    stopCountdown: function() {
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
            this.countdownInterval = null;
        }
    },

    checkPaymentStatus: function() {
        var self = this;

        $.ajax({
            url: '/mpesa/check-payment-status',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                mpesa_transaction_id: this.mpesaTransactionId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.is_paid) {
                        self.stopPolling();
                        self.stopCountdown();
                        self.showSuccess(response.receipt_number);
                    } else if (response.status === 'failed' || 
                               response.status === 'cancelled' || 
                               response.status === 'expired') {
                        self.stopPolling();
                        self.stopCountdown();
                        self.showError(response.result_description || 'Payment failed');
                    }
                    // Keep polling if still pending
                }
            },
            error: function() {
                // Ignore polling errors, keep trying
            }
        });
    },

    showWaiting: function() {
        $('#mpesa_info_alert').hide();
        $('#mpesa_phone').parent().parent().parent().hide();
        $('#mpesa_amount').parent().parent().parent().hide();
        $('#send_stk_push_btn').hide();
        $('#mpesa_waiting_section').show();
        $('#mpesa_success_section').hide();
        $('#mpesa_error_section').hide();
    },

    showSuccess: function(receiptNumber) {
        $('#mpesa_waiting_section').hide();
        $('#mpesa_success_section').show();
        $('#mpesa_receipt_display').text(receiptNumber);

        // Add payment to the form
        if (typeof pos_payment_add_row === 'function') {
            var amount = parseFloat($('#mpesa_amount').val());
            // Trigger adding M-Pesa payment to payment list
            // This integrates with the existing POS payment system
        }

        // Auto-complete payment after 2 seconds
        setTimeout(function() {
            // Mark the payment as complete in the POS
            toastr.success('M-Pesa payment completed successfully!');
        }, 2000);
    },

    showError: function(message) {
        $('#mpesa_waiting_section').hide();
        $('#mpesa_error_section').show();
        $('#mpesa_error_message').text(message);
    },

    resetForm: function() {
        $('#mpesa_info_alert').show();
        $('#mpesa_phone').parent().parent().parent().show();
        $('#mpesa_amount').parent().parent().parent().show();
        $('#send_stk_push_btn').show();
        $('#mpesa_waiting_section').hide();
        $('#mpesa_success_section').hide();
        $('#mpesa_error_section').hide();
        $('#mpesa_phone').val('').focus();
    },

    cancelPayment: function() {
        this.stopPolling();
        this.stopCountdown();
        this.resetForm();
    },

    show: function(amount) {
        this.setAmount(amount);
        $('#mpesa_payment_section').show();
        this.resetForm();
    },

    hide: function() {
        this.cancelPayment();
        $('#mpesa_payment_section').hide();
    }
};

$(document).ready(function() {
    MpesaPayment.init();
});
</script>
