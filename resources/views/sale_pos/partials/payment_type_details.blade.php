<div class="payment_details_div @if( $payment_line['method'] !== 'card' ) {{ 'hide' }} @endif" data-type="card" >
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_number_$row_index", __('lang_v1.card_no')) !!}
			{!! Form::text("payment[$row_index][card_number]", $payment_line['card_number'], ['class' => 'form-control', 'placeholder' => __('lang_v1.card_no'), 'id' => "card_number_$row_index"]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_holder_name_$row_index", __('lang_v1.card_holder_name')) !!}
			{!! Form::text("payment[$row_index][card_holder_name]", $payment_line['card_holder_name'], ['class' => 'form-control', 'placeholder' => __('lang_v1.card_holder_name'), 'id' => "card_holder_name_$row_index"]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_transaction_number_$row_index",__('lang_v1.card_transaction_no')) !!}
			{!! Form::text("payment[$row_index][card_transaction_number]", $payment_line['card_transaction_number'], ['class' => 'form-control', 'placeholder' => __('lang_v1.card_transaction_no'), 'id' => "card_transaction_number_$row_index"]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_type_$row_index", __('lang_v1.card_type')) !!}
			{!! Form::select("payment[$row_index][card_type]", ['credit' => 'Credit Card', 'debit' => 'Debit Card','visa' => 'Visa', 'master' => 'MasterCard'], $payment_line['card_type'],['class' => 'form-control', 'id' => "card_type_$row_index" ]); !!}
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_month_$row_index", __('lang_v1.month')) !!}
			{!! Form::text("payment[$row_index][card_month]", $payment_line['card_month'], ['class' => 'form-control', 'placeholder' => __('lang_v1.month'),
			'id' => "card_month_$row_index" ]); !!}
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_year_$row_index", __('lang_v1.year')) !!}
			{!! Form::text("payment[$row_index][card_year]", $payment_line['card_year'], ['class' => 'form-control', 'placeholder' => __('lang_v1.year'), 'id' => "card_year_$row_index" ]); !!}
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_security_$row_index",__('lang_v1.security_code')) !!}
			{!! Form::text("payment[$row_index][card_security]", $payment_line['card_security'], ['class' => 'form-control', 'placeholder' => __('lang_v1.security_code'), 'id' => "card_security_$row_index"]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<div class="payment_details_div @if( $payment_line['method'] !== 'cheque' ) {{ 'hide' }} @endif" data-type="cheque" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("cheque_number_$row_index",__('lang_v1.cheque_no')) !!}
			{!! Form::text("payment[$row_index][cheque_number]", $payment_line['cheque_number'], ['class' => 'form-control', 'placeholder' => __('lang_v1.cheque_no'), 'id' => "cheque_number_$row_index"]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line['method'] !== 'bank_transfer' ) {{ 'hide' }} @endif" data-type="bank_transfer" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("bank_account_number_$row_index",__('lang_v1.bank_account_number')) !!}
			{!! Form::text( "payment[$row_index][bank_account_number]", $payment_line['bank_account_number'], ['class' => 'form-control', 'placeholder' => __('lang_v1.bank_account_number'), 'id' => "bank_account_number_$row_index"]); !!}
		</div>
	</div>
</div>

<div class="payment_details_div @if( $payment_line['method'] !== 'mpesa' ) {{ 'hide' }} @endif" data-type="mpesa" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("mpesa_receipt_number_$row_index", __('lang_v1.mpesa_receipt_number')) !!}
			<div class="input-group">
				{!! Form::text("payment[$row_index][mpesa_receipt_number]", $payment_line['mpesa_receipt_number'] ?? '', ['class' => 'form-control mpesa_receipt_number', 'placeholder' => __('lang_v1.mpesa_receipt_number'), 'id' => "mpesa_receipt_number_$row_index"]); !!}
				<span class="input-group-btn">
					<button type="button" class="btn btn-info btn-flat match_mpesa_payment" data-row_index="{{$row_index}}">
						<i class="fas fa-search"></i> @lang('lang_v1.match')
					</button>
                    <button type="button" class="btn btn-success btn-flat" data-toggle="modal" data-target="#mpesa_details_modal">
						<i class="fas fa-mobile-alt"></i> STK Push
					</button>
				</span>
			</div>
			<p class="help-block">Enter Receipt Number, Match payment, or trigger STK Push</p>
		</div>
	</div>
</div>

<div class="payment_details_div @if( $payment_line['method'] !== 'pesapal' ) {{ 'hide' }} @endif" data-type="pesapal" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("pesapal_reference_$row_index", 'Pesapal Reference / Order ID') !!}
			{!! Form::text("payment[$row_index][pesapal_reference]", $payment_line['transaction_no'] ?? '', ['class' => 'form-control', 'placeholder' => 'Pesapal order tracking ID', 'id' => "pesapal_reference_$row_index"]) !!}
			<p class="help-block">Enter the Pesapal order tracking ID or leave blank and complete via the Pesapal portal.</p>
		</div>
	</div>
</div>

<div class="payment_details_div @if( $payment_line['method'] !== 'kcb_buni' ) {{ 'hide' }} @endif" data-type="kcb_buni" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("kcb_buni_reference_$row_index", 'KCB Buni Reference') !!}
			{!! Form::text("payment[$row_index][kcb_buni_reference]", $payment_line['transaction_no'] ?? '', ['class' => 'form-control', 'placeholder' => 'KCB Buni transaction reference', 'id' => "kcb_buni_reference_$row_index"]) !!}
			<p class="help-block">Enter the KCB Buni payment reference number from the customer's confirmation.</p>
		</div>
	</div>
</div>

<div class="payment_details_div @if( $payment_line['method'] !== 'custom_pay_1' ) {{ 'hide' }} @endif" data-type="custom_pay_1" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("transaction_no_1_$row_index", "Transaction Details") !!}
			{!! Form::text("payment[$row_index][transaction_no_1]", $payment_line['transaction_no'], ['class' => 'form-control', 'placeholder' => "Transaction Details (Optional)", 'id' => "transaction_no_1_$row_index"]); !!}
		</div>
	</div>
</div>

@for ($i = 2; $i < 8; $i++)
<div class="payment_details_div @if( $payment_line['method'] !== 'custom_pay_' . $i ) {{ 'hide' }} @endif" data-type="custom_pay_{{$i}}" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("transaction_no_{$i}_{$row_index}", __('lang_v1.transaction_no')) !!}
			{!! Form::text("payment[$row_index][transaction_no_{$i}]", $payment_line['transaction_no'], ['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no'), 'id' => "transaction_no_{$i}_{$row_index}"]); !!}
		</div>
	</div>
</div>
@endfor