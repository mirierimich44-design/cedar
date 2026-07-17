@extends('layouts.app')
@section('title', 'Daily Product Profit Report')

@section('content')

<div class="report-page-modern">
    <div class="rpt-banner">
        <div class="rpt-banner-inner">
            <div class="rpt-banner-title">
                <span class="rpt-banner-icon"><i class="fas fa-chart-area"></i></span>
                <div>
                    <h1>{{ 'Daily Product Profit Report' }}</h1>
                    <p class="rpt-subtitle">{{ session()->get('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>



<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => '#', 'method' => 'get', 'id' => 'daily_product_profit_report_filter_form' ]) !!}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('dppr_date_filter', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'dppr_date_filter', 'readonly']); !!}
                    </div>
                </div>
              {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="daily_product_profit_report_table">
                        <thead>
                            <tr>
                                <th>@lang('messages.date')</th>
                                <th>@lang('product.sku')</th>
                                <th>@lang('sale.product')</th>
                                <th>@lang('lang_v1.quantity')</th>
                                @can('view_profit')
                                <th>@lang('lang_v1.unit_perchase_price')</th>
                                @endcan
                                <th>@lang('lang_v1.selling_price')</th>
                                @can('view_profit')
                                <th>Profit</th>
                                <th>Profit %</th>
                                @endcan
                                <th>@lang('sale.total_amount')</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td colspan="3"><strong>@lang('sale.total'):</strong></td>
                                <td id="footer_total_quantity"></td>
                                @can('view_profit')
                                <td></td>
                                @endcan
                                <td></td>
                                @can('view_profit')
                                <td id="footer_total_profit"></td>
                                <td id="footer_percentage_profit"></td>
                                @endcan
                                <td id="footer_total_amount"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

</div>



@endsection

@section('javascript')
<script>
    $(document).ready(function(){
        if($('#dppr_date_filter').length == 1){
            $('#dppr_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#dppr_date_filter').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                daily_product_profit_report_table.ajax.reload();
            });
            $('#dppr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#dppr_date_filter').val('');
                daily_product_profit_report_table.ajax.reload();
            });
            $('#dppr_date_filter').data('daterangepicker').setStartDate(moment().startOf('month'));
            $('#dppr_date_filter').data('daterangepicker').setEndDate(moment().endOf('month'));
        }

        var daily_product_profit_report_table = $('#daily_product_profit_report_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/reports/daily-product-profit",
                "data": function ( d ) {
                    if($('#dppr_date_filter').val()) {
                        var start = $('#dppr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#dppr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'sku', name: 'sku' },
                { data: 'product_name', name: 'products.name' },
                { data: 'quantity', name: 'transaction_sell_lines.quantity', className: 'quantity' },
                @can('view_profit')
                { data: 'unit_purchase_price', name: 'unit_purchase_price' },
                @endcan
                { data: 'unit_sale_price', name: 'unit_sale_price' },
                @can('view_profit')
                { data: 'total_profit', name: 'total_profit', className: 'total_profit' },
                { data: 'profit_percentage', name: 'profit_percentage', className: 'profit_percentage' },
                @endcan
                { data: 'total_amount', name: 'total_amount', className: 'total_amount' }
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#daily_product_profit_report_table'));
            },
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api();
                var canViewProfit = {{ auth()->user()->can('view_profit') ? 'true' : 'false' }};

                // Helper to unformat HTML string to number
                var intVal = function ( i ) {
                    if(typeof i === 'string'){
                         // Strip HTML tags
                         i = i.replace(/<[^>]+>/g, '');
                         return __number_uf(i);
                    }
                    return typeof i === 'number' ? i : 0;
                };

                // Total quantity
                var total_quantity = 0;
                var total_profit = 0;
                var total_amount = 0;

                for (var i = 0; i < data.length; i++) {
                    total_quantity += intVal(data[i].quantity);
                    if (canViewProfit) {
                        total_profit += intVal(data[i].total_profit);
                    }
                    total_amount += intVal(data[i].total_amount);
                }

                $('#footer_total_quantity').html(__number_f(total_quantity, false));
                $('#footer_total_amount').html(__currency_trans_from_en(total_amount));

                if (canViewProfit) {
                    $('#footer_total_profit').html(__currency_trans_from_en(total_profit));
                    var percentage_profit = 0;
                    if(total_amount > 0){
                        percentage_profit = (total_profit / total_amount) * 100;
                    }
                    $('#footer_percentage_profit').html(__number_f(percentage_profit, 2) + '%');
                }
            }
        });
    });
</script>



@section('css')
@includeIf('report.partials.report_modern_css')
@endsection
@endsection