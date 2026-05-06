@extends('layouts.app')
@section('title', __('expense.expenses'))

@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')
<div class="page-modern">

    <section class="content-header"></section>

    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h1>@lang('expense.expenses')</h1>
                    <p class="pg-subtitle">@lang('expense.all_expenses') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                @can('expense.add')
                    <a class="pg-add-btn"
                        href="{{ action([\App\Http\Controllers\ExpenseController::class, 'create']) }}">
                        <i class="fas fa-plus"></i> @lang('messages.add')
                    </a>
                    <a class="pg-glass-btn"
                        href="{{ action([\App\Http\Controllers\ExpenseController::class, 'importExpense']) }}">
                        <i class="fas fa-file-import"></i> @lang('expense.import_expense')
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        @component('components.filters', ['title' => __('report.filters')])
            @if(auth()->user()->can('all_expense.access'))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('expense_for', __('expense.expense_for') . ':') !!}
                        {!! Form::select('expense_for', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('created_by', __('lang_v1.added_by') . ':') !!}
                        {!! Form::select('created_by', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('expense_contact_filter', __('contact.contact') . ':') !!}
                        {!! Form::select('expense_contact_filter', $contacts, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
            @endif
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_category_id', __('expense.expense_category') . ':') !!}
                    {!! Form::select('expense_category_id', $categories, null, ['placeholder' => __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'expense_category_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_sub_category_id_filter', __('product.sub_category') . ':') !!}
                    {!! Form::select('expense_sub_category_id_filter', $sub_categories, null, ['placeholder' => __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'expense_sub_category_id_filter']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_payment_status', __('purchase.payment_status') . ':') !!}
                    {!! Form::select('expense_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __('expense.all_expenses')])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="expense_table">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('purchase.ref_no')</th>
                            <th>@lang('lang_v1.recur_details')</th>
                            <th>@lang('expense.expense_category')</th>
                            <th>@lang('product.sub_category')</th>
                            <th>@lang('business.location')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('product.tax')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('purchase.payment_due')</th>
                            <th>@lang('expense.expense_for')</th>
                            <th>@lang('contact.contact')</th>
                            <th>@lang('expense.expense_note')</th>
                            <th>@lang('lang_v1.added_by')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="7"><strong>@lang('sale.total'):</strong></td>
                            <td class="footer_payment_status_count"></td>
                            <td></td>
                            <td class="footer_expense_total"></td>
                            <td class="footer_total_due"></td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endcomponent
    </section>

    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

</div>{{-- .page-modern --}}
@stop

@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.pay_expense_mpesa', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route("mpesa.payment-modal") }}',
                data: { transaction_id: $(this).data('id'), transaction_type: 'expense' },
                dataType: 'html',
                success: function(result) { $('#mpesa_payment_modal').html(result).modal('show'); }
            });
        });
    });
</script>
@endsection
