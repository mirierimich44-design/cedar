<div class="modal fade" tabindex="-1" role="dialog" id="modal_payment">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.payment')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-12">
                        <strong>@lang('lang_v1.advance_balance'):</strong> <span id="advance_balance_text"></span>
                        {!! Form::hidden('advance_balance', null, [
                            'id' => 'advance_balance',
                            'data-error-msg' => __('lang_v1.required_advance_balance_not_available'),
                        ]) !!}
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div id="payment_rows_div">
                                @php
                                    $pos_settings = !empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];
                                    $show_in_pos = '';


                                    if (isset($pos_settings['enable_cash_denomination_on']) && ($pos_settings['enable_cash_denomination_on'] == 'all_screens' || $pos_settings['enable_cash_denomination_on'] == 'pos_screen')) {
                                        $show_in_pos = true;
                                    }
                                    
                                @endphp
                                @foreach ($payment_lines as $payment_line)
                                    @if ($payment_line['is_return'] == 1)
                                        @php
                                            $change_return = $payment_line;
                                        @endphp

                                        @continue
                                    @endif

                                    @include('sale_pos.partials.payment_row', [
                                        'removable' => !$loop->first,
                                        'row_index' => $loop->index,
                                        'payment_line' => $payment_line,
                                        'show_denomination' => true,
                                        'show_in_pos' => $show_in_pos,
                                    ])
                                @endforeach
                            </div>
                            <input type="hidden" id="payment_row_index" value="{{ count($payment_lines) }}">
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm tw-w-full"
                                    id="add-payment-row">@lang('sale.add_payment_row')</button>
                            </div>
                        </div>
                        <br>
                        <div class="row @if ($change_return['amount'] == 0) hide @endif payment_row"
                            id="change_return_payment_data">
                            <div class="col-md-12">
                                <div class="box box-solid payment_row bg-lightgray">
                                    <div class="box-body">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::label('change_return_method', __('lang_v1.change_return_payment_method') . ':*') !!}
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fas fa-money-bill-alt"></i>
                                                    </span>
                                                    @php
                                                        $_payment_method = empty($change_return['method']) && array_key_exists('cash', $payment_types) ? 'cash' : $change_return['method'];

                                                        $_payment_types = $payment_types;
                                                        if (isset($_payment_types['advance'])) {
                                                            unset($_payment_types['advance']);
                                                        }
                                                    @endphp
                                                    {!! Form::select('payment[change_return][method]', $_payment_types, $_payment_method, [
                                                        'class' => 'form-control col-md-12 payment_types_dropdown',
                                                        'id' => 'change_return_method',
                                                        'style' => 'width:100%;',
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        @if (!empty($accounts))
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('change_return_account', __('lang_v1.change_return_payment_account') . ':') !!}
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fas fa-money-bill-alt"></i>
                                                        </span>
                                                        {!! Form::select(
                                                            'payment[change_return][account_id]',
                                                            $accounts,
                                                            !empty($change_return['account_id']) ? $change_return['account_id'] : '',
                                                            ['class' => 'form-control select2', 'id' => 'change_return_account', 'style' => 'width:100%;'],
                                                        ) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="clearfix"></div>
                                        @include('sale_pos.partials.payment_type_details', [
                                            'payment_line' => $change_return,
                                            'row_index' => 'change_return',
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('sale_note', __('sale.sell_note') . ':') !!}
                                    {!! Form::textarea('sale_note', !empty($transaction) ? $transaction->additional_notes : null, [
                                        'class' => 'form-control',
                                        'rows' => 3,
                                        'placeholder' => __('sale.sell_note'),
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('staff_note', __('sale.staff_note') . ':') !!}
                                    {!! Form::textarea('staff_note', !empty($transaction) ? $transaction->staff_note : null, [
                                        'class' => 'form-control',
                                        'rows' => 3,
                                        'placeholder' => __('sale.staff_note'),
                                    ]) !!}
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    <div class="col-md-3">
                        <div class="box box-solid bg-orange">
                            <div class="box-body">
                                <div class="col-md-12">
                                    <strong>
                                        @lang('lang_v1.total_items'):
                                    </strong>
                                    <br />
                                    <span class="lead text-bold total_quantity">0</span>
                                </div>

                                <div class="col-md-12">
                                    <hr>
                                    <strong>
                                        @lang('sale.total_payable'):
                                    </strong>
                                    <br />
                                    <span class="lead text-bold total_payable_span">0</span>
                                </div>

                                <div class="col-md-12">
                                    <hr>
                                    <strong>
                                        @lang('lang_v1.total_paying'):
                                    </strong>
                                    <br />
                                    <span class="lead text-bold total_paying">0</span>
                                    <input type="hidden" id="total_paying_input">
                                </div>

                                <div class="col-md-12">
                                    <hr>
                                    <strong>
                                        @lang('lang_v1.change_return'):
                                    </strong>
                                    <br />
                                    <span class="lead text-bold change_return_span">0</span>
                                    {!! Form::hidden('change_return', $change_return['amount'], [
                                        'class' => 'form-control change_return input_number',
                                        'required',
                                        'id' => 'change_return',
                                    ]) !!}
                                    <!-- <span class="lead text-bold total_quantity">0</span> -->
                                    @if (!empty($change_return['id']))
                                        <input type="hidden" name="change_return_id"
                                            value="{{ $change_return['id'] }}">
                                    @endif
                                </div>

                                <div class="col-md-12">
                                    <hr>
                                    <strong>
                                        @lang('lang_v1.balance'):
                                    </strong>
                                    <br />
                                    <span class="lead text-bold balance_due">0</span>
                                    <input type="hidden" id="in_balance_due" value=0>
                                </div>



                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="pos-save">@lang('sale.finalize_payment')</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Used for express checkout card transaction -->
<div class="modal fade" tabindex="-1" role="dialog" id="card_details_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.card_transaction_details')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('card_number', __('lang_v1.card_no')) !!}
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.card_no'),
                                    'id' => 'card_number',
                                    'autofocus',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('card_holder_name', __('lang_v1.card_holder_name')) !!}
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.card_holder_name'),
                                    'id' => 'card_holder_name',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('card_transaction_number', __('lang_v1.card_transaction_no')) !!}
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.card_transaction_no'),
                                    'id' => 'card_transaction_number',
                                ]) !!}
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('card_type', __('lang_v1.card_type')) !!}
                                {!! Form::select('', ['visa' => 'Visa', 'master' => 'MasterCard'], 'visa', [
                                    'class' => 'form-control select2',
                                    'id' => 'card_type',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('card_month', __('lang_v1.month')) !!}
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.month'),
                                    'id' => 'card_month',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('card_year', __('lang_v1.year')) !!}
                                {!! Form::text('', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.year'), 'id' => 'card_year']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('card_security', __('lang_v1.security_code')) !!}
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.security_code'),
                                    'id' => 'card_security',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="pos-save-card">@lang('sale.finalize_payment')</button>
            </div>
        </div>
    </div>
</div>

<!-- M-PESA Payment Modal - Styled -->
<div class="modal fade" tabindex="-1" role="dialog" id="mpesa_details_modal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
            {{-- Header --}}
            <div style="background: linear-gradient(135deg, #00B09B 0%, #96C93D 100%); padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;">
                <h4 style="margin: 0; color: white; font-weight: 700; font-size: 18px;">
                    <i class="fas fa-mobile-alt" style="margin-right: 10px;"></i> M-Pesa Payment
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1; font-size: 28px; text-shadow: none;">&times;</button>
            </div>

            <div class="modal-body" style="padding: 24px; background: #f8fafc;">
                {{-- Summary Card --}}
                <div style="background: white; border-radius: 12px; padding: 16px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-weight: 700; color: #00B09B; font-size: 11px; letter-spacing: 0.5px;">
                            <i class="fas fa-chart-line"></i> TODAY'S SUMMARY
                        </span>
                        <button type="button" id="refresh_mpesa_summary" style="background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 10px; font-size: 10px; cursor: pointer;">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; text-align: center;">
                        <div style="padding: 8px;">
                            <div id="mpesa_today_count" style="font-size: 20px; font-weight: 800; color: #1e293b;">0</div>
                            <div style="font-size: 9px; color: #64748b; font-weight: 600;">PAID</div>
                        </div>
                        <div style="padding: 8px; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0;">
                            <div id="mpesa_today_total" style="font-size: 20px; font-weight: 800; color: #10b981;">KES 0</div>
                            <div style="font-size: 9px; color: #64748b; font-weight: 600;">TOTAL</div>
                        </div>
                        <div style="padding: 8px;">
                            <div id="mpesa_today_pending" style="font-size: 20px; font-weight: 800; color: #f59e0b;">0</div>
                            <div style="font-size: 9px; color: #64748b; font-weight: 600;">PENDING</div>
                        </div>
                    </div>
                </div>

                {{-- Input Section --}}
                <div id="mpesa_input_section" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; letter-spacing: 0.5px;">PHONE NUMBER</label>
                        <div style="display: flex; border: 2px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
                            <span style="background: #f1f5f9; padding: 12px 14px; font-weight: 700; color: #64748b; border-right: 2px solid #e2e8f0;">+254</span>
                            <input type="tel" id="mpesa_phone_input" placeholder="712345678" maxlength="9" style="flex: 1; border: none; padding: 12px 14px; font-size: 16px; font-weight: 600; outline: none;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; letter-spacing: 0.5px;">AMOUNT (KES)</label>
                        <div style="display: flex; border: 2px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #f8fafc;">
                            <span style="background: #f1f5f9; padding: 12px 14px; font-weight: 700; color: #64748b; border-right: 2px solid #e2e8f0;">KES</span>
                            <input type="text" id="mpesa_amount_display" readonly style="flex: 1; border: none; padding: 12px 14px; font-size: 18px; font-weight: 800; color: #00B09B; background: transparent; outline: none;">
                        </div>
                    </div>

                    <button type="button" id="send_stk_push_btn" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #00B09B 0%, #96C93D 100%); color: white; border: none; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(0,176,155,0.3);">
                        <i class="fas fa-paper-plane" style="margin-right: 8px;"></i> SEND STK PUSH
                    </button>
                </div>

                {{-- Waiting/Success/Error States --}}
                <div id="mpesa_waiting_section" style="display: none; background: white; border-radius: 12px; padding: 32px; text-align: center;">
                    <div style="width: 60px; height: 60px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #f59e0b;"></i>
                    </div>
                    <h3 style="font-weight: 700; margin-bottom: 8px; color: #1e293b;">Waiting for PIN</h3>
                    <p style="color: #64748b; margin-bottom: 20px;">Customer should check their phone</p>
                    <span id="mpesa_countdown_display" style="display: inline-block; background: #fef3c7; color: #d97706; font-size: 24px; font-weight: 800; padding: 12px 24px; border-radius: 10px;">2:00</span>
                    <div style="margin-top: 20px;">
                        <button type="button" id="cancel_mpesa_btn" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Cancel</button>
                    </div>
                </div>

                <div id="mpesa_success_section" style="display: none; background: white; border-radius: 12px; padding: 32px; text-align: center;">
                    <div style="width: 60px; height: 60px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-check" style="font-size: 24px; color: #10b981;"></i>
                    </div>
                    <h3 style="font-weight: 700; color: #10b981; margin-bottom: 8px;">Payment Confirmed!</h3>
                    <p style="color: #64748b; margin-bottom: 8px;">Receipt:</p>
                    <h2 id="mpesa_receipt_display" style="font-family: monospace; color: #1e293b; letter-spacing: 2px;"></h2>
                    <input type="hidden" id="mpesa_receipt_hidden">
                </div>

                <div id="mpesa_error_section" style="display: none; background: white; border-radius: 12px; padding: 32px; text-align: center;">
                    <div style="width: 60px; height: 60px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-times" style="font-size: 24px; color: #ef4444;"></i>
                    </div>
                    <h3 style="font-weight: 700; color: #ef4444; margin-bottom: 8px;">Failed</h3>
                    <p id="mpesa_error_msg" style="color: #64748b; margin-bottom: 20px;"></p>
                    <button type="button" id="retry_mpesa_btn" style="background: #4f46e5; color: white; border: none; padding: 10px 24px; border-radius: 8px; cursor: pointer; font-weight: 600;">Retry</button>
                </div>

                {{-- Manual Entry (Optional) --}}
                <div id="mpesa_manual_section" style="margin-top: 20px; background: white; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <div style="text-align: center; margin-bottom: 12px;">
                        <span style="font-size: 10px; font-weight: 700; color: #94a3b8; letter-spacing: 1px;">RECEIPT CODE (OPTIONAL)</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="mpesa_manual_receipt" placeholder="e.g. QZK92PX..." style="flex: 1; border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; font-family: monospace; font-size: 14px; font-weight: 700; text-transform: uppercase; outline: none;">
                        <button type="button" id="mpesa_manual_submit" style="background: #f1f5f9; border: 2px solid #e2e8f0; padding: 10px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; color: #64748b;">USE</button>
                    </div>
                </div>

                {{-- Auto-Match --}}
                <div id="mpesa_auto_match_section" style="margin-top: 16px; padding: 12px; border-radius: 10px; background: #fffbeb; border: 1px dashed #f59e0b;">
                    <div id="auto_match_status_waiting" style="text-align: center;">
                        <i class="fas fa-sync fa-spin" style="color: #f59e0b;"></i>
                        <span style="font-size: 11px; font-weight: 700; color: #d97706; margin-left: 6px;">Auto-detecting...</span>
                    </div>
                    <div id="detected_payment_card" style="display: none;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                            <i class="fas fa-check-circle" style="color: #10b981;"></i>
                            <span style="font-weight: 700; color: #10b981; font-size: 12px;">PAYMENT FOUND!</span>
                        </div>
                        <div style="background: white; padding: 10px; border-radius: 6px; margin-bottom: 10px; font-size: 12px;">
                            <div style="display: flex; justify-content: space-between;"><span style="color: #64748b;">Receipt:</span><span id="detected_receipt" style="font-weight: 700;"></span></div>
                            <div style="display: flex; justify-content: space-between;"><span style="color: #64748b;">Amount:</span><span id="detected_amount" style="font-weight: 700; color: #10b981;"></span></div>
                            <div style="display: flex; justify-content: space-between;"><span style="color: #64748b;">Phone:</span><span id="detected_phone" style="font-weight: 700;"></span></div>
                        </div>
                        <button type="button" id="use_detected_payment_btn" style="width: 100%; background: #4f46e5; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: 700; cursor: pointer;">USE THIS</button>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div style="padding: 16px 24px; background: #f1f5f9; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between;">
                <button type="button" data-dismiss="modal" style="background: white; border: 1px solid #e2e8f0; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="button" id="pos-save-mpesa" style="background: linear-gradient(135deg, #00B09B 0%, #96C93D 100%); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer;">
                    <i class="fas fa-check"></i> Complete Sale
                </button>
            </div>
        </div>
    </div>
</div>


<script>
(function() {
    console.log("M-Pesa script loaded");
    function initMpesa() {
        console.log("Initializing M-Pesa modal logic...");
        if (typeof jQuery === 'undefined') {
            console.log("jQuery not found, waiting...");
            setTimeout(initMpesa, 200);
            return;
        }
        
        var $ = jQuery;
        console.log("jQuery found");
        var pollInterval = null;
        var autoMatchInterval = null;
        var countdownInterval = null;
        var txnId = null;
        var detectedId = null;

        $('#mpesa_phone_input').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        function loadSummary() {
            $.get('/mpesa/daily-summary', function(r) {
                if (r.success) {
                    $('#mpesa_today_count').text(r.count || 0);
                    $('#mpesa_today_total').text('KES ' + Number(r.total || 0).toLocaleString());
                    $('#mpesa_today_pending').text(r.pending || 0);
                }
            }).fail(function() {
                $('#mpesa_today_count, #mpesa_today_total, #mpesa_today_pending').text('-');
            });
        }

        $('#refresh_mpesa_summary').click(loadSummary);

        $('#mpesa_details_modal').on('show.bs.modal', function() {
            resetModal();
            loadSummary();
            var amt = 0;
            var txt = $('#total_payable').text();
            if (txt) amt = parseFloat(txt.replace(/[^0-9.]/g, '')) || 0;
            if (amt <= 0) {
                var inp = $('#final_total_input').val();
                if (inp) amt = parseFloat(inp) || 0;
            }
            $('#mpesa_amount_display').val(amt > 0 ? amt.toFixed(2) : '0.00');
        });

        $('#send_stk_push_btn').click(function() {
            var phone = $('#mpesa_phone_input').val().trim();
            var amt = parseFloat($('#mpesa_amount_display').val().replace(/[^0-9.]/g, '')) || 0;
            if (!phone || phone.length < 9) { toastr.error('Enter valid phone'); return; }
            if (phone.startsWith('0')) phone = phone.substring(1);
            phone = '254' + phone;
            if (amt <= 0) { toastr.error('Add items first'); return; }
            showWaiting();
            $.ajax({
                url: '/mpesa/stk-push',
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), phone: phone, amount: Math.ceil(amt), reference: ($('#invoice_no').val() || 'Sale').substring(0, 12) },
                success: function(r) {
                    if (r.success) { txnId = r.mpesa_transaction_id; toastr.success('Request sent!'); startPolling(); startCountdown(); }
                    else { showError(r.message || 'Failed'); }
                },
                error: function(xhr) { showError(xhr.responseJSON?.message || 'Request failed'); }
            });
        });

        function startPolling() {
            pollInterval = setInterval(function() {
                $.post('/mpesa/check-payment-status', { _token: $('meta[name="csrf-token"]').attr('content'), mpesa_transaction_id: txnId }, function(r) {
                    if (r.is_paid) { stopTimers(); showSuccess(r.receipt_number); }
                    else if (r.status === 'failed' || r.status === 'cancelled') { stopTimers(); showError(r.result_description || 'Failed'); }
                });
            }, 3000);
        }

        function startAutoMatch() {
            stopAutoMatch();
            autoMatchInterval = setInterval(function() {
                var amt = parseFloat($('#mpesa_amount_display').val());
                if (amt > 0) {
                    $.get('/mpesa/check-matching-payment', { amount: amt }, function(r) {
                        if (r.success && r.payment) {
                            stopAutoMatch();
                            showDetectedPayment(r.payment);
                        }
                    });
                }
            }, 5000);
        }

        function stopAutoMatch() {
            if (autoMatchInterval) clearInterval(autoMatchInterval);
            autoMatchInterval = null;
        }

        function showDetectedPayment(p) {
            detectedId = p.id;
            $('#auto_match_status_waiting').hide();
            $('#detected_receipt').text(p.trans_id);
            $('#detected_amount').text('KES ' + Number(p.amount).toLocaleString());
            $('#detected_phone').text(p.phone);
            $('#detected_payment_card').slideDown();
            toastr.info('Matching payment detected!');
        }

        function startCountdown() {
            var sec = 120;
            countdownInterval = setInterval(function() {
                sec--;
                $('#mpesa_countdown_display').text(Math.floor(sec/60) + ':' + (sec%60 < 10 ? '0' : '') + (sec%60));
                if (sec <= 0) { stopTimers(); showError('Timed out'); }
            }, 1000);
        }

        function stopTimers() { if (pollInterval) clearInterval(pollInterval); if (countdownInterval) clearInterval(countdownInterval); stopAutoMatch(); pollInterval = countdownInterval = null; }
        function showWaiting() { $('#mpesa_input_section, #mpesa_manual_section, #mpesa_auto_match_section').hide(); $('#mpesa_waiting_section').show(); stopAutoMatch(); }
        function showSuccess(receipt) { $('#mpesa_waiting_section, #mpesa_auto_match_section').hide(); $('#mpesa_success_section').show(); $('#mpesa_receipt_display').text(receipt); $('#mpesa_receipt_hidden').val(receipt); $('#pos-save-mpesa').show(); toastr.success('Payment received!'); stopAutoMatch(); }
        function showError(msg) { $('#mpesa_waiting_section, #mpesa_auto_match_section').hide(); $('#mpesa_error_section').show(); $('#mpesa_error_msg').text(msg); stopAutoMatch(); }
        function resetModal() { stopTimers(); $('#mpesa_input_section, #mpesa_manual_section, #mpesa_auto_match_section').show(); $('#mpesa_waiting_section, #mpesa_success_section, #mpesa_error_section, #detected_payment_card').hide(); $('#auto_match_status_waiting').show(); $('#mpesa_phone_input, #mpesa_manual_receipt').val(''); $('#pos-save-mpesa').hide(); $('#mpesa_countdown_display').text('2:00'); txnId = detectedId = null; startAutoMatch(); }

        $('#cancel_mpesa_btn, #retry_mpesa_btn').click(resetModal);

        // Manual receipt - optional, just use if provided
        $('#mpesa_manual_submit').click(function() {
            var r = $('#mpesa_manual_receipt').val().trim().toUpperCase();
            if (r && r.length >= 6) {
                showSuccess(r);
            } else if (r) {
                toastr.error('Receipt code too short');
            }
        });

        $('#use_detected_payment_btn').click(function() { var r = $('#detected_receipt').text(); if (r) showSuccess(r); });

        // Complete sale - receipt is OPTIONAL
        $('#pos-save-mpesa').click(function() {
            var r = $('#mpesa_receipt_hidden').val() || $('#mpesa_manual_receipt').val().trim().toUpperCase() || '';
            // Set receipt if available (optional)
            $('input#transaction_no_1_0').val(r);
            $('#mpesa_details_modal').modal('hide');
            // Set payment method to mpesa
            var payment_method_dropdown = $('#payment_rows_div').find('.payment_types_dropdown').first();
            if (payment_method_dropdown.length) {
                payment_method_dropdown.val('custom_pay_1').trigger('change');
            }
            // Submit form
            setTimeout(function() {
                if (typeof pos_form_obj !== 'undefined') {
                    pos_form_obj.submit();
                } else {
                    $('form#add_pos_sell_form, form#edit_pos_sell_form').first().submit();
                }
            }, 300);
        });
    }

    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initMpesa); } else { setTimeout(initMpesa, 100); }
})();
</script>

