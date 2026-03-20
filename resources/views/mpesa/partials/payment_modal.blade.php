<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => route('mpesa.initiate-payment'), 'method' => 'post', 'id' => 'mpesa_advanced_payment_form']) !!}
        <div class="modal-header">
            <h4 class="modal-title">@lang('lang_v1.mpesa_confirm_payment')</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
            <input type="hidden" name="transaction_type" value="{{ $transaction_type }}">
            <input type="hidden" name="amount" value="{{ $amount }}">

            <div class="row">
                <div class="col-md-12">
                    <p><strong>@lang('purchase.amount'):</strong> <span class="display_currency" data-currency_symbol="true">{{ $amount }}</span></p>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('payment_mode', __('lang_v1.payment_mode') . ':*') !!}
                        {!! Form::select('payment_mode', [
                            'send_money' => __('lang_v1.send_money') . ' (B2C)',
                            'buy_goods' => __('lang_v1.buy_goods') . ' (Till)',
                            'paybill' => __('lang_v1.paybill'),
                            'pochi' => 'Pochi La Biashara'
                        ], 'send_money', ['class' => 'form-control select2', 'required', 'style' => 'width:100%']) !!}
                    </div>
                </div>

                <!-- B2C Options (Send Money / Pochi) -->
                <div class="col-md-12 b2c_fields">
                    <div class="form-group">
                        {!! Form::label('phone', __('lang_v1.mobile_number') . ':*') !!}
                        {!! Form::text('phone', $phone, ['class' => 'form-control', 'placeholder' => __('lang_v1.mobile_number')]) !!}
                    </div>
                </div>

                <div class="col-md-12 b2c_fields" id="b2c_command_div">
                    <div class="form-group">
                        {!! Form::label('command_id', __('lang_v1.command_id') . ':') !!}
                        {!! Form::select('command_id', [
                            'BusinessPayment' => 'Business Payment',
                            'SalaryPayment' => 'Salary Payment',
                            'PromotionPayment' => 'Promotion Payment'
                        ], 'BusinessPayment', ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
                    </div>
                </div>

                <!-- B2B Options (Buy Goods / Paybill) -->
                <div class="col-md-12 b2b_fields" style="display:none;">
                    <div class="form-group">
                        {!! Form::label('b2b_shortcode', __('lang_v1.shortcode_or_till') . ':*') !!}
                        {!! Form::text('b2b_shortcode', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.shortcode_or_till')]) !!}
                    </div>
                </div>

                <div class="col-md-12 paybill_fields" style="display:none;">
                    <div class="form-group">
                        {!! Form::label('account_number', __('lang_v1.account_number') . ':*') !!}
                        {!! Form::text('account_number', $transaction->ref_no, ['class' => 'form-control', 'placeholder' => __('lang_v1.account_number')]) !!}
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('remarks', __('lang_v1.remarks') . ':') !!}
                        {!! Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('lang_v1.remarks')]) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#payment_mode').change(function() {
            var mode = $(this).val();
            if (mode == 'send_money' || mode == 'pochi') {
                $('.b2c_fields').show();
                $('.b2b_fields').hide();
                $('.paybill_fields').hide();
                if (mode == 'pochi') {
                    $('#b2c_command_div').hide();
                } else {
                    $('#b2c_command_div').show();
                }
            } else if (mode == 'buy_goods') {
                $('.b2c_fields').hide();
                $('.b2b_fields').show();
                $('.paybill_fields').hide();
            } else if (mode == 'paybill') {
                $('.b2c_fields').hide();
                $('.b2b_fields').show();
                $('.paybill_fields').show();
            }
        });

        $('#mpesa_advanced_payment_form').submit(function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            var url = $(this).attr('action');
            var btn = $(this).find('button[type="submit"]');

            btn.attr('disabled', true);

            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success) {
                        Swal.fire({
                            title: 'Success',
                            text: result.message,
                            icon: 'success'
                        });
                        $('#mpesa_payment_modal').modal('hide');
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: result.message,
                            icon: 'error'
                        });
                        btn.attr('disabled', false);
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error'
                    });
                    btn.attr('disabled', false);
                }
            });
        });
    });
</script>
