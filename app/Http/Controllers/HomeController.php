<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Charts\CommonChart;
use App\Currency;
use App\Media;
use App\Transaction;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\RestaurantUtil;
use App\Utils\TransactionUtil;
use App\Utils\ProductUtil;
use App\Utils\Util;
use App\VariationLocationDetails;
use Datatables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataExport;

class HomeController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $businessUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    protected $commonUtil;

    protected $restUtil;
    protected $productUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil,
        Util $commonUtil,
        RestaurantUtil $restUtil,
        ProductUtil $productUtil,
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
        $this->productUtil = $productUtil;
    }

    public function getMorningDigest(Request $request)
    {
        if (!auth()->user()->can('dashboard.data')) {
            return response()->json(['error' => 'Unauthorized']);
        }

        $business_id = $request->session()->get('user.business_id');
        $yesterday = \Carbon::now()->subDay()->format('Y-m-d');
        $today = \Carbon::now()->format('Y-m-d');
        
        // 1. Yesterday Sales
        $yesterday_sales = \App\Transaction::where('business_id', $business_id)
            ->whereDate('transaction_date', $yesterday)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->sum('final_total');

        // 1.b Today Sales
        $today_sales = \App\Transaction::where('business_id', $business_id)
            ->whereDate('transaction_date', $today)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->sum('final_total');

        // 1.c Unpaid Invoices Count (Due)
        $unpaid_invoices_count = \App\Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereIn('payment_status', ['due', 'partial'])
            ->count();

        // 1.d Total Customers Today (Total invoices/foot traffic today)
        $total_customers_today = \App\Transaction::where('business_id', $business_id)
            ->whereDate('transaction_date', $today)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->count();

        // 1.e Current Cash in Register (for the logged in user)
        $user_id = auth()->user()->id;
        $register_details = \App\CashRegister::leftjoin('cash_register_transactions as ct', 'ct.cash_register_id', '=', 'cash_registers.id')
            ->where('cash_registers.user_id', $user_id)
            ->where('cash_registers.status', 'open')
            ->select(
                DB::raw("SUM(IF(transaction_type='initial', amount, 0)) as cash_in_hand"),
                DB::raw("SUM(IF(pay_method='cash', IF(transaction_type='sell', amount, 0), 0)) as total_cash"),
                DB::raw("SUM(IF(pay_method='cash', IF(transaction_type='expense', amount, 0), 0)) as total_cash_expense"),
                DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='cash', amount, 0), 0)) as total_cash_refund")
            )->first();
            
        $current_cash_in_register = 0;
        if ($register_details) {
            $current_cash_in_register = $register_details->cash_in_hand + $register_details->total_cash - $register_details->total_cash_expense - $register_details->total_cash_refund;
        }

        // 2. Expiry Risk (Next 30 days)
        $expiry_date_limit = \Carbon::now()->addDays(30)->format('Y-m-d');
        
        $expiry_risk = \App\PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
            ->join('products as p', 'purchase_lines.product_id', '=', 'p.id')
            ->where('t.business_id', $business_id)
            ->where('p.enable_stock', 1)
            ->whereNotNull('purchase_lines.exp_date')
            ->whereDate('purchase_lines.exp_date', '<=', $expiry_date_limit)
            ->whereRaw('purchase_lines.quantity > (COALESCE(purchase_lines.quantity_sold, 0) + COALESCE(purchase_lines.quantity_adjusted, 0) + COALESCE(purchase_lines.quantity_returned, 0))')
            ->select([
                DB::raw('SUM(purchase_lines.purchase_price_inc_tax * (purchase_lines.quantity - COALESCE(purchase_lines.quantity_sold, 0) - COALESCE(purchase_lines.quantity_adjusted, 0) - COALESCE(purchase_lines.quantity_returned, 0))) as risk_value'),
                DB::raw('COUNT(DISTINCT purchase_lines.product_id) as item_count')
            ])->first();

        // 3. Top Product Yesterday
        $top_product = \App\TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
            ->where('transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', $yesterday)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->select('products.name', DB::raw('SUM(transaction_sell_lines.quantity) as qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->first();

        // 4. Dead Stock Alert (> 90 days no sale)
        $ninety_days_ago = \Carbon::now()->subDays(90)->format('Y-m-d');
        
        $products_with_stock = \App\VariationLocationDetails::join('products as p', 'variation_location_details.product_id', '=', 'p.id')
            ->where('p.business_id', $business_id)
            ->where('variation_location_details.qty_available', '>', 0)
            ->pluck('variation_location_details.variation_id')
            ->unique();

        $recently_sold_variations = \App\TransactionSellLine::join('transactions as t', 'transaction_sell_lines.transaction_id', '=', 't.id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->whereDate('t.transaction_date', '>=', $ninety_days_ago)
            ->pluck('transaction_sell_lines.variation_id')
            ->unique();

        $dead_stock_count = $products_with_stock->diff($recently_sold_variations)->count();

        // 5. Low Stock Alerts
        $low_stock_alerts_count = \App\VariationLocationDetails::join('product_variations', 'variation_location_details.product_variation_id', '=', 'product_variations.id')
            ->join('products as p', 'variation_location_details.product_id', '=', 'p.id')
            ->where('p.business_id', $business_id)
            ->where('p.enable_stock', 1)
            ->where('p.is_inactive', 0)
            ->whereNotNull('p.alert_quantity')
            ->whereRaw('variation_location_details.qty_available <= p.alert_quantity')
            ->count();

        return response()->json([
            'yesterday_sales' => $yesterday_sales,
            'today_sales' => $today_sales,
            'unpaid_invoices_count' => $unpaid_invoices_count,
            'total_customers_today' => $total_customers_today,
            'current_cash_in_register' => $current_cash_in_register,
            'expiry_risk_value' => $expiry_risk ? $expiry_risk->risk_value : 0,
            'expiry_item_count' => $expiry_risk ? $expiry_risk->item_count : 0,
            'top_product_name' => $top_product ? $top_product->name : 'N/A',
            'dead_stock_count' => $dead_stock_count,
            'low_stock_alerts_count' => $low_stock_alerts_count
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $__mark = function(string $label): void {};
        $user = auth()->user();
        if ($user->user_type == 'user_customer') {
            return redirect()->action([\Modules\Crm\Http\Controllers\DashboardController::class, 'index']);
        }

        if (empty($user->business_id)) {
            return redirect('/saas-admin');
        }

        $business_id = request()->session()->get('user.business_id');

        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! auth()->user()->can('dashboard.data')) {
            return view('home.index');
        }

        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();
        //ensure start date starts from at least 30 days before to get sells last 30 days
        $least_30_days = \Carbon::parse($fy['start'])->subDays(30)->format('Y-m-d');

        //get all sells
        $sells_this_fy = $this->transactionUtil->getSellsCurrentFy($business_id, $least_30_days, $fy['end']);

        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();

        //Chart for sells last 30 days
        $labels = [];
        $all_sell_values = [];
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            $labels[] = date('j M Y', strtotime($date));

            $total_sell_on_date = $sells_this_fy->where('date', $date)->sum('total_sells');

            if (! empty($total_sell_on_date)) {
                $all_sell_values[] = (float) $total_sell_on_date;
            } else {
                $all_sell_values[] = 0;
            }
        }

        //Group sells by location
        $location_sells = [];
        foreach ($all_locations as $loc_id => $loc_name) {
            $values = [];
            foreach ($dates as $date) {
                $total_sell_on_date_location = $sells_this_fy->where('date', $date)->where('location_id', $loc_id)->sum('total_sells');

                if (! empty($total_sell_on_date_location)) {
                    $values[] = (float) $total_sell_on_date_location;
                } else {
                    $values[] = 0;
                }
            }
            $location_sells[$loc_id]['loc_label'] = $loc_name;
            $location_sells[$loc_id]['values'] = $values;
        }

        $sells_chart_1 = new CommonChart;

        $sells_chart_1->labels($labels)
                        ->options($this->__chartOptions(__(
                            'home.total_sells',
                            ['currency' => $currency->code]
                            )));

        if (! empty($location_sells)) {
            foreach ($location_sells as $location_sell) {
                $sells_chart_1->dataset($location_sell['loc_label'], 'line', $location_sell['values']);
            }
        }

        if (count($all_locations) > 1) {
            $sells_chart_1->dataset(__('report.all_locations'), 'line', $all_sell_values);
        }


        $labels = [];
        $values = [];
        $date = strtotime($fy['start']);
        $last = date('m-Y', strtotime($fy['end']));
        $fy_months = [];
        do {
            $month_year = date('m-Y', $date);
            $fy_months[] = $month_year;

            $labels[] = \Carbon::createFromTimestamp($date)->format('M-Y');
            $date = strtotime('+1 month', $date);

            $total_sell_in_month_year = $sells_this_fy->where('yearmonth', $month_year)->sum('total_sells');

            if (! empty($total_sell_in_month_year)) {
                $values[] = (float) $total_sell_in_month_year;
            } else {
                $values[] = 0;
            }
        } while ($month_year != $last);

        $fy_sells_by_location_data = [];

        foreach ($all_locations as $loc_id => $loc_name) {
            $values_data = [];
            foreach ($fy_months as $month) {
                $total_sell_in_month_year_location = $sells_this_fy->where('yearmonth', $month)->where('location_id', $loc_id)->sum('total_sells');

                if (! empty($total_sell_in_month_year_location)) {
                    $values_data[] = (float) $total_sell_in_month_year_location;
                } else {
                    $values_data[] = 0;
                }
            }
            $fy_sells_by_location_data[$loc_id]['loc_label'] = $loc_name;
            $fy_sells_by_location_data[$loc_id]['values'] = $values_data;
        }

        $sells_chart_2 = new CommonChart;
        $sells_chart_2->labels($labels)
                    ->options($this->__chartOptions(__(
                        'home.total_sells',
                        ['currency' => $currency->code]
                            )));
        if (! empty($fy_sells_by_location_data)) {
            foreach ($fy_sells_by_location_data as $location_sell) {
                $sells_chart_2->dataset($location_sell['loc_label'], 'line', $location_sell['values']);
            }
        }
        if (count($all_locations) > 1) {
            $sells_chart_2->dataset(__('report.all_locations'), 'line', $values);
        }


        //Get Dashboard widgets from module
        $module_widgets = $this->moduleUtil->getModuleData('dashboard_widget');
        $__mark('module widgets');

        $widgets = [];

        foreach ($module_widgets as $widget_array) {
            if (! empty($widget_array['position'])) {
                $widgets[$widget_array['position']][] = $widget_array['widget'];
            }
        }

        $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];

        $start_date = \Carbon::now()->subDays(30)->format('Y-m-d');
        $end_date = \Carbon::now()->format('Y-m-d');
        $fy_start = $fy['start'];
        $fy_end = $fy['end'];

        // Staff Performance Chart
        $staff_performance_data = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('transaction_date', [$start_date, $end_date])
            ->select(
                DB::raw('SUM(final_total) as total_sales'),
                'created_by'
            )
            ->groupBy('created_by')
            ->with(['created_by_user'])
            ->get();

        $staff_labels = [];
        $staff_values = [];
        foreach ($staff_performance_data as $data) {
            $staff_labels[] = $data->created_by_user->user_full_name ?? $data->created_by;
            $staff_values[] = (float) $data->total_sales;
        }

        $staff_performance_chart = new CommonChart;
        $staff_performance_chart->labels($staff_labels)
            ->options($this->__chartOptions(__('lang_v1.total_sales')))
            ->dataset(__('lang_v1.total_sales'), 'bar', $staff_values);
        $__mark('staff_performance_chart');

        // Profit Margins Chart — CACHED for 1 hour per business.
        // getProfitLossDetails() is expensive (joins transactions + purchases + expenses).
        // Running it once per month across the FY was taking 100+ seconds on the dashboard.
        // Cached per (business_id, fy_start, fy_end); invalidate by clearing cache:
        //   php artisan cache:forget "home_profit_chart_{$business_id}_{$fy_start}_{$fy_end}"
        $profit_cache_key = "home_profit_chart_{$business_id}_{$fy_start}_{$fy_end}";
        [$labels, $profit_values] = \Cache::remember($profit_cache_key, now()->addHour(), function () use ($fy_start, $fy_end, $business_id) {
            $labels        = [];
            $profit_values = [];

            $current_month = \Carbon::parse($fy_start)->startOfMonth();
            $end_month     = \Carbon::parse($fy_end)->startOfMonth();

            while ($current_month <= $end_month) {
                $labels[] = $current_month->format('M-Y');

                $month_start = $current_month->copy()->startOfMonth()->format('Y-m-d');
                $month_end   = $current_month->copy()->endOfMonth()->format('Y-m-d');

                $gross_profit_data = $this->transactionUtil->getProfitLossDetails($business_id, null, $month_start, $month_end);
                $profit_values[]   = (float) ($gross_profit_data['gross_profit'] ?? 0);

                $current_month->addMonth();
            }

            return [$labels, $profit_values];
        });

        $__mark('profit_margin_loop');

        $profit_margin_chart = new CommonChart;
        $profit_margin_chart->labels($labels)
            ->options($this->__chartOptions(__('lang_v1.gross_profit')))
            ->dataset(__('lang_v1.gross_profit'), 'line', $profit_values);
        $__mark('profit_margin_chart built');

        return view('home.index', compact('sells_chart_1', 'sells_chart_2', 'widgets', 'all_locations', 'common_settings', 'is_admin', 'staff_performance_chart', 'profit_margin_chart'));
    }

    /**
     * Retrieves purchase and sell details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotals()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $location_id = request()->location_id;
            $business_id = request()->session()->get('user.business_id');

            // get user id parameter
            $created_by = request()->user_id;

            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start, $end, $location_id, $created_by);

            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start, $end, $location_id, $created_by);

            $total_ledger_discount = $this->transactionUtil->getTotalLedgerDiscount($business_id, $start, $end);

            $purchase_details['purchase_due'] = $purchase_details['purchase_due'] - $total_ledger_discount['total_purchase_discount'];

            $transaction_types = [
                'purchase_return', 'sell_return', 'expense',
            ];

            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start,
                $end,
                $location_id,
                $created_by
            );

            $total_purchase_inc_tax = ! empty($purchase_details['total_purchase_inc_tax']) ? $purchase_details['total_purchase_inc_tax'] : 0;
            $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'];

            $output = $purchase_details;
            $output['total_purchase'] = $total_purchase_inc_tax;
            $output['total_purchase_return'] = $total_purchase_return_inc_tax;
            $output['total_purchase_return_paid'] = $this->transactionUtil->getTotalPurchaseReturnPaid($business_id, $start, $end, $location_id);

            $total_sell_inc_tax = ! empty($sell_details['total_sell_inc_tax']) ? $sell_details['total_sell_inc_tax'] : 0;
            $total_sell_return_inc_tax = ! empty($transaction_totals['total_sell_return_inc_tax']) ? $transaction_totals['total_sell_return_inc_tax'] : 0;
            $output['total_sell_return_paid'] = $this->transactionUtil->getTotalSellReturnPaid($business_id, $start, $end, $location_id);

            $output['total_sell'] = $total_sell_inc_tax;
            $output['total_sell_return'] = $total_sell_return_inc_tax;

            $output['invoice_due'] = $sell_details['invoice_due'] - $total_ledger_discount['total_sell_discount'];
            $output['total_expense'] = $transaction_totals['total_expense'];

            //NET = TOTAL SALES - INVOICE DUE - EXPENSE
            $output['net'] = $output['total_sell'] - $output['invoice_due'] - $output['total_expense'];

            return $output;
        }
    }

    /**
     * Retrieves sell products whose available quntity is less than alert quntity.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductStockAlert()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $permitted_locations = auth()->user()->permitted_locations();
            $products = $this->productUtil->getProductAlert($business_id, $permitted_locations);

            // Get pending orders count per product/variation
            $pending_orders = \DB::table('pos_order_lines')
                ->join('pos_orders', 'pos_order_lines.order_id', '=', 'pos_orders.id')
                ->where('pos_orders.business_id', $business_id)
                ->whereIn('pos_orders.status', ['pending', 'processing'])
                ->whereNotNull('pos_order_lines.product_id')
                ->select(
                    'pos_order_lines.product_id',
                    'pos_order_lines.variation_id',
                    \DB::raw('SUM(pos_order_lines.quantity) as order_qty'),
                    \DB::raw('COUNT(DISTINCT pos_orders.id) as order_count')
                )
                ->groupBy('pos_order_lines.product_id', 'pos_order_lines.variation_id')
                ->get()
                ->keyBy(function ($item) {
                    return $item->product_id . '_' . $item->variation_id;
                });

            // Get pending followups count per product/variation
            $pending_followups = \DB::table('followups')
                ->where('business_id', $business_id)
                ->whereIn('status', ['pending', 'contacted'])
                ->whereNotNull('product_id')
                ->select(
                    'product_id',
                    'variation_id',
                    \DB::raw('SUM(quantity) as followup_qty'),
                    \DB::raw('COUNT(*) as followup_count')
                )
                ->groupBy('product_id', 'variation_id')
                ->get()
                ->keyBy(function ($item) {
                    return $item->product_id . '_' . ($item->variation_id ?? 0);
                });

            return Datatables::of($products)
                ->editColumn('product', function ($row) use ($pending_orders, $pending_followups) {
                    $productName = $row->type == 'single'
                        ? $row->product . ' (' . $row->sku . ')'
                        : $row->product . ' - ' . $row->product_variation . ' - ' . $row->variation . ' (' . $row->sub_sku . ')';

                    // Check for pending orders/followups
                    $key = $row->product_id . '_' . $row->variation_id;
                    $badges = '';

                    if (isset($pending_orders[$key]) && $pending_orders[$key]->order_count > 0) {
                        $badges .= ' <span class="badge bg-blue" title="' . $pending_orders[$key]->order_qty . ' qty in ' . $pending_orders[$key]->order_count . ' orders"><i class="fa fa-shopping-cart"></i> ' . $pending_orders[$key]->order_count . '</span>';
                    }

                    if (isset($pending_followups[$key]) && $pending_followups[$key]->followup_count > 0) {
                        $badges .= ' <span class="badge bg-purple" title="' . $pending_followups[$key]->followup_qty . ' qty in ' . $pending_followups[$key]->followup_count . ' followups"><i class="fa fa-phone"></i> ' . $pending_followups[$key]->followup_count . '</span>';
                    }

                    return $productName . $badges;
                })
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;

                    return '<span data-is_quantity="true" data-orig-value="' . (float) $stock . '" class="display_currency" data-currency_symbol=false>' . (float) $stock . '</span> ' . $row->unit;
                })
                ->removeColumn('sku')
                ->removeColumn('sub_sku')
                ->removeColumn('unit')
                ->removeColumn('type')
                ->removeColumn('product_variation')
                ->removeColumn('variation')
                ->rawColumns([0, 2])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchasePaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format('Y-m-d H:i:s');

            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->input('location_id'))) {
                $query->where('transactions.location_id', request()->input('location_id'));
            }

            $dues = $query->select(
                'transactions.id as id',
                'c.name as supplier',
                'c.supplier_business_name',
                'ref_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = ! empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;

                    return '<span class="display_currency" data-currency_symbol="true">'.
                    $due.'</span>';
                })
                ->addColumn('action', '@can("purchase.create") <a href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'addPayment\'], [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endcan')
                ->removeColumn('supplier_business_name')
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$supplier}}')
                ->editColumn('ref_no', function ($row) {
                    if (auth()->user()->can('purchase.view')) {
                        return  '<a href="#" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->id]).'"
                                    class="btn-modal" data-container=".view_modal">'.$row->ref_no.'</a>';
                    }

                    return $row->ref_no;
                })
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesPaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format('Y-m-d H:i:s');

            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->input('location_id'))) {
                $query->where('transactions.location_id', request()->input('location_id'));
            }

            $dues = $query->select(
                'transactions.id as id',
                'c.name as customer',
                'c.supplier_business_name',
                'transactions.invoice_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = ! empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;

                    return '<span class="display_currency" data-currency_symbol="true">'.
                    $due.'</span>';
                })
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view')) {
                        return  '<a href="#" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]).'"
                                    class="btn-modal" data-container=".view_modal">'.$row->invoice_no.'</a>';
                    }

                    return $row->invoice_no;
                })
                ->addColumn('action', '@if(auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access")) <a href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'addPayment\'], [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endif')
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$customer}}')
                ->removeColumn('supplier_business_name')
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    public function loadMoreNotifications()
    {
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'DESC')->paginate(10);

        if (request()->input('page') == 1) {
            auth()->user()->unreadNotifications->markAsRead();
        }
        $notifications_data = $this->commonUtil->parseNotifications($notifications);

        return view('layouts.partials.notification_list', compact('notifications_data'));
    }

    /**
     * Function to count total number of unread notifications
     *
     * @return json
     */
    public function getTotalUnreadNotifications()
    {
        $unread_notifications = auth()->user()->unreadNotifications;
        $total_unread = $unread_notifications->count();

        $notification_html = '';
        $modal_notifications = [];
        foreach ($unread_notifications as $unread_notification) {
            if (isset($data['show_popup'])) {
                $modal_notifications[] = $unread_notification;
                $unread_notification->markAsRead();
            }
        }
        if (! empty($modal_notifications)) {
            $notification_html = view('home.notification_modal')->with(['notifications' => $modal_notifications])->render();
        }

        return [
            'total_unread' => $total_unread,
            'notification_html' => $notification_html,
        ];
    }

    private function __chartOptions($title)
    {
        return [
            'chart' => [
                'style' => [
                    'fontFamily' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                ],
                'backgroundColor' => 'transparent',
                'spacing' => [10, 10, 15, 10],
            ],
            'title' => [
                'text' => null,
            ],
            'yAxis' => [
                'title' => [
                    'text' => $title,
                    'style' => [
                        'color' => '#64748b',
                        'fontSize' => '12px',
                        'fontWeight' => '500',
                    ],
                ],
                'gridLineColor' => '#f1f5f9',
                'gridLineDashStyle' => 'Dash',
                'labels' => [
                    'style' => [
                        'color' => '#94a3b8',
                        'fontSize' => '11px',
                    ],
                ],
            ],
            'xAxis' => [
                'lineColor' => '#e2e8f0',
                'tickColor' => '#e2e8f0',
                'labels' => [
                    'style' => [
                        'color' => '#64748b',
                        'fontSize' => '11px',
                    ],
                ],
            ],
            'legend' => [
                'align' => 'right',
                'verticalAlign' => 'top',
                'floating' => true,
                'layout' => 'vertical',
                'padding' => 12,
                'itemStyle' => [
                    'color' => '#475569',
                    'fontSize' => '12px',
                    'fontWeight' => '500',
                ],
                'itemHoverStyle' => [
                    'color' => '#1e293b',
                ],
            ],
            'tooltip' => [
                'backgroundColor' => '#1e293b',
                'borderColor' => '#334155',
                'borderRadius' => 8,
                'shadow' => true,
                'style' => [
                    'color' => '#fff',
                    'fontSize' => '12px',
                ],
            ],
            'plotOptions' => [
                'line' => [
                    'lineWidth' => 2.5,
                    'marker' => [
                        'radius' => 4,
                        'symbol' => 'circle',
                    ],
                    'states' => [
                        'hover' => [
                            'lineWidth' => 3,
                        ],
                    ],
                ],
                'bar' => [
                    'borderRadius' => 4,
                    'borderWidth' => 0,
                    'groupPadding' => 0.15,
                ],
                'column' => [
                    'borderRadius' => 4,
                    'borderWidth' => 0,
                ],
            ],
            'colors' => ['#059669', '#2563eb', '#7c3aed', '#d97706', '#dc2626', '#0ea5e9', '#ec4899', '#14b8a6'],
            'credits' => [
                'enabled' => false,
            ],
        ];
    }

    public function getCalendar()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->restUtil->is_admin(auth()->user(), $business_id);
        $is_superadmin = auth()->user()->can('superadmin');
        if (request()->ajax()) {
            $data = [
                'start_date' => request()->start,
                'end_date' => request()->end,
                'user_id' => ($is_admin || $is_superadmin) && ! empty(request()->user_id) ? request()->user_id : auth()->user()->id,
                'location_id' => ! empty(request()->location_id) ? request()->location_id : null,
                'business_id' => $business_id,
                'events' => request()->events ?? [],
                'color' => '#007FFF',
            ];
            $events = [];

            if (in_array('bookings', $data['events'])) {
                $events = $this->restUtil->getBookingsForCalendar($data);
            }

            $module_events = $this->moduleUtil->getModuleData('calendarEvents', $data);

            foreach ($module_events as $module_event) {
                $events = array_merge($events, $module_event);
            }

            return $events;
        }

        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();
        $users = [];
        if ($is_admin) {
            $users = User::forDropdown($business_id, false);
        }

        $event_types = [
            'bookings' => [
                'label' => __('restaurant.bookings'),
                'color' => '#007FFF',
            ],
        ];
        $module_event_types = $this->moduleUtil->getModuleData('eventTypes');
        foreach ($module_event_types as $module_event_type) {
            $event_types = array_merge($event_types, $module_event_type);
        }

        return view('home.calendar')->with(compact('all_locations', 'users', 'event_types'));
    }

    public function showNotification($id)
    {
        $notification = DatabaseNotification::find($id);

        $data = $notification->data;

        $notification->markAsRead();

        return view('home.notification_modal')->with([
            'notifications' => [$notification],
        ]);
    }

    public function attachMediasToGivenModel(Request $request)
    {
        if ($request->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $model_id = $request->input('model_id');
                $model = $request->input('model_type');
                $model_media_type = $request->input('model_media_type');

                DB::beginTransaction();

                //find model to which medias are to be attached
                $model_to_be_attached = $model::where('business_id', $business_id)
                                        ->findOrFail($model_id);

                Media::uploadMedia($business_id, $model_to_be_attached, $request, 'file', false, $model_media_type);

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (Exception $e) {
                DB::rollBack();

                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function getUserLocation($latlng)
    {
        $latlng_array = explode(',', $latlng);

        $response = $this->moduleUtil->getLocationFromCoordinates($latlng_array[0], $latlng_array[1]);

        return ['address' => $response];
    }

    /**
     * Get live stats for real-time dashboard updates
     */
    public function getLiveStats()
    {
        if (!request()->ajax()) {
            abort(404);
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = request()->location_id;
        $today = \Carbon::now()->format('Y-m-d');

        // Today's sales
        $sales_query = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereDate('transaction_date', $today);

        if (!empty($location_id)) {
            $sales_query->where('location_id', $location_id);
        }

        $today_sales = $sales_query->sum('final_total');
        $today_transactions = $sales_query->count();

        // Last sale
        $last_sale = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->latest('transaction_date')
            ->first();

        return response()->json([
            'today_sales' => number_format($today_sales, 2),
            'today_transactions' => $today_transactions,
            'last_sale_amount' => $last_sale ? number_format($last_sale->final_total, 2) : '0.00',
            'last_sale_time' => $last_sale ? \Carbon::parse($last_sale->transaction_date)->diffForHumans() : 'No sales today',
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    /**
     * Get best selling products for the week
     */
    public function getBestSellers()
    {
        if (!request()->ajax() && request()->get('export_type') != 'excel') {
            abort(404);
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = request()->location_id;
        $start_date = \Carbon::now()->subDays(7)->format('Y-m-d');
        $end_date = \Carbon::now()->format('Y-m-d');

        $query = DB::table('transaction_sell_lines as tsl')
            ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
            ->join('products as p', 'tsl.product_id', '=', 'p.id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->whereDate('t.transaction_date', '>=', $start_date)
            ->whereDate('t.transaction_date', '<=', $end_date);

        if (!empty($location_id)) {
            $query->where('t.location_id', $location_id);
        }

        $best_sellers_query = $query->select(
                'p.id',
                'p.name',
                'p.sku',
                'p.image',
                DB::raw('SUM(tsl.quantity) as total_qty'),
                DB::raw('SUM(tsl.unit_price_inc_tax * tsl.quantity) as total_revenue')
            )
            ->groupBy('p.id', 'p.name', 'p.sku', 'p.image')
            ->orderBy('total_qty', 'desc');

        if (request()->ajax() && request()->has('draw')) {
            return Datatables::of($best_sellers_query)
                ->editColumn('total_qty', function ($row) {
                    return (float) $row->total_qty;
                })
                ->editColumn('total_revenue', function ($row) {
                    return (float) $row->total_revenue;
                })
                ->make(true);
        }

        $best_sellers = $best_sellers_query->limit(10)->get();

        if (request()->get('export_type') == 'excel') {
            $data = [];
            foreach ($best_sellers as $row) {
                $data[] = [
                    $row->name,
                    $row->sku,
                    (float) $row->total_qty,
                    (float) $row->total_revenue
                ];
            }
            return Excel::download(new DataExport($data, ['Product', 'SKU', 'Total Sold', 'Total Revenue']), 'best_sellers_weekly.xlsx');
        }

        return response()->json(['data' => $best_sellers]);
    }

    /**
     * Get products expiring soon
     */
    public function getExpiringProducts()
    {
        if (!request()->ajax() && request()->get('export_type') != 'excel') {
            abort(404);
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = request()->location_id;
        $days = request()->days ?? 30;
        $expiry_date = \Carbon::now()->addDays($days)->format('Y-m-d');

        $query = DB::table('purchase_lines as pl')
            ->join('transactions as t', 'pl.transaction_id', '=', 't.id')
            ->join('products as p', 'pl.product_id', '=', 'p.id')
            ->join('variations as v', 'pl.variation_id', '=', 'v.id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'purchase')
            ->whereNotNull('pl.exp_date')
            ->where('pl.exp_date', '<=', $expiry_date)
            ->where('pl.exp_date', '>=', \Carbon::now()->format('Y-m-d'))
            ->whereRaw('(pl.quantity - pl.quantity_sold - pl.quantity_adjusted - pl.quantity_returned) > 0');

        if (!empty($location_id)) {
            $query->where('t.location_id', $location_id);
        }

        $expiring_query = $query->select(
                'p.id',
                'p.name',
                'p.sku',
                'v.name as variation_name',
                'pl.exp_date',
                'pl.lot_number',
                DB::raw('(pl.quantity - pl.quantity_sold - pl.quantity_adjusted - pl.quantity_returned) as qty_remaining'),
                DB::raw('DATEDIFF(pl.exp_date, CURDATE()) as days_until_expiry')
            )
            ->orderBy('pl.exp_date', 'asc');

        if (request()->ajax() && request()->has('draw')) {
            return Datatables::of($expiring_query)
                ->editColumn('qty_remaining', function ($row) {
                    return (float) $row->qty_remaining;
                })
                ->make(true);
        }

        $expiring = $expiring_query->limit(20)->get();

        if (request()->get('export_type') == 'excel') {
            $data = [];
            foreach ($expiring as $row) {
                $data[] = [
                    $row->name,
                    $row->sku,
                    $row->variation_name,
                    $row->lot_number,
                    $row->exp_date,
                    (float) $row->qty_remaining,
                    $row->days_until_expiry . ' days'
                ];
            }
            return Excel::download(new DataExport($data, ['Product', 'SKU', 'Variation', 'Lot Number', 'Expiry Date', 'Qty Remaining', 'Days Left']), 'expiring_soon.xlsx');
        }

        return response()->json(['data' => $expiring]);
    }

    /**
     * Get automatic reorder suggestions based on sales velocity
     */
    public function getReorderSuggestions()
    {
        if (!request()->ajax()) {
            abort(404);
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = request()->location_id;
        $lead_time_days = request()->lead_time ?? 7;

        // Get average daily sales for last 30 days
        $start_date = \Carbon::now()->subDays(30)->format('Y-m-d');

        $sales_subquery = DB::table('transaction_sell_lines as tsl')
            ->join('transactions as t_tmp', 'tsl.transaction_id', '=', 't_tmp.id')
            ->where('t_tmp.business_id', $business_id)
            ->where('t_tmp.type', 'sell')
            ->where('t_tmp.status', 'final')
            ->where('t_tmp.transaction_date', '>=', $start_date)
            ->select(
                'tsl.product_id',
                't_tmp.location_id',
                DB::raw('SUM(tsl.quantity) / 30 as avg_daily_sales')
            )
            ->groupBy('tsl.product_id', 't_tmp.location_id');

        $suggestions_query = DB::table('variation_location_details as vld')
            ->join('variations as v', 'vld.variation_id', '=', 'v.id')
            ->join('products as p', 'v.product_id', '=', 'p.id')
            ->join('business_locations as bl', 'vld.location_id', '=', 'bl.id')
            ->leftJoinSub($sales_subquery, 'sales', function($join) {
                $join->on('p.id', '=', 'sales.product_id')
                    ->on('vld.location_id', '=', 'sales.location_id');
            })
            ->where('p.business_id', $business_id)
            ->whereRaw("vld.qty_available < COALESCE(sales.avg_daily_sales, 0) * ?", [$lead_time_days])
            ->select(
                'p.id',
                'p.name',
                'p.sku',
                'p.alert_quantity',
                'vld.qty_available as current_stock',
                DB::raw('COALESCE(sales.avg_daily_sales, 0) as avg_daily_sales'),
                DB::raw("ROUND(COALESCE(sales.avg_daily_sales, 0) * ?, 0) as suggested_reorder_qty"),
                'bl.name as location_name'
            );
        
        // Add bindings for whereRaw and select
        // Add bindings for whereRaw and select

        $suggestions_query->addBinding([$lead_time_days], 'select');

        if (!empty($location_id)) {
            $suggestions_query->where('vld.location_id', $location_id);
        }

        if (request()->ajax() && request()->has('draw')) {
            return Datatables::of($suggestions_query)
                ->editColumn('current_stock', function ($row) {
                    return (float) $row->current_stock;
                })
                ->editColumn('avg_daily_sales', function ($row) {
                    return (float) $row->avg_daily_sales;
                })
                ->make(true);
        }

        $suggestions = $suggestions_query->orderBy(DB::raw("(COALESCE(sales.avg_daily_sales, 0) * $lead_time_days - vld.qty_available)"), 'desc')
            ->limit(20)
            ->get();

        return response()->json(['data' => $suggestions]);
    }

    /**
     * Get dashboard counters (customers, suppliers count)
     */
    public function getDashboardCounters()
    {
        $business_id = request()->session()->get('user.business_id');

        $total_customers = \App\Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->count();

        $total_suppliers = \App\Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->count();

        return response()->json([
            'total_customers' => $total_customers,
            'total_suppliers' => $total_suppliers,
        ]);
    }

    /**
     * Get overall reports data for pie chart
     */
    public function getOverallReports(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $year = $request->get('year', date('Y'));
        $location_id = $request->get('location_id');

        $start_date = $year . '-01-01';
        $end_date = $year . '-12-31';

        // Total purchases
        $purchase_query = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->whereBetween('transaction_date', [$start_date, $end_date]);
        if ($location_id) {
            $purchase_query->where('location_id', $location_id);
        }
        $total_purchase = $purchase_query->sum('final_total');

        // Total sales
        $sales_query = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('transaction_date', [$start_date, $end_date]);
        if ($location_id) {
            $sales_query->where('location_id', $location_id);
        }
        $total_sales = $sales_query->sum('final_total');

        // Total expenses
        $expense_query = Transaction::where('business_id', $business_id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$start_date, $end_date]);
        if ($location_id) {
            $expense_query->where('location_id', $location_id);
        }
        $total_expense = $expense_query->sum('final_total');

        // Income (sales - purchases - expenses) - simplified
        $total_income = $total_sales - $total_purchase - $total_expense;
        $total_income = max(0, $total_income); // Don't show negative

        return response()->json([
            'purchase' => round($total_purchase, 2),
            'sales' => round($total_sales, 2),
            'income' => round($total_income, 2),
            'expense' => round($total_expense, 2),
            'year' => $year,
        ]);
    }
}
