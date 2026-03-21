<?php

namespace App\Http\Controllers;

use App\Brands;
use App\BusinessLocation;
use App\CashRegister;
use App\Category;
use App\Charts\CommonChart;
use App\Contact;
use App\CustomerGroup;
use App\ExpenseCategory;
use App\Followup;
use App\PosOrder;
use App\Product;
use App\PurchaseLine;
use App\Restaurant\ResTable;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\TransactionSellLinesPurchaseLines;
use App\Unit;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\VariationLocationDetails;
use Datatables;
use DB;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ReportController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;

    protected $productUtil;

    protected $moduleUtil;

    protected $businessUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
    }

    public function getStockBySellingPrice(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $location_id = $request->get('location_id');

        $day_before_start_date = \Carbon::createFromFormat('Y-m-d', $start_date)->subDay()->format('Y-m-d');

        $permitted_locations = auth()->user()->permitted_locations();

        $opening_stock_by_sp = $this->transactionUtil->getOpeningClosingStock($business_id, $day_before_start_date, $location_id, true, true, $permitted_locations);

        $closing_stock_by_sp = $this->transactionUtil->getOpeningClosingStock($business_id, $end_date, $location_id, false, true, $permitted_locations);

        return [
            'opening_stock_by_sp' => $opening_stock_by_sp,
            'closing_stock_by_sp' => $closing_stock_by_sp,
        ];
    }

    /**
     * Shows profit\loss of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfitLoss(Request $request)
    {
        if (! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');

            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

            $location_id = ! empty(request()->input('location_id')) ? request()->input('location_id') : null;
            $start_date = ! empty(request()->input('start_date')) ? request()->input('start_date') : $fy['start'];
            $end_date = ! empty(request()->input('end_date')) ? request()->input('end_date') : $fy['end'];
    
            $user_id = request()->input('user_id') ?? null;

            $permitted_locations = auth()->user()->permitted_locations();
            $data = $this->transactionUtil->getProfitLossDetails($business_id, $location_id, $start_date, $end_date, $user_id, $permitted_locations);
    
            // $data['closing_stock'] = $data['closing_stock'] - $data['total_sell_return'];

            return view('report.partials.profit_loss_details', compact('data'))->render();
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.profit_loss', compact('business_locations'));
    }

    /**
     * Shows product report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseSell(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $location_id = $request->get('location_id');

            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start_date, $end_date, $location_id);

            $sell_details = $this->transactionUtil->getSellTotals(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );

            $transaction_types = [
                'purchase_return', 'sell_return',
            ];

            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start_date,
                $end_date,
                $location_id
            );

            $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'];
            $total_sell_return_inc_tax = $transaction_totals['total_sell_return_inc_tax'];

            $difference = [
                'total' => $sell_details['total_sell_inc_tax'] - $total_sell_return_inc_tax - ($purchase_details['total_purchase_inc_tax'] - $total_purchase_return_inc_tax),
                'due' => $sell_details['invoice_due'] - $purchase_details['purchase_due'],
            ];

            return ['purchase' => $purchase_details,
                'sell' => $sell_details,
                'total_purchase_return' => $total_purchase_return_inc_tax,
                'total_sell_return' => $total_sell_return_inc_tax,
                'difference' => $difference,
            ];
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.purchase_sell')
                    ->with(compact('business_locations'));
    }

    /**
     * Shows report for Supplier
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerSuppliers(Request $request)
    {
        if (! auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $contacts = Contact::where('contacts.business_id', $business_id)
                ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
                ->active()
                ->groupBy('contacts.id')
                ->select(
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                    DB::raw("SUM(IF(t.type = 'ledger_discount' AND sub_type='sell_discount', final_total, 0)) as total_ledger_discount_sell"),
                    DB::raw("SUM(IF(t.type = 'ledger_discount' AND sub_type='purchase_discount', final_total, 0)) as total_ledger_discount_purchase"),
                    'contacts.supplier_business_name',
                    'contacts.name',
                    'contacts.id',
                    'contacts.type as contact_type'
                );
            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $contacts->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($request->input('customer_group_id'))) {
                $contacts->where('contacts.customer_group_id', $request->input('customer_group_id'));
            }

            if (! empty($request->input('location_id'))) {
                $contacts->where('t.location_id', $request->input('location_id'));
            }

            if (! empty($request->input('contact_id'))) {
                $contacts->where('t.contact_id', $request->input('contact_id'));
            }

            if (! empty($request->input('contact_type'))) {
                $contacts->whereIn('contacts.type', [$request->input('contact_type'), 'both']);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $contacts->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            return Datatables::of($contacts)
                ->editColumn('name', function ($row) {
                    $name = $row->name;
                    if (! empty($row->supplier_business_name)) {
                        $name .= ', '.$row->supplier_business_name;
                    }

                    return '<a href="'.action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]).'" target="_blank" class="no-print">'.
                            $name.
                        '</a>';
                })
                ->editColumn(
                    'total_purchase',
                    '<span class="total_purchase" data-orig-value="{{$total_purchase}}">@format_currency($total_purchase)</span>'
                )
                ->editColumn(
                    'total_purchase_return',
                    '<span class="total_purchase_return" data-orig-value="{{$total_purchase_return}}">@format_currency($total_purchase_return)</span>'
                )
                ->editColumn(
                    'total_sell_return',
                    '<span class="total_sell_return" data-orig-value="{{$total_sell_return}}">@format_currency($total_sell_return)</span>'
                )
                ->editColumn(
                    'total_invoice',
                    '<span class="total_invoice" data-orig-value="{{$total_invoice}}">@format_currency($total_invoice)</span>'
                )

                ->addColumn('due', function ($row) {
                    $total_ledger_discount_purchase = $row->total_ledger_discount_purchase ?? 0;
                    $total_ledger_discount_sell = $total_ledger_discount_sell ?? 0;
                    $due = ($row->total_invoice - $row->invoice_received - $total_ledger_discount_sell) - ($row->total_purchase - $row->purchase_paid - $total_ledger_discount_purchase) - ($row->total_sell_return - $row->sell_return_paid) + ($row->total_purchase_return - $row->purchase_return_received);

                    if ($row->contact_type == 'supplier') {
                        $due -= $row->opening_balance - $row->opening_balance_paid;
                    } else {
                        $due += $row->opening_balance - $row->opening_balance_paid;
                    }

                    $due_formatted = $this->transactionUtil->num_f($due, true);

                    return '<span class="total_due" data-orig-value="'.$due.'">'.$due_formatted.'</span>';
                })
                ->addColumn(
                    'opening_balance_due',
                    '<span class="opening_balance_due" data-orig-value="{{$opening_balance - $opening_balance_paid}}">@format_currency($opening_balance - $opening_balance_paid)</span>'
                )
                ->removeColumn('supplier_business_name')
                ->removeColumn('invoice_received')
                ->removeColumn('purchase_paid')
                ->removeColumn('id')
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                        ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['total_purchase', 'total_invoice', 'due', 'name', 'total_purchase_return', 'total_sell_return', 'opening_balance_due'])
                ->make(true);
        }

        $customer_group = CustomerGroup::forDropdown($business_id, false, true);
        $types = [
            '' => __('lang_v1.all'),
            'customer' => __('report.customer'),
            'supplier' => __('report.supplier'),
        ];

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $contact_dropdown = Contact::contactDropdown($business_id, false, false);
        $show_vat = true;

        return view('report.contact')
        ->with(compact('customer_group', 'types', 'business_locations', 'contact_dropdown', 'show_vat'));
    }

    /**
     * Shows Customer Report (separated from combined report)
     * Includes VAT calculations at 16% Kenya standard rate
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerReport(Request $request)
    {
        if (! auth()->user()->can('customer_report.view') && ! auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Return the details in ajax call
        if ($request->ajax()) {
            $contacts = Contact::where('contacts.business_id', $business_id)
                ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
                ->leftJoin('customer_groups AS cg', 'contacts.customer_group_id', '=', 'cg.id')
                ->whereIn('contacts.type', ['customer', 'both'])
                ->active()
                ->groupBy('contacts.id')
                ->select(
                    'contacts.contact_id',
                    'contacts.name',
                    'contacts.mobile',
                    'cg.name as customer_group',
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                    // VAT calculation at 16% Kenya rate
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', COALESCE(t.tax_amount, 0), 0)) as vat_amount"),
                    'contacts.id'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $contacts->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($request->input('customer_group_id'))) {
                $contacts->where('contacts.customer_group_id', $request->input('customer_group_id'));
            }

            if (! empty($request->input('location_id'))) {
                $contacts->where('t.location_id', $request->input('location_id'));
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $contacts->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            return Datatables::of($contacts)
                ->editColumn('name', function ($row) {
                    return '<a href="'.action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]).'" target="_blank">'.
                            $row->name.
                        '</a>';
                })
                ->editColumn('total_invoice', function ($row) {
                    return '<span class="total_invoice" data-orig-value="'.$row->total_invoice.'">'.
                        $this->transactionUtil->num_f($row->total_invoice, true).'</span>';
                })
                ->addColumn('total_invoice_raw', function ($row) {
                    return $row->total_invoice;
                })
                ->editColumn('total_sell_return', function ($row) {
                    return '<span class="total_sell_return" data-orig-value="'.$row->total_sell_return.'">'.
                        $this->transactionUtil->num_f($row->total_sell_return, true).'</span>';
                })
                ->addColumn('total_sell_return_raw', function ($row) {
                    return $row->total_sell_return;
                })
                ->addColumn('total_due', function ($row) {
                    $due = ($row->total_invoice - $row->invoice_received) - ($row->total_sell_return - $row->sell_return_paid) + ($row->opening_balance - $row->opening_balance_paid);
                    return '<span class="total_due" data-orig-value="'.$due.'">'.
                        $this->transactionUtil->num_f($due, true).'</span>';
                })
                ->addColumn('total_due_raw', function ($row) {
                    return ($row->total_invoice - $row->invoice_received) - ($row->total_sell_return - $row->sell_return_paid) + ($row->opening_balance - $row->opening_balance_paid);
                })
                ->addColumn('total_paid', function ($row) {
                    return '<span class="total_paid" data-orig-value="'.$row->invoice_received.'">'.
                        $this->transactionUtil->num_f($row->invoice_received, true).'</span>';
                })
                ->addColumn('total_paid_raw', function ($row) {
                    return $row->invoice_received;
                })
                ->editColumn('vat_amount', function ($row) {
                    return '<span class="vat_amount" data-orig-value="'.$row->vat_amount.'">'.
                        $this->transactionUtil->num_f($row->vat_amount, true).'</span>';
                })
                ->addColumn('vat_amount_raw', function ($row) {
                    return $row->vat_amount;
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('contacts.name', 'like', "%{$keyword}%")
                        ->orWhere('contacts.mobile', 'like', "%{$keyword}%");
                })
                ->rawColumns(['name', 'total_invoice', 'total_sell_return', 'total_due', 'total_paid', 'vat_amount'])
                ->make(true);
        }

        $customer_groups = CustomerGroup::forDropdown($business_id, false, true);
        $locations = BusinessLocation::forDropdown($business_id, true);
        $show_vat = true; // Show VAT column for Kenya

        return view('report.customer_report', compact('customer_groups', 'locations', 'show_vat'));
    }

    /**
     * Shows Supplier Report (separated from combined report)
     * Includes VAT calculations at 16% Kenya standard rate
     *
     * @return \Illuminate\Http\Response
     */
    public function getSupplierReport(Request $request)
    {
        if (! auth()->user()->can('supplier_report.view') && ! auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Return the details in ajax call
        if ($request->ajax()) {
            $contacts = Contact::where('contacts.business_id', $business_id)
                ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
                ->whereIn('contacts.type', ['supplier', 'both'])
                ->active()
                ->groupBy('contacts.id')
                ->select(
                    'contacts.contact_id',
                    'contacts.supplier_business_name',
                    'contacts.name',
                    'contacts.mobile',
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_received"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                    // VAT calculation at 16% Kenya rate
                    DB::raw("SUM(IF(t.type = 'purchase', COALESCE(t.tax_amount, 0), 0)) as vat_amount"),
                    'contacts.id'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $contacts->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($request->input('location_id'))) {
                $contacts->where('t.location_id', $request->input('location_id'));
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $contacts->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            return Datatables::of($contacts)
                ->editColumn('name', function ($row) {
                    $name = $row->name;
                    if (!empty($row->supplier_business_name)) {
                        $name .= ' ('.$row->supplier_business_name.')';
                    }
                    return '<a href="'.action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]).'" target="_blank">'.$name.'</a>';
                })
                ->editColumn('total_purchase', function ($row) {
                    return '<span class="total_purchase" data-orig-value="'.$row->total_purchase.'">'.
                        $this->transactionUtil->num_f($row->total_purchase, true).'</span>';
                })
                ->addColumn('total_purchase_raw', function ($row) {
                    return $row->total_purchase;
                })
                ->editColumn('total_purchase_return', function ($row) {
                    return '<span class="total_purchase_return" data-orig-value="'.$row->total_purchase_return.'">'.
                        $this->transactionUtil->num_f($row->total_purchase_return, true).'</span>';
                })
                ->addColumn('total_purchase_return_raw', function ($row) {
                    return $row->total_purchase_return;
                })
                ->addColumn('total_due', function ($row) {
                    $due = ($row->total_purchase - $row->purchase_paid) - ($row->total_purchase_return - $row->purchase_return_received) + ($row->opening_balance - $row->opening_balance_paid);
                    return '<span class="total_due" data-orig-value="'.$due.'">'.
                        $this->transactionUtil->num_f($due, true).'</span>';
                })
                ->addColumn('total_due_raw', function ($row) {
                    return ($row->total_purchase - $row->purchase_paid) - ($row->total_purchase_return - $row->purchase_return_received) + ($row->opening_balance - $row->opening_balance_paid);
                })
                ->addColumn('total_paid', function ($row) {
                    return '<span class="total_paid" data-orig-value="'.$row->purchase_paid.'">'.
                        $this->transactionUtil->num_f($row->purchase_paid, true).'</span>';
                })
                ->addColumn('total_paid_raw', function ($row) {
                    return $row->purchase_paid;
                })
                ->editColumn('vat_amount', function ($row) {
                    return '<span class="vat_amount" data-orig-value="'.$row->vat_amount.'">'.
                        $this->transactionUtil->num_f($row->vat_amount, true).'</span>';
                })
                ->addColumn('vat_amount_raw', function ($row) {
                    return $row->vat_amount;
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('contacts.name', 'like', "%{$keyword}%")
                        ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%")
                        ->orWhere('contacts.mobile', 'like', "%{$keyword}%");
                })
                ->rawColumns(['name', 'total_purchase', 'total_purchase_return', 'total_due', 'total_paid', 'vat_amount'])
                ->make(true);
        }

        $locations = BusinessLocation::forDropdown($business_id, true);
        $show_vat = true; // Show VAT column for Kenya

        return view('report.supplier_report', compact('locations', 'show_vat'));
    }

    /**
     * Shows Follow-up Report
     *
     * @return \Illuminate\Http\Response
     */
    public function getFollowupReport(Request $request)
    {
        if (!auth()->user()->can('followups.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $followups = Followup::forBusiness($business_id)
                ->with(['location', 'product', 'variation', 'createdBy'])
                ->select('followups.*');

            if (!empty($request->location_id)) {
                $followups->where('location_id', $request->location_id);
            }

            if (!empty($request->status)) {
                $followups->where('status', $request->status);
            }

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end = $request->end_date;
                $followups->whereDate('followups.created_at', '>=', $start)
                            ->whereDate('followups.created_at', '<=', $end);
            }

            return Datatables::of($followups)
                ->editColumn('created_at', function ($row) {
                    return $this->transactionUtil->format_date($row->created_at, true);
                })
                ->editColumn('status', function ($row) {
                    $statusClasses = [
                        'pending' => 'bg-yellow',
                        'contacted' => 'bg-blue',
                        'resolved' => 'bg-green',
                    ];
                    $class = $statusClasses[$row->status] ?? 'bg-gray';
                    return '<span class="label ' . $class . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    if (auth()->user()->can('followups.update')) {
                        $html .= '<button type="button" class="btn btn-primary btn-xs edit-followup-status" data-id="' . $row->id . '" data-status="' . $row->status . '"><i class="fa fa-edit"></i></button>';
                    }
                    if (auth()->user()->can('followups.delete')) {
                        $html .= '<button type="button" class="btn btn-danger btn-xs delete-followup" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                    }
                    $html .= '</div>';
                    // Recipient: Location mobile or Business Owner's contact number (Internal)
                    $business = \App\Business::find($row->business_id);
                    $location = \App\BusinessLocation::find($row->location_id);
                    $recipient_number = !empty($location->mobile) ? $location->mobile : ($business->owner ? $business->owner->contact_number : null);
                    
                    if (!empty($recipient_number)) {
                        $html .= ' <a target="_blank" href="https://wa.me/' . $recipient_number . '?text=' . urlencode(__('lang_v1.followup_reminder_message', ['product' => $row->product ? $row->product->name : '-', 'customer' => $row->customer_name])) . '" class="btn btn-success btn-xs"><i class="fab fa-whatsapp"></i></a>';
                    }
                    return $html;
                })
                ->addColumn('product_name', function ($row) {
                    $name = $row->product ? $row->product->name : '-';
                    if ($row->variation && $row->variation->name !== 'DUMMY') {
                        $name .= ' (' . $row->variation->name . ')';
                    }
                    return $name;
                })
                ->addColumn('location_name', function ($row) {
                    return $row->location ? $row->location->name : '-';
                })
                ->addColumn('created_by_name', function ($row) {
                    return $row->createdBy ? $row->createdBy->first_name : '-';
                })
                ->addColumn('comment', function ($row) {
                    return $row->comment;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $locations = BusinessLocation::forDropdown($business_id);
        $statuses = Followup::statusOptions();

        return view('report.followups', compact('locations', 'statuses'));
    }

    /**
     * Shows POS Orders Report
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrdersReport(Request $request)
    {
        if (!auth()->user()->can('orders.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $orders = PosOrder::forBusiness($business_id)
                ->leftJoin('business_locations', 'pos_orders.location_id', '=', 'business_locations.id')
                ->leftJoin('contacts', 'pos_orders.contact_id', '=', 'contacts.id')
                ->leftJoin('users', 'pos_orders.created_by', '=', 'users.id')
                ->with(['orderLines.product', 'orderLines.variation'])
                ->select([
                    'pos_orders.*',
                    'business_locations.name as location_name',
                    'contacts.name as customer_name',
                    'users.first_name as user_first_name',
                    'users.last_name as user_last_name'
                ]);

            if (!empty($request->location_id)) {
                $orders->where('pos_orders.location_id', $request->location_id);
            }

            if (!empty($request->status)) {
                $orders->where('pos_orders.status', $request->status);
            }

            if (!empty($request->contact_id)) {
                $orders->where('pos_orders.contact_id', $request->contact_id);
            }

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end = $request->end_date;
                $orders->whereDate('pos_orders.created_at', '>=', $start)
                            ->whereDate('pos_orders.created_at', '<=', $end);
            }

            return Datatables::of($orders)
                ->editColumn('created_at', function ($row) {
                    return $this->transactionUtil->format_date($row->created_at, true);
                })
                ->editColumn('status', function ($row) {
                    $statusClasses = [
                        'pending' => 'bg-yellow',
                        'processing' => 'bg-blue',
                        'completed' => 'bg-green',
                        'cancelled' => 'bg-red',
                    ];
                    $status = $row->status ?? 'pending';
                    $class = $statusClasses[$status] ?? 'bg-gray';
                    return '<span class="label ' . $class . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('items_count', function ($row) {
                    return $row->orderLines->count();
                })
                ->addColumn('products', function ($row) {
                    $products = [];
                    foreach ($row->orderLines as $line) {
                        $p = $line->product ? $line->product->name : ($line->custom_product_name ?? '');
                        if ($line->variation) {
                            $p .= ' (' . $line->variation->name . ')';
                        }
                        $products[] = $p . ' x ' . number_format($line->quantity, 0);
                    }
                    return implode(', ', $products);
                })
                ->addColumn('location_name', function ($row) {
                    return $row->location_name ?? '-';
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer_name ?? 'Walk-In Customer';
                })
                ->addColumn('created_by_name', function ($row) {
                    return trim(($row->user_first_name ?? '') . ' ' . ($row->user_last_name ?? ''));
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs btn-modal" data-href="' . action([\App\Http\Controllers\OrderController::class, 'show'], [$row->id]) . '" data-container=".view_modal">
                            <i class="fa fa-eye"></i> ' . __('messages.view') . '
                        </button>
                        <a href="' . action([\App\Http\Controllers\OrderController::class, 'edit'], [$row->id]) . '" class="btn btn-warning btn-xs">
                            <i class="fa fa-edit"></i> ' . __('messages.edit') . '
                        </a>
                    </div>';
                    return $html;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $locations = BusinessLocation::forDropdown($business_id);
        $statuses = [
            'pending' => __('lang_v1.pending'),
            'processing' => __('lang_v1.processing'),
            'completed' => __('lang_v1.completed'),
            'cancelled' => __('lang_v1.cancelled'),
        ];

        return view('report.orders', compact('locations', 'statuses'));
    }

    /**
     * Shows product stock report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockReport(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
                                                ->get();
        $allowed_selling_price_group = false;
        foreach ($selling_price_groups as $selling_price_group) {
            if (auth()->user()->can('selling_price_group.'.$selling_price_group->id)) {
                $allowed_selling_price_group = true;
                break;
            }
        }
        if ($this->moduleUtil->isModuleInstalled('Manufacturing') && (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module'))) {
            $show_manufacturing_data = 1;
        } else {
            $show_manufacturing_data = 0;
        }
        if ($request->ajax()) {
            $filters = request()->only(['location_id', 'category_id', 'sub_category_id', 'brand_id', 'unit_id', 'tax_id', 'type',
                'only_mfg_products', 'active_state',  'not_for_selling', 'repair_model_id', 'product_id', 'active_state', ]);

            $filters['not_for_selling'] = isset($filters['not_for_selling']) && $filters['not_for_selling'] == 'true' ? 1 : 0;

            $filters['show_manufacturing_data'] = $show_manufacturing_data;

            //Return the details in ajax call
            $for = request()->input('for') == 'view_product' ? 'view_product' : 'datatables';

            $products = $this->productUtil->getProductStockDetails($business_id, $filters, $for);
            //To show stock details on view product modal
            if ($for == 'view_product' && ! empty(request()->input('product_id'))) {
                $product_stock_details = $products;

                return view('product.partials.product_stock_details')->with(compact('product_stock_details'));
            }

            $datatable = Datatables::of($products)
                ->editColumn('stock', function ($row) {
                    if ($row->enable_stock) {
                        $stock = $row->stock ? $row->stock : 0;

                        return  '<span class="current_stock" data-is_quantity="true" data-orig-value="'.(float) $stock.'" data-unit="'.$row->unit.'"> '.$this->transactionUtil->num_f($stock, false, null, true).'</span>'.' '.$row->unit;
                    } else {
                        return '--';
                    }
                })
                ->editColumn('product', function ($row) {
                    $name = $row->product;

                    return $name;
                })
                ->addColumn('action', function ($row) {
                    return '<a class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max " href="'.action([\App\Http\Controllers\ProductController::class, 'productStockHistory'], [$row->product_id]).
                    '?location_id='.$row->location_id.'&variation_id='.$row->variation_id.
                    '"><i class="fas fa-history"></i> '.__('lang_v1.product_stock_history').'</a>';
                })
                ->addColumn('variation', function ($row) {
                    $variation = '';
                    if ($row->type == 'variable') {
                        $variation .= $row->product_variation.'-'.$row->variation_name;
                    }

                    return $variation;
                })
                ->editColumn('total_sold', function ($row) {
                    $total_sold = 0;
                    if ($row->total_sold) {
                        $total_sold = (float) $row->total_sold;
                    }

                    return '<span data-is_quantity="true" class="total_sold" data-orig-value="'.$total_sold.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_sold, false, null, true).'</span> '.$row->unit;
                })
                ->editColumn('total_transfered', function ($row) {
                    $total_transfered = 0;
                    if ($row->total_transfered) {
                        $total_transfered = (float) $row->total_transfered;
                    }

                    return '<span class="total_transfered" data-is_quantity="true" data-orig-value="'.$total_transfered.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_transfered, false, null, true).'</span> '.$row->unit;
                })

                ->editColumn('total_adjusted', function ($row) {
                    $total_adjusted = 0;
                    if ($row->total_adjusted) {
                        $total_adjusted = (float) $row->total_adjusted;
                    }

                    return '<span data-is_quantity="true" class="total_adjusted" data-orig-value="'.$total_adjusted.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_adjusted, false, null, true).'</span> '.$row->unit;
                })
                ->editColumn('unit_price', function ($row) use ($allowed_selling_price_group) {
                    $html = '';
                    if (auth()->user()->can('access_default_selling_price')) {
                        $html .= $this->transactionUtil->num_f($row->unit_price, true);
                    }

                    if ($allowed_selling_price_group) {
                        $html .= ' <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary tw-w-max btn-modal no-print" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\ProductController::class, 'viewGroupPrice'], [$row->product_id]).'">'.__('lang_v1.view_group_prices').'</button>';
                    }

                    return $html;
                })
                ->editColumn('stock_price', function ($row) {
                    $html = '<span class="total_stock_price" data-orig-value="'
                        .$row->stock_price.'">'.
                        $this->transactionUtil->num_f($row->stock_price, true).'</span>';

                    return $html;
                })
                ->editColumn('stock_value_by_sale_price', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;
                    $unit_selling_price = (float) $row->group_price > 0 ? $row->group_price : $row->unit_price;
                    $stock_price = $stock * $unit_selling_price;

                    return  '<span class="stock_value_by_sale_price" data-orig-value="'.(float) $stock_price.'" > '.$this->transactionUtil->num_f($stock_price, true).'</span>';
                })
                ->addColumn('potential_profit', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;
                    $unit_selling_price = (float) $row->group_price > 0 ? $row->group_price : $row->unit_price;
                    $stock_price_by_sp = $stock * $unit_selling_price;
                    $potential_profit = (float) $stock_price_by_sp - (float) $row->stock_price;

                    return  '<span class="potential_profit" data-orig-value="'.(float) $potential_profit.'" > '.$this->transactionUtil->num_f($potential_profit, true).'</span>';
                })
                ->setRowClass(function ($row) {
                    return $row->enable_stock && $row->stock <= $row->alert_quantity ? 'bg-danger' : '';
                })
                ->filterColumn('variation', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(pv.name, ''), '-', COALESCE(variations.name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('enable_stock')
                ->removeColumn('unit')
                ->removeColumn('id');

            $raw_columns = ['unit_price', 'total_transfered', 'total_sold',
                'total_adjusted', 'stock', 'stock_price', 'stock_value_by_sale_price',
                'potential_profit', 'action', ];

            if ($show_manufacturing_data) {
                $datatable->editColumn('total_mfg_stock', function ($row) {
                    $total_mfg_stock = 0;
                    if ($row->total_mfg_stock) {
                        $total_mfg_stock = (float) $row->total_mfg_stock;
                    }

                    return '<span data-is_quantity="true" class="total_mfg_stock"  data-orig-value="'.$total_mfg_stock.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_mfg_stock, false, null, true).'</span> '.$row->unit;
                });
                $raw_columns[] = 'total_mfg_stock';
            }

            return $datatable->rawColumns($raw_columns)->make(true);
        }

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.stock_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations', 'show_manufacturing_data'));
    }

    // // this function copy of above get route becouse of large size parameter 
    // public function postStockReport(Request $request){
    //     if ($request->ajax()) {
    //         $filters = request()->only(['location_id', 'category_id', 'sub_category_id', 'brand_id', 'unit_id', 'tax_id', 'type',
    //             'only_mfg_products', 'active_state',  'not_for_selling', 'repair_model_id', 'product_id', 'active_state', ]);

    //         $filters['not_for_selling'] = isset($filters['not_for_selling']) && $filters['not_for_selling'] == 'true' ? 1 : 0;

    //         $filters['show_manufacturing_data'] = $show_manufacturing_data;

    //         //Return the details in ajax call
    //         $for = request()->input('for') == 'view_product' ? 'view_product' : 'datatables';

    //         $products = $this->productUtil->getProductStockDetails($business_id, $filters, $for);
    //         //To show stock details on view product modal
    //         if ($for == 'view_product' && ! empty(request()->input('product_id'))) {
    //             $product_stock_details = $products;

    //             return view('product.partials.product_stock_details')->with(compact('product_stock_details'));
    //         }

    //         $datatable = Datatables::of($products)
    //             ->editColumn('stock', function ($row) {
    //                 if ($row->enable_stock) {
    //                     $stock = $row->stock ? $row->stock : 0;

    //                     return  '<span class="current_stock" data-orig-value="'.(float) $stock.'" data-unit="'.$row->unit.'"> '.$this->transactionUtil->num_f($stock, false, null, true).'</span>'.' '.$row->unit;
    //                 } else {
    //                     return '--';
    //                 }
    //             })
    //             ->editColumn('product', function ($row) {
    //                 $name = $row->product;

    //                 return $name;
    //             })
    //             ->addColumn('action', function ($row) {
    //                 return '<a class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info" href="'.action([\App\Http\Controllers\ProductController::class, 'productStockHistory'], [$row->product_id]).
    //                 '?location_id='.$row->location_id.'&variation_id='.$row->variation_id.
    //                 '"><i class="fas fa-history"></i> '.__('lang_v1.product_stock_history').'</a>';
    //             })
    //             ->addColumn('variation', function ($row) {
    //                 $variation = '';
    //                 if ($row->type == 'variable') {
    //                     $variation .= $row->product_variation.'-'.$row->variation_name;
    //                 }

    //                 return $variation;
    //             })
    //             ->editColumn('total_sold', function ($row) {
    //                 $total_sold = 0;
    //                 if ($row->total_sold) {
    //                     $total_sold = (float) $row->total_sold;
    //                 }

    //                 return '<span data-is_quantity="true" class="total_sold" data-orig-value="'.$total_sold.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_sold, false, null, true).'</span> '.$row->unit;
    //             })
    //             ->editColumn('total_transfered', function ($row) {
    //                 $total_transfered = 0;
    //                 if ($row->total_transfered) {
    //                     $total_transfered = (float) $row->total_transfered;
    //                 }

    //                 return '<span class="total_transfered" data-orig-value="'.$total_transfered.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_transfered, false, null, true).'</span> '.$row->unit;
    //             })

    //             ->editColumn('total_adjusted', function ($row) {
    //                 $total_adjusted = 0;
    //                 if ($row->total_adjusted) {
    //                     $total_adjusted = (float) $row->total_adjusted;
    //                 }

    //                 return '<span class="total_adjusted" data-orig-value="'.$total_adjusted.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_adjusted, false, null, true).'</span> '.$row->unit;
    //             })
    //             ->editColumn('unit_price', function ($row) use ($allowed_selling_price_group) {
    //                 $html = '';
    //                 if (auth()->user()->can('access_default_selling_price')) {
    //                     $html .= $this->transactionUtil->num_f($row->unit_price, true);
    //                 }

    //                 if ($allowed_selling_price_group) {
    //                     $html .= ' <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary btn-modal no-print" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\ProductController::class, 'viewGroupPrice'], [$row->product_id]).'">'.__('lang_v1.view_group_prices').'</button>';
    //                 }

    //                 return $html;
    //             })
    //             ->editColumn('stock_price', function ($row) {
    //                 $html = '<span class="total_stock_price" data-orig-value="'
    //                     .$row->stock_price.'">'.
    //                     $this->transactionUtil->num_f($row->stock_price, true).'</span>';

    //                 return $html;
    //             })
    //             ->editColumn('stock_value_by_sale_price', function ($row) {
    //                 $stock = $row->stock ? $row->stock : 0;
    //                 $unit_selling_price = (float) $row->group_price > 0 ? $row->group_price : $row->unit_price;
    //                 $stock_price = $stock * $unit_selling_price;

    //                 return  '<span class="stock_value_by_sale_price" data-orig-value="'.(float) $stock_price.'" > '.$this->transactionUtil->num_f($stock_price, true).'</span>';
    //             })
    //             ->addColumn('potential_profit', function ($row) {
    //                 $stock = $row->stock ? $row->stock : 0;
    //                 $unit_selling_price = (float) $row->group_price > 0 ? $row->group_price : $row->unit_price;
    //                 $stock_price_by_sp = $stock * $unit_selling_price;
    //                 $potential_profit = (float) $stock_price_by_sp - (float) $row->stock_price;

    //                 return  '<span class="potential_profit" data-orig-value="'.(float) $potential_profit.'" > '.$this->transactionUtil->num_f($potential_profit, true).'</span>';
    //             })
    //             ->setRowClass(function ($row) {
    //                 return $row->enable_stock && $row->stock <= $row->alert_quantity ? 'bg-danger' : '';
    //             })
    //             ->filterColumn('variation', function ($query, $keyword) {
    //                 $query->whereRaw("CONCAT(COALESCE(pv.name, ''), '-', COALESCE(variations.name, '')) like ?", ["%{$keyword}%"]);
    //             })
    //             ->removeColumn('enable_stock')
    //             ->removeColumn('unit')
    //             ->removeColumn('id');

    //         $raw_columns = ['unit_price', 'total_transfered', 'total_sold',
    //             'total_adjusted', 'stock', 'stock_price', 'stock_value_by_sale_price',
    //             'potential_profit', 'action', ];

    //         if ($show_manufacturing_data) {
    //             $datatable->editColumn('total_mfg_stock', function ($row) {
    //                 $total_mfg_stock = 0;
    //                 if ($row->total_mfg_stock) {
    //                     $total_mfg_stock = (float) $row->total_mfg_stock;
    //                 }

    //                 return '<span data-is_quantity="true" class="total_mfg_stock"  data-orig-value="'.$total_mfg_stock.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_mfg_stock, false, null, true).'</span> '.$row->unit;
    //             });
    //             $raw_columns[] = 'total_mfg_stock';
    //         }

    //         return $datatable->rawColumns($raw_columns)->make(true);
    //     }
    // }

    /**
     * Shows product stock details
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockDetails(Request $request)
    {
        //Return the details in ajax call
        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $product_id = $request->input('product_id');
            $query = Product::leftjoin('units as u', 'products.unit_id', '=', 'u.id')
                ->join('variations as v', 'products.id', '=', 'v.product_id')
                ->join('product_variations as pv', 'pv.id', '=', 'v.product_variation_id')
                ->leftjoin('variation_location_details as vld', 'v.id', '=', 'vld.variation_id')
                ->where('products.business_id', $business_id)
                ->where('products.id', $product_id)
                ->whereNull('v.deleted_at');

            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = '';
            if ($permitted_locations != 'all') {
                $query->whereIn('vld.location_id', $permitted_locations);
                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }

            if (! empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');

                $query->where('vld.location_id', $location_id);

                $location_filter .= "AND transactions.location_id=$location_id";
            }

            $product_details = $query->select(
                'products.name as product',
                'u.short_name as unit',
                'pv.name as product_variation',
                'v.name as variation',
                'v.sub_sku as sub_sku',
                'v.sell_price_inc_tax',
                DB::raw('SUM(vld.qty_available) as stock'),
                DB::raw("(SELECT SUM(IF(transactions.type='sell', TSL.quantity - TSL.quantity_returned, -1* TPL.quantity) ) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id

                        LEFT JOIN purchase_lines AS TPL ON transactions.id=TPL.transaction_id

                        WHERE transactions.status='final' AND transactions.type='sell' $location_filter 
                        AND (TSL.variation_id=v.id OR TPL.variation_id=v.id)) as total_sold"),
                DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell_transfer' $location_filter 
                        AND (TSL.variation_id=v.id)) as total_transfered"),
                DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions 
                        LEFT JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='stock_adjustment' $location_filter 
                        AND (SAL.variation_id=v.id)) as total_adjusted")
                // DB::raw("(SELECT SUM(quantity) FROM transaction_sell_lines LEFT JOIN transactions ON transaction_sell_lines.transaction_id=transactions.id WHERE transactions.status='final' $location_filter AND
                //     transaction_sell_lines.variation_id=v.id) as total_sold")
            )
                        ->groupBy('v.id')
                        ->get();

            return view('report.stock_details')
                        ->with(compact('product_details'));
        }
    }

    /**
     * Shows tax report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getTaxDetails(Request $request)
    {
        if (! auth()->user()->can('tax_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $taxes = TaxRate::forBusiness($business_id);
            $type = $request->input('type');

            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

            $sells = Transaction::leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')
                            ->leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
                ->where('transactions.business_id', $business_id)
                ->with(['payment_lines'])
                ->select('c.name as contact_name',
                        'c.supplier_business_name',
                        'c.tax_number',
                        'transactions.ref_no',
                        'transactions.invoice_no',
                        'transactions.transaction_date',
                        'transactions.total_before_tax',
                        'transactions.tax_id',
                        'transactions.tax_amount',
                        'transactions.id',
                        'transactions.type',
                        'transactions.discount_type',
                        'transactions.discount_amount'
                    );
            if ($type == 'sell') {
                $sells->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where(function ($query) {
                        $query->whereHas('sell_lines', function ($q) {
                            $q->whereNotNull('transaction_sell_lines.tax_id');
                        })->orWhereNotNull('transactions.tax_id');
                    })
                    ->with(['sell_lines' => function ($q) {
                        $q->whereNotNull('transaction_sell_lines.tax_id');
                    }, 'sell_lines.line_tax']);
            }
            if ($type == 'purchase') {
                $sells->where('transactions.type', 'purchase')
                    ->where('transactions.status', 'received')
                    ->where(function ($query) {
                        $query->whereHas('purchase_lines', function ($q) {
                            $q->whereNotNull('purchase_lines.tax_id');
                        })->orWhereNotNull('transactions.tax_id');
                    })
                    ->with(['purchase_lines' => function ($q) {
                        $q->whereNotNull('purchase_lines.tax_id');
                    }, 'purchase_lines.line_tax']);
            }

            if ($type == 'expense') {
                $sells->where('transactions.type', 'expense')
                        ->whereNotNull('transactions.tax_id');
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (request()->has('contact_id')) {
                $contact_id = request()->get('contact_id');
                if (! empty($contact_id)) {
                    $sells->where('transactions.contact_id', $contact_id);
                }
            }

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                                ->whereDate('transactions.transaction_date', '<=', $end);
            }
            $datatable = Datatables::of($sells);
            $raw_cols = ['total_before_tax', 'discount_amount', 'contact_name', 'payment_methods'];
            $group_taxes_array = TaxRate::groupTaxes($business_id);
            $group_taxes = [];
            foreach ($group_taxes_array as $group_tax) {
                foreach ($group_tax['sub_taxes'] as $sub_tax) {
                    $group_taxes[$group_tax->id]['sub_taxes'][$sub_tax->id] = $sub_tax;
                }
            }
            foreach ($taxes as $tax) {
                $col = 'tax_'.$tax['id'];
                $raw_cols[] = $col;
                $datatable->addColumn($col, function ($row) use ($tax, $type, $col, $group_taxes) {
                    $tax_amount = 0;
                    if ($type == 'sell') {
                        foreach ($row->sell_lines as $sell_line) {
                            if ($sell_line->tax_id == $tax['id']) {

                                // $tax_amount += ($sell_line->item_tax * ($sell_line->quantity - 
                                // $sell_line->quantity_returned));

                                $tax_for_one = $sell_line->quantity > 0 ? $sell_line->line_total_tax / $sell_line->quantity : 0;
                                $tax_amount += ($tax_for_one * ($sell_line->quantity - $sell_line->quantity_returned));
                                
                            }

                            //break group tax
                            if ($sell_line->line_tax->is_tax_group == 1 && array_key_exists($tax['id'], $group_taxes[$sell_line->tax_id]['sub_taxes'])) {
                                $group_tax_details = $this->transactionUtil->groupTaxDetails($sell_line->line_tax, $sell_line->item_tax);

                                $sub_tax_share = 0;
                                foreach ($group_tax_details as $sub_tax_details) {
                                    if ($sub_tax_details['id'] == $tax['id']) {
                                        $sub_tax_share = $sub_tax_details['calculated_tax'];
                                    }
                                }

                                $tax_amount += ($sub_tax_share * ($sell_line->quantity - $sell_line->quantity_returned));
                            }
                        }
                    } elseif ($type == 'purchase') {
                        foreach ($row->purchase_lines as $purchase_line) {
                            if ($purchase_line->tax_id == $tax['id']) {
                                $tax_amount += ($purchase_line->item_tax * ($purchase_line->quantity - $purchase_line->quantity_returned));
                            }

                            //break group tax
                            if ($purchase_line->line_tax->is_tax_group == 1 && array_key_exists($tax['id'], $group_taxes[$purchase_line->tax_id]['sub_taxes'])) {
                                $group_tax_details = $this->transactionUtil->groupTaxDetails($purchase_line->line_tax, $purchase_line->item_tax);

                                $sub_tax_share = 0;
                                foreach ($group_tax_details as $sub_tax_details) {
                                    if ($sub_tax_details['id'] == $tax['id']) {
                                        $sub_tax_share = $sub_tax_details['calculated_tax'];
                                    }
                                }

                                $tax_amount += ($sub_tax_share * ($purchase_line->quantity - $purchase_line->quantity_returned));
                            }
                        }
                    }
                    if ($row->tax_id == $tax['id']) {
                        $tax_amount += $row->tax_amount;
                    }

                    //break group tax
                    if (! empty($group_taxes[$row->tax_id]) && array_key_exists($tax['id'], $group_taxes[$row->tax_id]['sub_taxes'])) {
                        $group_tax_details = $this->transactionUtil->groupTaxDetails($row->tax_id, $row->tax_amount);

                        $sub_tax_share = 0;
                        foreach ($group_tax_details as $sub_tax_details) {
                            if ($sub_tax_details['id'] == $tax['id']) {
                                $sub_tax_share = $sub_tax_details['calculated_tax'];
                            }
                        }

                        $tax_amount += $sub_tax_share;
                    }

                    if ($tax_amount > 0) {
                        return '<span class="display_currency '.$col.'" data-currency_symbol="true" data-orig-value="'.$tax_amount.'">'.$tax_amount.'</span>';
                    } else {
                        return '';
                    }
                });
            }

            $datatable->editColumn(
                    'total_before_tax',
                    function ($row) {
                        return '<span class="total_before_tax" 
                        data-orig-value="'.$row->total_before_tax.'">'.
                        $this->transactionUtil->num_f($row->total_before_tax, true).'</span>';
                    }
                )->editColumn('discount_amount', function ($row) {
                    $d = '';
                    if ($row->discount_amount !== 0) {
                        $symbol = $row->discount_type != 'percentage';
                        $d .= $this->transactionUtil->num_f($row->discount_amount, $symbol);

                        if ($row->discount_type == 'percentage') {
                            $d .= '%';
                        }
                    }

                    return $d;
                })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('contact_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$contact_name}}')
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = $payment_types[$methods[0]];
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = ! empty($payment_method) ? '<span class="payment-method" data-orig-value="'.$payment_method.'" data-status-name="'.$payment_method.'">'.$payment_method.'</span>' : '';

                    return $html;
                });

            return $datatable->rawColumns($raw_cols)
                            ->make(true);
        }
    }

    /**
     * Shows tax report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getTaxReport(Request $request)
    {
        if (! auth()->user()->can('tax_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');
            $contact_id = $request->get('contact_id');

            $input_tax_details = $this->transactionUtil->getInputTax($business_id, $start_date, $end_date, $location_id, $contact_id);

            $output_tax_details = $this->transactionUtil->getOutputTax($business_id, $start_date, $end_date, $location_id, $contact_id);

            $expense_tax_details = $this->transactionUtil->getExpenseTax($business_id, $start_date, $end_date, $location_id, $contact_id);

            $module_output_taxes = $this->moduleUtil->getModuleData('getModuleOutputTax', ['start_date' => $start_date, 'end_date' => $end_date]);

            $total_module_output_tax = 0;
            foreach ($module_output_taxes as $key => $module_output_tax) {
                $total_module_output_tax += $module_output_tax;
            }

            $total_output_tax = $output_tax_details['total_tax'] + $total_module_output_tax;

            $tax_diff = $total_output_tax - $input_tax_details['total_tax'] - $expense_tax_details['total_tax'];

            return [
                'tax_diff' => $tax_diff,
            ];
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $taxes = TaxRate::forBusiness($business_id);

        $tax_report_tabs = $this->moduleUtil->getModuleData('getTaxReportViewTabs');

        $contact_dropdown = Contact::contactDropdown($business_id, false, false);

        return view('report.tax_report')
            ->with(compact('business_locations', 'taxes', 'tax_report_tabs', 'contact_dropdown'));
    }

    /**
     * Shows trending products
     *
     * @return \Illuminate\Http\Response
     */
    public function getTrendingProducts(Request $request)
    {
        if (! auth()->user()->can('trending_product_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $filters = request()->only(['category', 'sub_category', 'brand', 'unit', 'limit', 'location_id', 'product_type']);

        $date_range = request()->input('date_range');

        if (! empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        }

        $products = $this->productUtil->getTrendingProducts($business_id, $filters);

        $values = [];
        $labels = [];
        foreach ($products as $product) {
            $values[] = (float) $product->total_unit_sold;
            $labels[] = $product->product.' - '.$product->sku.' ('.$product->unit.')';
        }

        $chart = new CommonChart;
        $chart->labels($labels)
            ->dataset(__('report.total_unit_sold'), 'column', $values);

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.trending_products')
                    ->with(compact('chart', 'categories', 'brands', 'units', 'business_locations', 'products'));
    }

    public function getTrendingProductsAjax()
    {
        $business_id = request()->session()->get('user.business_id');
    }

    /**
     * Shows expense report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getExpenseReport(Request $request)
    {
        if (! auth()->user()->can('expense_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $filters = $request->only(['category', 'location_id']);

        $date_range = $request->input('date_range');

        if (! empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        } else {
            $filters['start_date'] = \Carbon::now()->startOfMonth()->format('Y-m-d');
            $filters['end_date'] = \Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $expenses = $this->transactionUtil->getExpenseReport($business_id, $filters);

        $values = [];
        $labels = [];
        foreach ($expenses as $expense) {
            $values[] = (float) $expense->total_expense;
            $labels[] = ! empty($expense->category) ? $expense->category : __('report.others');
        }

        $chart = new CommonChart;
        $chart->labels($labels)
            ->title(__('report.expense_report'))
            ->dataset(__('report.total_expense'), 'column', $values);

        $categories = ExpenseCategory::where('business_id', $business_id)
                            ->pluck('name', 'id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.expense_report')
                    ->with(compact('chart', 'categories', 'business_locations', 'expenses'));
    }

    /**
     * Shows stock adjustment report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockAdjustmentReport(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $query = Transaction::where('business_id', $business_id)
                            ->where('type', 'stock_adjustment');

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('location_id', $permitted_locations);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            $location_id = $request->get('location_id');
            if (! empty($location_id)) {
                $query->where('location_id', $location_id);
            }

            $stock_adjustment_details = $query->select(
                DB::raw('SUM(final_total) as total_amount'),
                DB::raw('SUM(total_amount_recovered) as total_recovered'),
                DB::raw("SUM(IF(adjustment_type = 'normal', final_total, 0)) as total_normal"),
                DB::raw("SUM(IF(adjustment_type = 'abnormal', final_total, 0)) as total_abnormal")
            )->first();

            return $stock_adjustment_details;
        }
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.stock_adjustment_report')
                    ->with(compact('business_locations'));
    }

    /**
     * Shows register report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegisterReport(Request $request)
    {
        if (! auth()->user()->can('register_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {

        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');
        $user_id = request()->input('user_id');

        $permitted_locations = auth()->user()->permitted_locations();

            $registers = $this->transactionUtil->registerReport($business_id, $permitted_locations, $start_date, $end_date, $user_id);

            return Datatables::of($registers)
                ->editColumn('total_card_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_card_payment.'" >'.$this->transactionUtil->num_f($row->total_card_payment, true).' ('.$row->total_card_slips.')</span>';
                })
                ->editColumn('total_cheque_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_cheque_payment.'" >'.$this->transactionUtil->num_f($row->total_cheque_payment, true).' ('.$row->total_cheques.')</span>';
                })
                ->editColumn('total_cash_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_cash_payment.'" >'.$this->transactionUtil->num_f($row->total_cash_payment, true).'</span>';
                })
                ->editColumn('total_bank_transfer_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_bank_transfer_payment.'" >'.$this->transactionUtil->num_f($row->total_bank_transfer_payment, true).'</span>';
                })
                ->editColumn('total_other_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_other_payment.'" >'.$this->transactionUtil->num_f($row->total_other_payment, true).'</span>';
                })
                ->editColumn('total_advance_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_advance_payment.'" >'.$this->transactionUtil->num_f($row->total_advance_payment, true).'</span>';
                })
                ->editColumn('total_custom_pay_1', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_1.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_1, true).'</span>';
                })
                ->editColumn('total_custom_pay_2', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_2.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_2, true).'</span>';
                })
                ->editColumn('total_custom_pay_3', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_3.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_3, true).'</span>';
                })
                ->editColumn('total_custom_pay_4', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_4.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_4, true).'</span>';
                })
                ->editColumn('total_custom_pay_5', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_5.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_5, true).'</span>';
                })
                ->editColumn('total_custom_pay_6', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_6.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_6, true).'</span>';
                })
                ->editColumn('total_custom_pay_7', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_7.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_7, true).'</span>';
                })
                ->editColumn('closed_at', function ($row) {
                    if ($row->status == 'close') {
                        return $this->productUtil->format_date($row->closed_at, true);
                    } else {
                        return '';
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return $this->productUtil->format_date($row->created_at, true);
                })
                ->addColumn('total', function ($row) {
                    $total = $row->total_card_payment + $row->total_cheque_payment + $row->total_cash_payment + $row->total_bank_transfer_payment + $row->total_other_payment + $row->total_advance_payment + $row->total_custom_pay_1 + $row->total_custom_pay_2 + $row->total_custom_pay_3 + $row->total_custom_pay_4 + $row->total_custom_pay_5 + $row->total_custom_pay_6 + $row->total_custom_pay_7;

                    return '<span data-orig-value="'.$total.'" >'.$this->transactionUtil->num_f($total, true).'</span>';
                })

                ->addColumn('action', '@if(auth()->user()->can("view_cash_register"))<button type="button" data-href="{{action(\'App\Http\Controllers\CashRegisterController@show\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max btn-modal" 
                    data-container=".view_register"><i class="fas fa-eye" aria-hidden="true"></i> @lang("messages.view")</button>@endif @if($status != "close" && auth()->user()->can("close_cash_register"))<button type="button" data-href="{{action(\'App\Http\Controllers\CashRegisterController@getCloseRegister\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error tw-w-max btn-modal" 
                        data-container=".view_register"><i class="fas fa-window-close"></i> @lang("messages.close")</button> @endif')
                ->filterColumn('user_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, ''), '<br>', COALESCE(u.email, '')) like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['action', 'user_name', 'total_card_payment', 'total_cheque_payment', 'total_cash_payment', 'total_bank_transfer_payment', 'total_other_payment', 'total_advance_payment', 'total_custom_pay_1', 'total_custom_pay_2', 'total_custom_pay_3', 'total_custom_pay_4', 'total_custom_pay_5', 'total_custom_pay_6', 'total_custom_pay_7', 'total'])
                ->make(true);
        }

        $users = User::forDropdown($business_id, false);
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        return view('report.register_report')
                    ->with(compact('users', 'payment_types'));
    }

    /**
     * Shows sales representative report
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesRepresentativeReport(Request $request)
    {
        if (! auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $users = User::allUsersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        return view('report.sales_representative')
                ->with(compact('users', 'business_locations', 'pos_settings'));
    }

    /**
     * Shows sales representative total expense
     *
     * @return json
     */
    public function getSalesRepresentativeTotalExpense(Request $request)
    {
        if (! auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            $filters = $request->only(['expense_for', 'location_id', 'start_date', 'end_date']);

            $total_expense = $this->transactionUtil->getExpenseReport($business_id, $filters, 'total');

            return $total_expense;
        }
    }

    /**
     * Shows sales representative total sales
     *
     * @return json
     */
    public function getSalesRepresentativeTotalSell(Request $request)
    {
        if (! auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $location_id = $request->get('location_id');
            $created_by = $request->get('created_by');

            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start_date, $end_date, $location_id, $created_by);

            //Get Sell Return details
            $transaction_types = [
                'sell_return',
            ];
            $sell_return_details = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start_date,
                $end_date,
                $location_id,
                $created_by
            );

            $total_sell_return = ! empty($sell_return_details['total_sell_return_exc_tax']) ? $sell_return_details['total_sell_return_exc_tax'] : 0;
            $total_sell = $sell_details['total_sell_exc_tax'] - $total_sell_return;

            return [
                'total_sell_exc_tax' => $sell_details['total_sell_exc_tax'],
                'total_sell_return_exc_tax' => $total_sell_return,
                'total_sell' => $total_sell,
            ];
        }
    }

    /**
     * Shows sales representative total commission
     *
     * @return json
     */
    public function getSalesRepresentativeTotalCommission(Request $request)
    {
        if (! auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $location_id = $request->get('location_id');
            $commission_agent = $request->get('commission_agent');

            $business_details = $this->businessUtil->getDetails($business_id);
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

            $commsn_calculation_type = empty($pos_settings['cmmsn_calculation_type']) || $pos_settings['cmmsn_calculation_type'] == 'invoice_value' ? 'invoice_value' : $pos_settings['cmmsn_calculation_type'];

            $commission_percentage = User::find($commission_agent)->cmmsn_percent;

            if ($commsn_calculation_type == 'payment_received') {
                $payment_details = $this->transactionUtil->getTotalPaymentWithCommission($business_id, $start_date, $end_date, $location_id, $commission_agent);

                //Get Commision
                $total_commission = $commission_percentage * $payment_details['total_payment_with_commission'] / 100;

                return ['total_payment_with_commission' => $payment_details['total_payment_with_commission'] ?? 0,
                    'total_commission' => $total_commission,
                    'commission_percentage' => $commission_percentage,
                ];
            }

            $sell_details = $this->transactionUtil->getTotalSellCommission($business_id, $start_date, $end_date, $location_id, $commission_agent);

            //Get Commision
            $total_commission = $commission_percentage * $sell_details['total_sales_with_commission'] / 100;

            return ['total_sales_with_commission' => $sell_details['total_sales_with_commission'],
                'total_commission' => $total_commission,
                'commission_percentage' => $commission_percentage,
            ];
        }
    }

    /**
     * Seller Daily Report - Shows products sold by each seller per day
     */
    public function getSellerDailyReport(Request $request)
    {
        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $users = User::allUsersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.seller_daily_report')->with(compact('users', 'business_locations'));
    }

    /**
     * Seller Daily Report Data - AJAX
     */
    public function getSellerDailyReportData(Request $request)
    {
        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $user_id = $request->get('user_id');
        $location_id = $request->get('location_id');

        $query = \App\TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
            ->join('users', 'transactions.created_by', '=', 'users.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', $start_date)
                  ->whereDate('transactions.transaction_date', '<=', $end_date);
        }

        if (!empty($user_id)) {
            $query->where('transactions.created_by', $user_id);
        }

        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }

        $sales = $query->select(
            \DB::raw('DATE(transactions.transaction_date) as sale_date'),
            'transactions.created_by',
            \DB::raw("CONCAT(users.first_name, ' ', COALESCE(users.last_name, '')) as seller_name"),
            'products.name as product_name',
            'products.sku',
            \DB::raw('SUM(transaction_sell_lines.quantity) as total_qty'),
            \DB::raw('SUM(transaction_sell_lines.unit_price_before_discount * transaction_sell_lines.quantity) as total_price'),
            \DB::raw('COUNT(DISTINCT transactions.id) as num_transactions')
        )
        ->groupBy('sale_date', 'transactions.created_by', 'transaction_sell_lines.product_id')
        ->orderBy('sale_date', 'desc')
        ->orderBy('seller_name')
        ->orderBy('product_name')
        ->get();

        return datatables()->of($sales)
            ->editColumn('sale_date', function($row) {
                return \Carbon\Carbon::parse($row->sale_date)->format('d M Y');
            })
            ->editColumn('total_price', function($row) {
                return number_format($row->total_price, 2);
            })
            ->editColumn('total_qty', function($row) {
                return number_format($row->total_qty, 2);
            })
            ->make(true);
    }

    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockExpiryReport(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //TODO:: Need to display reference number and edit expiry date button

        //Return the details in ajax call
        if ($request->ajax()) {
            $query = PurchaseLine::leftjoin(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                            ->leftjoin(
                                'products as p',
                                'purchase_lines.product_id',
                                '=',
                                'p.id'
                            )
                            ->leftjoin(
                                'variations as v',
                                'purchase_lines.variation_id',
                                '=',
                                'v.id'
                            )
                            ->leftjoin(
                                'product_variations as pv',
                                'v.product_variation_id',
                                '=',
                                'pv.id'
                            )
                            ->leftjoin('business_locations as l', 't.location_id', '=', 'l.id')
                            ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                            ->where('t.business_id', $business_id)
                            //->whereNotNull('p.expiry_period')
                            //->whereNotNull('p.expiry_period_type')
                            //->whereNotNull('exp_date')
                            ->where('p.enable_stock', 1);
            // ->whereRaw('purchase_lines.quantity > purchase_lines.quantity_sold + quantity_adjusted + quantity_returned');

            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('t.location_id', $location_id)
                        //If filter by location then hide products not available in that location
                        ->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                        ->where(function ($q) use ($location_id) {
                            $q->where('pl.location_id', $location_id);
                        });
            }

            if (! empty($request->input('category_id'))) {
                $query->where('p.category_id', $request->input('category_id'));
            }
            if (! empty($request->input('sub_category_id'))) {
                $query->where('p.sub_category_id', $request->input('sub_category_id'));
            }
            if (! empty($request->input('brand_id'))) {
                $query->where('p.brand_id', $request->input('brand_id'));
            }
            if (! empty($request->input('unit_id'))) {
                $query->where('p.unit_id', $request->input('unit_id'));
            }
            if (! empty($request->input('exp_date_filter'))) {
                $query->whereDate('exp_date', '<=', $request->input('exp_date_filter'));
            }

            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (! empty($only_mfg_products)) {
                $query->where('t.type', 'production_purchase');
            }

            $report = $query->select(
                'p.name as product',
                'p.sku',
                'p.type as product_type',
                'v.name as variation',
                'v.sub_sku',
                'pv.name as product_variation',
                'l.name as location',
                'mfg_date',
                'exp_date',
                'u.short_name as unit',
                DB::raw('SUM(COALESCE(quantity, 0) - COALESCE(quantity_sold, 0) - COALESCE(quantity_adjusted, 0) - COALESCE(quantity_returned, 0)) as stock_left'),
                't.ref_no',
                't.id as transaction_id',
                'purchase_lines.id as purchase_line_id',
                'purchase_lines.lot_number'
            )
            ->having('stock_left', '>', 0)
            ->groupBy('purchase_lines.variation_id')
            ->groupBy('purchase_lines.exp_date')
            ->groupBy('purchase_lines.lot_number');

            return Datatables::of($report)
                ->editColumn('product', function ($row) {
                    if ($row->product_type == 'variable') {
                        return $row->product.' - '.
                        $row->product_variation.' - '.$row->variation.' ('.$row->sub_sku.')';
                    } else {
                        return $row->product.' ('.$row->sku.')';
                    }
                })
                ->editColumn('mfg_date', function ($row) {
                    if (! empty($row->mfg_date)) {
                        return $this->productUtil->format_date($row->mfg_date);
                    } else {
                        return '--';
                    }
                })
                // ->editColumn('exp_date', function ($row) {
                //     if (!empty($row->exp_date)) {
                //         $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                //         $carbon_now = \Carbon::now();
                //         if ($carbon_now->diffInDays($carbon_exp, false) >= 0) {
                //             return $this->productUtil->format_date($row->exp_date) . '<br><small>( <span class="time-to-now">' . $row->exp_date . '</span> )</small>';
                //         } else {
                //             return $this->productUtil->format_date($row->exp_date) . ' &nbsp; <span class="label label-danger no-print">' . __('report.expired') . '</span><span class="print_section">' . __('report.expired') . '</span><br><small>( <span class="time-from-now">' . $row->exp_date . '</span> )</small>';
                //         }
                //     } else {
                //         return '--';
                //     }
                // })
                ->editColumn('ref_no', function ($row) {
                    return '<button type="button" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                            .'" class="btn btn-link btn-modal" data-container=".view_modal"  >'.$row->ref_no.'</button>';
                })
                ->editColumn('stock_left', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency stock_left" data-currency_symbol=false data-orig-value="'.$row->stock_left.'" data-unit="'.$row->unit.'" >'.$row->stock_left.'</span> '.$row->unit;
                })
                ->addColumn('edit', function ($row) {
                    $html = '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary stock_expiry_edit_btn" data-transaction_id="'.$row->transaction_id.'" data-purchase_line_id="'.$row->purchase_line_id.'"> <i class="fa fa-edit"></i> '.__('messages.edit').
                    '</button>';

                    if (! empty($row->exp_date)) {
                        $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                        $carbon_now = \Carbon::now();
                        if ($carbon_now->diffInDays($carbon_exp, false) < 0) {
                            $html .= ' <button type="button" class="btn btn-warning btn-xs remove_from_stock_btn" data-href="'.action([\App\Http\Controllers\StockAdjustmentController::class, 'removeExpiredStock'], [$row->purchase_line_id]).'"> <i class="fa fa-trash"></i> '.__('lang_v1.remove_from_stock').
                            '</button>';
                        }
                    }

                    return $html;
                })
                ->rawColumns(['exp_date', 'ref_no', 'edit', 'stock_left'])
                ->make(true);
        }

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $view_stock_filter = [
            \Carbon::now()->subDay()->format('Y-m-d') => __('report.expired'),
            \Carbon::now()->addWeek()->format('Y-m-d') => __('report.expiring_in_1_week'),
            \Carbon::now()->addDays(15)->format('Y-m-d') => __('report.expiring_in_15_days'),
            \Carbon::now()->addMonth()->format('Y-m-d') => __('report.expiring_in_1_month'),
            \Carbon::now()->addMonths(3)->format('Y-m-d') => __('report.expiring_in_3_months'),
            \Carbon::now()->addMonths(6)->format('Y-m-d') => __('report.expiring_in_6_months'),
            \Carbon::now()->addYear()->format('Y-m-d') => __('report.expiring_in_1_year'),
        ];

        return view('report.stock_expiry_report')
                ->with(compact('categories', 'brands', 'units', 'business_locations', 'view_stock_filter'));
    }

    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockExpiryReportEditModal(Request $request, $purchase_line_id)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $purchase_line = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                                ->join(
                                    'products as p',
                                    'purchase_lines.product_id',
                                    '=',
                                    'p.id'
                                )
                                ->where('purchase_lines.id', $purchase_line_id)
                                ->where('t.business_id', $business_id)
                                ->select(['purchase_lines.*', 'p.name', 't.ref_no'])
                                ->first();

            if (! empty($purchase_line)) {
                if (! empty($purchase_line->exp_date)) {
                    $purchase_line->exp_date = date('m/d/Y', strtotime($purchase_line->exp_date));
                }
            }

            return view('report.partials.stock_expiry_edit_modal')
                ->with(compact('purchase_line'));
        }
    }

    /**
     * Update product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function updateStockExpiryReport(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            //Return the details in ajax call
            if ($request->ajax()) {
                DB::beginTransaction();

                $input = $request->only(['purchase_line_id', 'exp_date']);

                $purchase_line = PurchaseLine::join(
                    'transactions as t',
                    'purchase_lines.transaction_id',
                    '=',
                    't.id'
                )
                                    ->join(
                                        'products as p',
                                        'purchase_lines.product_id',
                                        '=',
                                        'p.id'
                                    )
                                    ->where('purchase_lines.id', $input['purchase_line_id'])
                                    ->where('t.business_id', $business_id)
                                    ->select(['purchase_lines.*', 'p.name', 't.ref_no'])
                                    ->first();

                if (! empty($purchase_line) && ! empty($input['exp_date'])) {
                    $purchase_line->exp_date = $this->productUtil->uf_date($input['exp_date']);
                    $purchase_line->save();
                }

                DB::commit();

                $output = ['success' => 1,
                    'msg' => __('lang_v1.updated_succesfully'),
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerGroup(Request $request)
    {
        if (! auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = Transaction::leftjoin('customer_groups AS CG', 'transactions.customer_group_id', '=', 'CG.id')
                        ->where('transactions.business_id', $business_id)
                        ->where('transactions.type', 'sell')
                        ->where('transactions.status', 'final')
                        ->groupBy('transactions.customer_group_id')
                        ->select(DB::raw('SUM(final_total) as total_sell'), 'CG.name');

            $group_id = $request->get('customer_group_id', null);
            if (! empty($group_id)) {
                $query->where('transactions.customer_group_id', $group_id);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('transactions.location_id', $location_id);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            return Datatables::of($query)
                ->editColumn('total_sell', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>'.$row->total_sell.'</span>';
                })
                ->rawColumns(['total_sell'])
                ->make(true);
        }

        $customer_group = CustomerGroup::forDropdown($business_id, false, true);
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.customer_group')
            ->with(compact('customer_group', 'business_locations'));
    }

    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductPurchaseReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
                    )
                    ->join(
                        'variations as v',
                        'purchase_lines.variation_id',
                        '=',
                        'v.id'
                    )
                    ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                    ->join('contacts as c', 't.contact_id', '=', 'c.id')
                    ->join('products as p', 'pv.product_id', '=', 'p.id')
                    ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                    ->where('t.business_id', $business_id)
                    ->where('t.type', 'purchase')
                    ->select(
                        'p.name as product_name',
                        'p.type as product_type',
                        'pv.name as product_variation',
                        'v.name as variation_name',
                        'v.sub_sku',
                        'c.name as supplier',
                        'c.supplier_business_name',
                        't.id as transaction_id',
                        't.ref_no',
                        't.transaction_date as transaction_date',
                        'purchase_lines.purchase_price_inc_tax as unit_purchase_price',
                        DB::raw('(purchase_lines.quantity - purchase_lines.quantity_returned) as purchase_qty'),
                        'purchase_lines.quantity_adjusted',
                        'u.short_name as unit',
                        DB::raw('((purchase_lines.quantity - purchase_lines.quantity_returned - purchase_lines.quantity_adjusted) * purchase_lines.purchase_price_inc_tax) as subtotal')
                    )
                    ->groupBy('purchase_lines.id');
            if (! empty($variation_id)) {
                $query->where('purchase_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $supplier_id = $request->get('supplier_id', null);
            if (! empty($supplier_id)) {
                $query->where('t.contact_id', $supplier_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (! empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $product_name;
                })
                 ->editColumn('ref_no', function ($row) {
                     return '<a data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->ref_no.'</a>';
                 })
                 ->editColumn('purchase_qty', function ($row) {
                     return '<span data-is_quantity="true" class="display_currency purchase_qty" data-currency_symbol=false data-orig-value="'.(float) $row->purchase_qty.'" data-unit="'.$row->unit.'" >'.(float) $row->purchase_qty.'</span> '.$row->unit;
                 })
                 ->editColumn('quantity_adjusted', function ($row) {
                     return '<span data-is_quantity="true" class="display_currency quantity_adjusted" data-currency_symbol=false data-orig-value="'.(float) $row->quantity_adjusted.'" data-unit="'.$row->unit.'" >'.(float) $row->quantity_adjusted.'</span> '.$row->unit;
                 })
                 ->editColumn('subtotal', function ($row) {
                     return '<span class="row_subtotal"  
                     data-orig-value="'.$row->subtotal.'">'.
                     $this->transactionUtil->num_f($row->subtotal, true).'</span>';
                 })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('unit_purchase_price', function ($row) {
                    return $this->transactionUtil->num_f($row->unit_purchase_price, true);
                })
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$supplier}}')
                ->rawColumns(['ref_no', 'unit_purchase_price', 'subtotal', 'purchase_qty', 'quantity_adjusted', 'supplier'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id);
        $brands = Brands::forDropdown($business_id);

        return view('report.product_purchase_report')
            ->with(compact('business_locations', 'suppliers', 'brands'));
    }

    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $custom_labels = json_decode(session('business.custom_labels'), true);

        $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : '';
        $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : '';

        if ($request->ajax()) {
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('tax_rates', 'transaction_sell_lines.tax_id', '=', 'tax_rates.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->with('transaction.payment_lines')
                ->whereNull('parent_sell_line_id')
                ->select(
                    'p.name as product_name',
                    'p.type as product_type',
                    'p.product_custom_field1 as product_custom_field1',
                    'p.product_custom_field2 as product_custom_field2',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    'c.name as customer',
                    'c.mobile as contact_no',
                    'c.email as contact_email',
                    'c.supplier_business_name',
                    'c.contact_id',
                    't.id as transaction_id',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    'transaction_sell_lines.unit_price_before_discount as unit_price',
                    'transaction_sell_lines.unit_price_inc_tax as unit_sale_price',
                    DB::raw('(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as sell_qty'),
                    'transaction_sell_lines.line_discount_type as discount_type',
                    'transaction_sell_lines.line_discount_amount as discount_amount',
                    'transaction_sell_lines.item_tax',
                    'tax_rates.name as tax',
                    'u.short_name as unit',
                    'transaction_sell_lines.parent_sell_line_id',
                    DB::raw('((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal')
                )
                ->groupBy('transaction_sell_lines.id');

            if (! empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            $customer_group_id = $request->get('customer_group_id', null);
            if (! empty($customer_group_id)) {
                $query->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')
                ->where('CG.id', $customer_group_id);
            }

            $category_id = $request->get('category_id', null);
            if (! empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (! empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $product_name;
                })
                 ->editColumn('invoice_no', function ($row) {
                     return '<a data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->invoice_no.'</a>';
                 })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="unit_sale_price" data-orig-value="'.$row->unit_sale_price.'">'.
                    $this->transactionUtil->num_f($row->unit_sale_price, true).'</span>';
                })
                ->editColumn('sell_qty', function ($row) {
                    //ignore child sell line of combo product
                    $class = is_null($row->parent_sell_line_id) ? 'sell_qty' : '';

                    return '<span class="'.$class.'"  data-orig-value="'.$row->sell_qty.'" 
                    data-unit="'.$row->unit.'" >'.
                    $this->transactionUtil->num_f($row->sell_qty, false, null, true).'</span> '.$row->unit;
                })
                 ->editColumn('subtotal', function ($row) {
                     //ignore child sell line of combo product
                     $class = is_null($row->parent_sell_line_id) ? 'row_subtotal' : '';

                     return '<span class="'.$class.'"  data-orig-value="'.$row->subtotal.'">'.
                    $this->transactionUtil->num_f($row->subtotal, true).'</span>';
                 })
                ->editColumn('unit_price', function ($row) {
                    return '<span class="unit_price" data-orig-value="'.$row->unit_price.'">'.
                    $this->transactionUtil->num_f($row->unit_price, true).'</span>';
                })
                ->editColumn('discount_amount', '
                    @if($discount_type == "percentage")
                        {{@num_format($discount_amount)}} %
                    @elseif($discount_type == "fixed")
                        {{@num_format($discount_amount)}}
                    @endif
                    ')
                ->editColumn('tax', function ($row) {
                    return $this->transactionUtil->num_f($row->item_tax, true)
                     .'<br>'.'<span data-orig-value="'.$row->item_tax.'" 
                     class="tax" data-unit="'.$row->tax.'"><small>('.$row->tax.')</small></span>';
                })
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->transaction->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = $payment_types[$methods[0]] ?? '';
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = ! empty($payment_method) ? '<span class="payment-method" data-orig-value="'.$payment_method.'" data-status-name="'.$payment_method.'">'.$payment_method.'</span>' : '';

                    return $html;
                })
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$customer}}')
                ->rawColumns(['invoice_no', 'unit_sale_price', 'subtotal', 'sell_qty', 'discount_amount', 'unit_price', 'tax', 'customer', 'payment_methods'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id);
        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $customer_group = CustomerGroup::forDropdown($business_id, false, true);

        return view('report.product_sell_report')
            ->with(compact('business_locations', 'customers', 'categories', 'brands',
                'customer_group', 'product_custom_field1', 'product_custom_field2'));
    }

    /**
     * Shows product purchase report with purchase details
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellReportWithPurchase(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'transaction_sell_lines_purchase_lines as tspl',
                    'transaction_sell_lines.id',
                    '=',
                    'tspl.sell_line_id'
                )
                ->join(
                    'purchase_lines as pl',
                    'tspl.purchase_line_id',
                    '=',
                    'pl.id'
                )
                ->join(
                    'transactions as purchase',
                    'pl.transaction_id',
                    '=',
                    'purchase.id'
                )
                ->leftjoin('contacts as supplier', 'purchase.contact_id', '=', 'supplier.id')
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->leftjoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->select(
                    'p.name as product_name',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    'c.name as customer',
                    'c.mobile as contact_no',
                    'c.email as contact_email',
                    'c.supplier_business_name',
                    't.id as transaction_id',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    'tspl.quantity as purchase_quantity',
                    'u.short_name as unit',
                    'supplier.name as supplier_name',
                    'purchase.ref_no as ref_no',
                    'purchase.type as purchase_type',
                    'pl.lot_number'
                );

            if (! empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }
            $customer_group_id = $request->get('customer_group_id', null);
            if (! empty($customer_group_id)) {
                $query->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')
                ->where('CG.id', $customer_group_id);
            }

            $category_id = $request->get('category_id', null);
            if (! empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (! empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $product_name;
                })
                 ->editColumn('invoice_no', function ($row) {
                     return '<a data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->invoice_no.'</a>';
                 })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>'.$row->unit_sale_price.'</span>';
                })
                ->editColumn('purchase_quantity', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency purchase_quantity" data-currency_symbol=false data-orig-value="'.(float) $row->purchase_quantity.'" data-unit="'.$row->unit.'" >'.(float) $row->purchase_quantity.'</span> '.$row->unit;
                })
                ->editColumn('ref_no', '
                    @if($purchase_type == "opening_stock")
                        <i><small class="help-block">(@lang("lang_v1.opening_stock"))</small></i>
                    @else
                        {{$ref_no}}
                    @endif
                    ')
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$customer}}')
                ->rawColumns(['invoice_no', 'purchase_quantity', 'ref_no', 'customer'])
                ->make(true);
        }
    }

    /**
     * Shows product lot report
     *
     * @return \Illuminate\Http\Response
     */
    public function getLotReport(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $query = Product::where('products.business_id', $business_id)
                    ->leftjoin('units', 'products.unit_id', '=', 'units.id')
                    ->join('variations as v', 'products.id', '=', 'v.product_id')
                    ->join('purchase_lines as pl', 'v.id', '=', 'pl.variation_id')
                    ->leftjoin(
                        'transaction_sell_lines_purchase_lines as tspl',
                        'pl.id',
                        '=',
                        'tspl.purchase_line_id'
                    )
                    ->join('transactions as t', 'pl.transaction_id', '=', 't.id');

            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = 'WHERE ';

            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);

                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter = " LEFT JOIN transactions as t2 on pls.transaction_id=t2.id WHERE t2.location_id IN ($locations_imploded) AND ";
            }

            if (! empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('t.location_id', $location_id)
                    //If filter by location then hide products not available in that location
                    ->ForLocation($location_id);

                $location_filter = "LEFT JOIN transactions as t2 on pls.transaction_id=t2.id WHERE t2.location_id=$location_id AND ";
            }

            if (! empty($request->input('category_id'))) {
                $query->where('products.category_id', $request->input('category_id'));
            }

            if (! empty($request->input('sub_category_id'))) {
                $query->where('products.sub_category_id', $request->input('sub_category_id'));
            }

            if (! empty($request->input('brand_id'))) {
                $query->where('products.brand_id', $request->input('brand_id'));
            }

            if (! empty($request->input('unit_id'))) {
                $query->where('products.unit_id', $request->input('unit_id'));
            }

            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (! empty($only_mfg_products)) {
                $query->where('t.type', 'production_purchase');
            }

            $products = $query->select(
                'products.name as product',
                'v.name as variation_name',
                'sub_sku',
                'pl.lot_number',
                'pl.exp_date as exp_date',
                DB::raw("( COALESCE((SELECT SUM(quantity - quantity_returned) from purchase_lines as pls $location_filter variation_id = v.id AND lot_number = pl.lot_number), 0) - 
                    SUM(COALESCE((tspl.quantity - tspl.qty_returned), 0))) as stock"),
                // DB::raw("(SELECT SUM(IF(transactions.type='sell', TSL.quantity, -1* TPL.quantity) ) FROM transactions
                //         LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id

                //         LEFT JOIN purchase_lines AS TPL ON transactions.id=TPL.transaction_id

                //         WHERE transactions.status='final' AND transactions.type IN ('sell', 'sell_return') $location_filter
                //         AND (TSL.product_id=products.id OR TPL.product_id=products.id)) as total_sold"),

                DB::raw('COALESCE(SUM(IF(tspl.sell_line_id IS NULL, 0, (tspl.quantity - tspl.qty_returned)) ), 0) as total_sold'),
                DB::raw('COALESCE(SUM(IF(tspl.stock_adjustment_line_id IS NULL, 0, tspl.quantity ) ), 0) as total_adjusted'),
                'products.type',
                'units.short_name as unit'
            )
            ->whereNotNull('pl.lot_number')
            ->groupBy('v.id')
            ->groupBy('pl.lot_number');

            return Datatables::of($products)
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;

                    return '<span data-is_quantity="true" class="display_currency total_stock" data-currency_symbol=false data-orig-value="'.(float) $stock.'" data-unit="'.$row->unit.'" >'.(float) $stock.'</span> '.$row->unit;
                })
                ->editColumn('product', function ($row) {
                    if ($row->variation_name != 'DUMMY') {
                        return $row->product.' ('.$row->variation_name.')';
                    } else {
                        return $row->product;
                    }
                })
                ->editColumn('total_sold', function ($row) {
                    if ($row->total_sold) {
                        return '<span data-is_quantity="true" class="display_currency total_sold" data-currency_symbol=false data-orig-value="'.(float) $row->total_sold.'" data-unit="'.$row->unit.'" >'.(float) $row->total_sold.'</span> '.$row->unit;
                    } else {
                        return '0'.' '.$row->unit;
                    }
                })
                ->editColumn('total_adjusted', function ($row) {
                    if ($row->total_adjusted) {
                        return '<span data-is_quantity="true" class="display_currency total_adjusted" data-currency_symbol=false data-orig-value="'.(float) $row->total_adjusted.'" data-unit="'.$row->unit.'" >'.(float) $row->total_adjusted.'</span> '.$row->unit;
                    } else {
                        return '0'.' '.$row->unit;
                    }
                })
                ->editColumn('exp_date', function ($row) {
                    if (! empty($row->exp_date)) {
                        $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                        $carbon_now = \Carbon::now();
                        if ($carbon_now->diffInDays($carbon_exp, false) >= 0) {
                            return $this->productUtil->format_date($row->exp_date).'<br><small>( <span class="time-to-now">'.$row->exp_date.'</span> )</small>';
                        } else {
                            return $this->productUtil->format_date($row->exp_date).' &nbsp; <span class="label label-danger no-print">'.__('report.expired').'</span><span class="print_section">'.__('report.expired').'</span><br><small>( <span class="time-from-now">'.$row->exp_date.'</span> )</small>';
                        }
                    } else {
                        return '--';
                    }
                })
                ->removeColumn('unit')
                ->removeColumn('id')
                ->removeColumn('variation_name')
                ->rawColumns(['exp_date', 'stock', 'total_sold', 'total_adjusted'])
                ->make(true);
        }

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.lot_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations'));
    }

    /**
     * Shows purchase payment report
     *
     * @return \Illuminate\Http\Response
     */
    public function purchasePaymentReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $supplier_id = $request->get('supplier_id', null);
            $contact_filter1 = ! empty($supplier_id) ? "AND t.contact_id=$supplier_id" : '';
            $contact_filter2 = ! empty($supplier_id) ? "AND transactions.contact_id=$supplier_id" : '';

            $location_id = $request->get('location_id', null);

            $parent_payment_query_part = empty($location_id) ? 'AND transaction_payments.parent_id IS NULL' : '';

            $query = TransactionPayment::leftjoin('transactions as t', function ($join) use ($business_id) {
                $join->on('transaction_payments.transaction_id', '=', 't.id')
                    ->where('t.business_id', $business_id)
                    ->whereIn('t.type', ['purchase', 'opening_balance']);
            })
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) use ($business_id, $contact_filter1, $contact_filter2, $parent_payment_query_part) {
                    $q->whereRaw("(transaction_payments.transaction_id IS NOT NULL AND t.type IN ('purchase', 'opening_balance')  $parent_payment_query_part $contact_filter1)")
                        ->orWhereRaw("EXISTS(SELECT * FROM transaction_payments as tp JOIN transactions ON tp.transaction_id = transactions.id WHERE transactions.type IN ('purchase', 'opening_balance') AND transactions.business_id = $business_id AND tp.parent_id=transaction_payments.id $contact_filter2)");
                })

                ->select(
                    DB::raw("IF(transaction_payments.transaction_id IS NULL, 
                                (SELECT c.name FROM transactions as ts
                                JOIN contacts as c ON ts.contact_id=c.id 
                                WHERE ts.id=(
                                        SELECT tps.transaction_id FROM transaction_payments as tps
                                        WHERE tps.parent_id=transaction_payments.id LIMIT 1
                                    )
                                ),
                                (SELECT CONCAT(COALESCE(c.supplier_business_name, ''), '<br>', c.name) FROM transactions as ts JOIN
                                    contacts as c ON ts.contact_id=c.id
                                    WHERE ts.id=t.id 
                                )
                            ) as supplier"),
                    'transaction_payments.amount',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    't.ref_no',
                    't.id as transaction_id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number',
                    'transaction_no',
                    'transaction_payments.id as DT_RowId'
                )
                ->groupBy('transaction_payments.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

            return Datatables::of($query)
                 ->editColumn('ref_no', function ($row) {
                     if (! empty($row->ref_no)) {
                         return '<a data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->ref_no.'</a>';
                     } else {
                         return '';
                     }
                 })
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) use ($payment_types) {
                    $method = ! empty($payment_types[$row->method]) ? $payment_types[$row->method] : '';
                    if ($row->method == 'cheque') {
                        $method .= '<br>('.__('lang_v1.cheque_no').': '.$row->cheque_number.')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>('.__('lang_v1.card_transaction_no').': '.$row->card_transaction_number.')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>('.__('lang_v1.bank_account_no').': '.$row->bank_account_number.')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    }

                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="paid-amount" data-orig-value="'.$row->amount.'">'.
                    $this->transactionUtil->num_f($row->amount, true).'</span>';
                })
                ->addColumn('action', '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary view_payment" data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'viewPayment\'], [$DT_RowId]) }}">@lang("messages.view")
                    </button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['ref_no', 'amount', 'method', 'action', 'supplier'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);

        return view('report.purchase_payment_report')
            ->with(compact('business_locations', 'suppliers'));
    }

    /**
     * Shows sell payment report
     *
     * @return \Illuminate\Http\Response
     */
    public function sellPaymentReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
        if ($request->ajax()) {
            $customer_id = $request->get('supplier_id', null);
            $contact_filter1 = ! empty($customer_id) ? "AND t.contact_id=$customer_id" : '';
            $contact_filter2 = ! empty($customer_id) ? "AND transactions.contact_id=$customer_id" : '';

            $location_id = $request->get('location_id', null);
            $parent_payment_query_part = empty($location_id) ? 'AND transaction_payments.parent_id IS NULL' : '';

            $query = TransactionPayment::leftjoin('transactions as t', function ($join) use ($business_id) {
                $join->on('transaction_payments.transaction_id', '=', 't.id')
                    ->where('t.business_id', $business_id)
                    ->whereIn('t.type', ['sell', 'opening_balance']);
            })
                ->leftjoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')

            
            //     DB::raw("IF(transaction_payments.transaction_id IS NULL, 
            //     (SELECT c.name FROM transactions as ts
            //     JOIN contacts as c ON ts.contact_id=c.id 
            //     WHERE ts.id=(
            //             SELECT tps.transaction_id FROM transaction_payments as tps
            //             WHERE tps.parent_id=transaction_payments.id LIMIT 1
            //         )
            //     ),
            //     (SELECT CONCAT(COALESCE(CONCAT(c.supplier_business_name, '<br>'), ''), c.name) FROM transactions as ts JOIN
            //         contacts as c ON ts.contact_id=c.id
            //         WHERE ts.id=t.id 
            //     )
            // ) as customer")
            // remove above line from select and below join becouse customer search not work
            
                ->leftJoin(DB::raw("(
                    SELECT 
                        tp.id as payment_id, 
                        IF(tp.transaction_id IS NULL, 
                            (SELECT c.name 
                             FROM transactions as ts
                             JOIN contacts as c ON ts.contact_id = c.id 
                             WHERE ts.id = (
                                SELECT tps.transaction_id 
                                FROM transaction_payments as tps 
                                WHERE tps.parent_id = tp.id 
                                LIMIT 1
                             )
                            ), 
                            CONCAT(COALESCE(CONCAT(c.supplier_business_name, '<br>'), ''), c.name)
                        ) as customer_name
                    FROM transaction_payments tp
                    LEFT JOIN transactions t ON tp.transaction_id = t.id
                    LEFT JOIN contacts c ON t.contact_id = c.id
                ) as customer_subquery"), 'transaction_payments.id', '=', 'customer_subquery.payment_id')              
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) use ($business_id, $contact_filter1, $contact_filter2, $parent_payment_query_part) {
                    $q->whereRaw("(transaction_payments.transaction_id IS NOT NULL AND t.type IN ('sell', 'opening_balance') $parent_payment_query_part $contact_filter1)")
                        ->orWhereRaw("EXISTS(SELECT * FROM transaction_payments as tp JOIN transactions ON tp.transaction_id = transactions.id WHERE transactions.type IN ('sell', 'opening_balance') AND transactions.business_id = $business_id AND tp.parent_id=transaction_payments.id $contact_filter2)");
                })
                ->select(
                    'customer_subquery.customer_name as customer',
                    'transaction_payments.amount',
                    'transaction_payments.is_return',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    'transaction_payments.transaction_no',
                    't.invoice_no',
                    'c.contact_id',
                    't.id as transaction_id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number',
                    'transaction_payments.id as DT_RowId',
                    'CG.name as customer_group'
                )
                ->groupBy('transaction_payments.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($request->get('customer_group_id'))) {
                $query->where('CG.id', $request->get('customer_group_id'));
            }

            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            if (! empty($request->has('commission_agent'))) {
                $query->where('t.commission_agent', $request->get('commission_agent'));
            }

            if (! empty($request->get('payment_types'))) {
                $query->where('transaction_payments.method', $request->get('payment_types'));
            }

            return Datatables::of($query)
                 ->editColumn('invoice_no', function ($row) {
                     if (! empty($row->transaction_id)) {
                         return '<a data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->invoice_no.'</a>';
                     } else {
                         return '';
                     }
                 })
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) use ($payment_types) {
                    $method = ! empty($payment_types[$row->method]) ? $payment_types[$row->method] : '';
                    if ($row->method == 'cheque') {
                        $method .= '<br>('.__('lang_v1.cheque_no').': '.$row->cheque_number.')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>('.__('lang_v1.card_transaction_no').': '.$row->card_transaction_number.')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>('.__('lang_v1.bank_account_no').': '.$row->bank_account_number.')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    }
                    if ($row->is_return == 1) {
                        $method .= '<br><small>('.__('lang_v1.change_return').')</small>';
                    }

                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    $amount = $row->is_return == 1 ? -1 * $row->amount : $row->amount;

                    return '<span class="paid-amount" data-orig-value="'.$amount.'" 
                    >'.$this->transactionUtil->num_f($amount, true).'</span>';
                })
                ->addColumn('action', '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary view_payment" data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'viewPayment\'], [$DT_RowId]) }}">@lang("messages.view")
                    </button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['invoice_no', 'amount', 'method', 'action', 'customer'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id, false);
        $customer_groups = CustomerGroup::forDropdown($business_id, false, true);

        return view('report.sell_payment_report')
            ->with(compact('business_locations', 'customers', 'payment_types', 'customer_groups'));
    }

    /**
     * Shows tables report
     *
     * @return \Illuminate\Http\Response
     */
    public function getTableReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = ResTable::leftjoin('transactions AS T', 'T.res_table_id', '=', 'res_tables.id')
                        ->where('T.business_id', $business_id)
                        ->where('T.type', 'sell')
                        ->where('T.status', 'final')
                        ->groupBy('res_tables.id')
                        ->select(DB::raw('SUM(final_total) as total_sell'), 'res_tables.name as table');

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('T.location_id', $location_id);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            return Datatables::of($query)
                ->editColumn('total_sell', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">'.$row->total_sell.'</span>';
                })
                ->rawColumns(['total_sell'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.table_report')
            ->with(compact('business_locations'));
    }

    /**
     * Shows service staff report
     *
     * @return \Illuminate\Http\Response
     */
    public function getServiceStaffReport(Request $request)
    {
        if (! auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $waiters = $this->transactionUtil->serviceStaffDropdown($business_id);

        return view('report.service_staff_report')
            ->with(compact('business_locations', 'waiters'));
    }

    /**
     * Shows product sell report grouped by date
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellGroupedReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->get('location_id', null);

        $vld_str = '';
        if (! empty($location_id)) {
            $vld_str = "AND vld.location_id=$location_id";
        }

        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->select(
                    'p.name as product_name',
                    'p.enable_stock',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    't.id as transaction_id',
                    't.transaction_date as transaction_date',
                    'transaction_sell_lines.parent_sell_line_id',
                    DB::raw('DATE_FORMAT(t.transaction_date, "%Y-%m-%d") as formated_date'),
                    DB::raw("(SELECT SUM(vld.qty_available) FROM variation_location_details as vld WHERE vld.variation_id=v.id $vld_str) as current_stock"),
                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as total_qty_sold'),
                    'u.short_name as unit',
                    DB::raw('SUM((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal')
                )
                ->groupBy('v.id')
                ->groupBy('formated_date');

            if (! empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            $customer_group_id = $request->get('customer_group_id', null);
            if (! empty($customer_group_id)) {
                $query->leftjoin('contacts AS c', 't.contact_id', '=', 'c.id')
                    ->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')
                ->where('CG.id', $customer_group_id);
            }

            $category_id = $request->get('category_id', null);
            if (! empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (! empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $product_name;
                })
                ->editColumn('transaction_date', '{{@format_date($formated_date)}}')
                ->editColumn('total_qty_sold', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency sell_qty" data-currency_symbol=false data-orig-value="'.(float) $row->total_qty_sold.'" data-unit="'.$row->unit.'" >'.(float) $row->total_qty_sold.'</span> '.$row->unit;
                })
                ->editColumn('current_stock', function ($row) {
                    if ($row->enable_stock) {
                        return '<span data-is_quantity="true" class="display_currency current_stock" data-currency_symbol=false data-orig-value="'.(float) $row->current_stock.'" data-unit="'.$row->unit.'" >'.(float) $row->current_stock.'</span> '.$row->unit;
                    } else {
                        return '';
                    }
                })
                 ->editColumn('subtotal', function ($row) {
                     $class = is_null($row->parent_sell_line_id) ? 'row_subtotal' : '';

                     return '<span class="'.$class.'" data-orig-value="'.$row->subtotal.'">'.
                     $this->transactionUtil->num_f($row->subtotal, true).'</span>';
                 })

                ->rawColumns(['current_stock', 'subtotal', 'total_qty_sold'])
                ->make(true);
        }
    }

    /**
     * Shows product sell report grouped by date
     *
     * @return \Illuminate\Http\Response
     */
    public function productSellReportBy(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->get('location_id', null);
        $group_by = $request->get('group_by', null);

        $vld_str = '';
        if (! empty($location_id)) {
            $vld_str = "AND vld.location_id=$location_id";
        }

        if ($request->ajax()) {
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->leftjoin(
                    'products as p',
                    'transaction_sell_lines.product_id',
                    '=',
                    'p.id'
                )
                ->leftjoin('categories as cat', 'p.category_id', '=', 'cat.id')
                ->leftjoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->select(
                    'b.name as brand_name',
                    'cat.name as category_name',
                    DB::raw("(SELECT SUM(vld.qty_available) FROM variation_location_details as vld WHERE vld.variation_id=transaction_sell_lines.variation_id $vld_str) as current_stock"),
                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as total_qty_sold'),
                    DB::raw('SUM((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal'),
                    'transaction_sell_lines.parent_sell_line_id'
                );

            if ($group_by == 'category') {
                $query->groupBy('cat.id');
            } elseif ($group_by == 'brand') {
                $query->groupBy('b.id');
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            $customer_group_id = $request->get('customer_group_id', null);
            if (! empty($customer_group_id)) {
                $query->leftjoin('contacts AS c', 't.contact_id', '=', 'c.id')
                    ->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')
                ->where('CG.id', $customer_group_id);
            }

            $category_id = $request->get('category_id', null);
            if (! empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (! empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('category_name', '{{$category_name ?? __("lang_v1.uncategorized")}}')
                ->editColumn('brand_name', '{{$brand_name ?? __("lang_v1.no_brand")}}')
                ->editColumn('total_qty_sold', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency sell_qty" data-currency_symbol=false data-orig-value="'.(float) $row->total_qty_sold.'" data-unit="" >'.(float) $row->total_qty_sold.'</span> '.$row->unit;
                })
                ->editColumn('current_stock', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency current_stock" data-currency_symbol=false data-orig-value="'.(float) $row->current_stock.'" data-unit="">'.(float) $row->current_stock.'</span> ';
                })
                 ->editColumn('subtotal', function ($row) {
                     $class = is_null($row->parent_sell_line_id) ? 'row_subtotal' : '';

                     return '<span class="'.$class.'" data-orig-value="'.$row->subtotal.'">'
                    .$this->transactionUtil->num_f($row->subtotal, true).'</span>';
                 })

                ->rawColumns(['current_stock', 'subtotal', 'total_qty_sold', 'category_name'])
                ->make(true);
        }
    }

    /**
     * Shows product stock details and allows to adjust mismatch
     *
     * @return \Illuminate\Http\Response
     */
    public function productStockDetails()
    {
        if (! auth()->user()->can('report.stock_details')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $variation_id = request()->get('variation_id', null);
        $location_id = request()->input('location_id');

        $location = null;
        $stock_details = [];

        if (! empty(request()->input('location_id'))) {
            $location = BusinessLocation::where('business_id', $business_id)
                                        ->where('id', $location_id)
                                        ->first();
            $stock_details = $this->productUtil->getVariationStockMisMatch($business_id, $variation_id, $location_id);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('report.product_stock_details')
            ->with(compact('stock_details', 'business_locations', 'location'));
    }

    /**
     * Adjusts stock availability mismatch if found
     *
     * @return \Illuminate\Http\Response
     */
    public function adjustProductStock()
    {
        if (! auth()->user()->can('report.stock_details')) {
            abort(403, 'Unauthorized action.');
        }

        if (! empty(request()->input('variation_id'))
            && ! empty(request()->input('location_id'))
            && request()->has('stock')) {
            $business_id = request()->session()->get('user.business_id');

            $this->productUtil->fixVariationStockMisMatch($business_id, request()->input('variation_id'), request()->input('location_id'), request()->input('stock'));
        }

        return redirect()->back()->with(['status' => [
            'success' => 1,
            'msg' => __('lang_v1.updated_succesfully'),
        ]]);
    }

    /**
     * Retrieves line orders/sales
     *
     * @return obj
     */
    public function serviceStaffLineOrders()
    {
        $business_id = request()->session()->get('user.business_id');

        $query = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                ->leftJoin('variations as v', 'transaction_sell_lines.variation_id', '=', 'v.id')
                ->leftJoin('products as p', 'v.product_id', '=', 'p.id')
                ->leftJoin('units as u', 'p.unit_id', '=', 'u.id')
                ->leftJoin('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->leftJoin('users as ss', 'ss.id', '=', 'transaction_sell_lines.res_service_staff_id')
                ->leftjoin(
                    'business_locations AS bl',
                    't.location_id',
                    '=',
                    'bl.id'
                )
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereNotNull('transaction_sell_lines.res_service_staff_id');

        if (! empty(request()->service_staff_id)) {
            $query->where('transaction_sell_lines.res_service_staff_id', request()->service_staff_id);
        }

        if (request()->has('location_id')) {
            $location_id = request()->get('location_id');
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
        }

        if (! empty(request()->start_date) && ! empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $query->whereDate('t.transaction_date', '>=', $start)
                        ->whereDate('t.transaction_date', '<=', $end);
        }

        $query->select(
            'p.name as product_name',
            'p.type as product_type',
            'v.name as variation_name',
            'pv.name as product_variation_name',
            'u.short_name as unit',
            't.id as transaction_id',
            'bl.name as business_location',
            't.transaction_date',
            't.invoice_no',
            'transaction_sell_lines.quantity',
            'transaction_sell_lines.unit_price_before_discount',
            'transaction_sell_lines.line_discount_type',
            'transaction_sell_lines.line_discount_amount',
            'transaction_sell_lines.item_tax',
            'transaction_sell_lines.unit_price_inc_tax',
            DB::raw('CONCAT(COALESCE(ss.first_name, ""), COALESCE(ss.last_name, "")) as service_staff')
        );

        $datatable = Datatables::of($query)
            ->editColumn('product_name', function ($row) {
                $name = $row->product_name;
                if ($row->product_type == 'variable') {
                    $name .= ' - '.$row->product_variation_name.' - '.$row->variation_name;
                }

                return $name;
            })
            ->editColumn(
                'unit_price_inc_tax',
                '<span class="display_currency unit_price_inc_tax" data-currency_symbol="true" data-orig-value="{{$unit_price_inc_tax}}">{{$unit_price_inc_tax}}</span>'
            )
            ->editColumn(
                'item_tax',
                '<span class="display_currency item_tax" data-currency_symbol="true" data-orig-value="{{$item_tax}}">{{$item_tax}}</span>'
            )
            ->editColumn(
                'quantity',
                '<span class="display_currency quantity" data-unit="{{$unit}}" data-currency_symbol="false" data-orig-value="{{$quantity}}">{{$quantity}}</span> {{$unit}}'
            )
            ->editColumn(
                'unit_price_before_discount',
                '<span class="display_currency unit_price_before_discount" data-currency_symbol="true" data-orig-value="{{$unit_price_before_discount}}">{{$unit_price_before_discount}}</span>'
            )
            ->addColumn(
                'total',
                '<span class="display_currency total" data-currency_symbol="true" data-orig-value="{{$unit_price_inc_tax * $quantity}}">{{$unit_price_inc_tax * $quantity}}</span>'
            )
            ->editColumn(
                'line_discount_amount',
                function ($row) {
                    $discount = ! empty($row->line_discount_amount) ? $row->line_discount_amount : 0;

                    if (! empty($discount) && $row->line_discount_type == 'percentage') {
                        $discount = $row->unit_price_before_discount * ($discount / 100);
                    }

                    return '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="'.$discount.'">'.$discount.'</span>';
                }
            )
            ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

            ->rawColumns(['line_discount_amount', 'unit_price_before_discount', 'item_tax', 'unit_price_inc_tax', 'item_tax', 'quantity', 'total'])
                  ->make(true);

        return $datatable;
    }

    /**
     * Lists profit by product, category, brand, location, invoice and date
     *
     * @return string $by = null
     */
    public function getProfit($by = null)
    {
        $business_id = request()->session()->get('user.business_id');

        $query = TransactionSellLine::join('transactions as sale', 'transaction_sell_lines.transaction_id', '=', 'sale.id')
            ->leftjoin('transaction_sell_lines_purchase_lines as TSPL', 'transaction_sell_lines.id', '=', 'TSPL.sell_line_id')
            ->leftjoin(
                'purchase_lines as PL',
                'TSPL.purchase_line_id',
                '=',
                'PL.id'
            )
            ->where('sale.type', 'sell')
            ->where('sale.status', 'final')
            ->join('products as P', 'transaction_sell_lines.product_id', '=', 'P.id')
            ->where('sale.business_id', $business_id)
            ->where('transaction_sell_lines.children_type', '!=', 'combo');
        //If type combo: find childrens, sale price parent - get PP of childrens
        $query->select(DB::raw('SUM(IF (TSPL.id IS NULL AND P.type="combo", ( 
            SELECT Sum((tspl2.quantity - tspl2.qty_returned) * (tsl.unit_price_inc_tax - pl2.purchase_price_inc_tax)) AS total
                FROM transaction_sell_lines AS tsl
                    JOIN transaction_sell_lines_purchase_lines AS tspl2
                ON tsl.id=tspl2.sell_line_id 
                JOIN purchase_lines AS pl2 
                ON tspl2.purchase_line_id = pl2.id 
                WHERE tsl.parent_sell_line_id = transaction_sell_lines.id), IF(P.enable_stock=0,(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax,   
                (TSPL.quantity - TSPL.qty_returned) * (transaction_sell_lines.unit_price_inc_tax - PL.purchase_price_inc_tax)) )) AS gross_profit')
            );

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('sale.location_id', $permitted_locations);
        }

        if (! empty(request()->location_id)) {
            $query->where('sale.location_id', request()->location_id);
        }

        if (! empty(request()->start_date) && ! empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $query->whereDate('sale.transaction_date', '>=', $start)
                        ->whereDate('sale.transaction_date', '<=', $end);
        }

        if ($by == 'product') {
            $query->join('variations as V', 'transaction_sell_lines.variation_id', '=', 'V.id')
                ->leftJoin('product_variations as PV', 'PV.id', '=', 'V.product_variation_id')
                ->addSelect(DB::raw("IF(P.type='variable', CONCAT(P.name, ' - ', PV.name, ' - ', V.name, ' (', V.sub_sku, ')'), CONCAT(P.name, ' (', P.sku, ')')) as product"))
                ->groupBy('V.id');
        }

        if ($by == 'category') {
            $query->join('variations as V', 'transaction_sell_lines.variation_id', '=', 'V.id')
                ->leftJoin('categories as C', 'C.id', '=', 'P.category_id')
                ->addSelect('C.name as category')
                ->groupBy('C.id');
        }

        if ($by == 'brand') {
            $query->join('variations as V', 'transaction_sell_lines.variation_id', '=', 'V.id')
                ->leftJoin('brands as B', 'B.id', '=', 'P.brand_id')
                ->addSelect('B.name as brand')
                ->groupBy('B.id');
        }

        if ($by == 'location') {
            $query->join('business_locations as L', 'sale.location_id', '=', 'L.id')
                ->addSelect('L.name as location')
                ->groupBy('L.id');
        }

        if ($by == 'invoice') {
            $query->addSelect(
                'sale.invoice_no',
                'sale.id as transaction_id',
                'sale.discount_type',
                'sale.discount_amount',
                'sale.total_before_tax'
            )
                ->groupBy('sale.invoice_no');
        }

        if ($by == 'date') {
            $query->addSelect('sale.transaction_date')
                ->groupBy(DB::raw('DATE(sale.transaction_date)'));
        }

        if ($by == 'day') {
            $results = $query->addSelect(DB::raw('DAYNAME(sale.transaction_date) as day'))
                ->groupBy(DB::raw('DAYOFWEEK(sale.transaction_date)'))
                ->get();

            $profits = [];
            foreach ($results as $result) {
                $profits[strtolower($result->day)] = $result->gross_profit;
            }
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            return view('report.partials.profit_by_day')->with(compact('profits', 'days'));
        }

        if ($by == 'customer') {
            $query->join('contacts as CU', 'sale.contact_id', '=', 'CU.id')
            ->addSelect('CU.name as customer', 'CU.supplier_business_name')
                ->groupBy('sale.contact_id');
        }

        if ($by == 'service_staff') {
            $query->join('users as U', function ($join) {
                $join->on(DB::raw("COALESCE(transaction_sell_lines.res_service_staff_id, sale.res_waiter_id)"), '=', 'U.id');
            })
            ->where('U.is_enable_service_staff_pin', 1)
            ->addSelect(
                "U.first_name as f_name",
                "U.last_name as l_name",
                "U.surname as surname"
            )
            ->groupBy('U.id');
        }        

        $datatable = Datatables::of($query);

        if (in_array($by, ['invoice'])) {
            $datatable->editColumn('gross_profit', function ($row) {
                $discount = $row->discount_amount;
                if ($row->discount_type == 'percentage') {
                    $discount = ($row->discount_amount * $row->total_before_tax) / 100;
                }

                $profit = $row->gross_profit - $discount;
                $html = '<span class="gross-profit" data-orig-value="'.$profit.'" >'.$this->transactionUtil->num_f($profit, true).'</span>';

                return $html;
            });
        } else {
            $datatable->editColumn(
                'gross_profit',
                function ($row) {
                    return '<span class="gross-profit" data-orig-value="'.$row->gross_profit.'">'.$this->transactionUtil->num_f($row->gross_profit, true).'</span>';
                });
        }

        if ($by == 'category') {
            $datatable->editColumn(
                'category',
                '{{$category ?? __("lang_v1.uncategorized")}}'
            );
        }
        if ($by == 'brand') {
            $datatable->editColumn(
                'brand',
                '{{$brand ?? __("report.others")}}'
            );
        }

        if ($by == 'date') {
            $datatable->editColumn('transaction_date', '{{@format_date($transaction_date)}}');
        }

        if ($by == 'product') {
            $datatable->filterColumn(
                 'product',
                 function ($query, $keyword) {
                     $query->whereRaw("IF(P.type='variable', CONCAT(P.name, ' - ', PV.name, ' - ', V.name, ' (', V.sub_sku, ')'), CONCAT(P.name, ' (', P.sku, ')')) LIKE '%{$keyword}%'");
                 });
        }
        $raw_columns = ['gross_profit'];

        if ($by == 'customer') {
            $datatable->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$customer}}');
            $raw_columns[] = 'customer';
        }

        if($by == 'service_staff'){
            $datatable->editColumn('staff_name', '{{$surname}} {{$f_name}} {{$l_name}}');
            $raw_columns[] = 'staff_name';
        }

        if ($by == 'invoice') {
            $datatable->editColumn('invoice_no', function ($row) {
                return '<a data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->invoice_no.'</a>';
            });
            $raw_columns[] = 'invoice_no';
        }

        return $datatable->rawColumns($raw_columns)
                  ->make(true);
    }

    /**
     * Shows items report from sell purchase mapping table
     *
     * @return \Illuminate\Http\Response
     */
    public function itemsReport()
    {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $query = TransactionSellLinesPurchaseLines::leftJoin('transaction_sell_lines 
                    as SL', 'SL.id', '=', 'transaction_sell_lines_purchase_lines.sell_line_id')
                ->leftJoin('stock_adjustment_lines 
                    as SAL', 'SAL.id', '=', 'transaction_sell_lines_purchase_lines.stock_adjustment_line_id')
                ->leftJoin('transactions as sale', 'SL.transaction_id', '=', 'sale.id')
                ->leftJoin('transactions as stock_adjustment', 'SAL.transaction_id', '=', 'stock_adjustment.id')
                ->join('purchase_lines as PL', 'PL.id', '=', 'transaction_sell_lines_purchase_lines.purchase_line_id')
                ->join('transactions as purchase', 'PL.transaction_id', '=', 'purchase.id')
                ->join('business_locations as bl', 'purchase.location_id', '=', 'bl.id')
                ->join(
                    'variations as v',
                    'PL.variation_id',
                    '=',
                    'v.id'
                    )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('products as p', 'PL.product_id', '=', 'p.id')
                ->join('units as u', 'p.unit_id', '=', 'u.id')
                ->leftJoin('contacts as suppliers', 'purchase.contact_id', '=', 'suppliers.id')
                ->leftJoin('contacts as customers', 'sale.contact_id', '=', 'customers.id')
                ->where('purchase.business_id', $business_id)
                ->select(
                    'v.sub_sku as sku',
                    'p.type as product_type',
                    'p.name as product_name',
                    'v.name as variation_name',
                    'pv.name as product_variation',
                    'u.short_name as unit',
                    'purchase.transaction_date as purchase_date',
                    'purchase.ref_no as purchase_ref_no',
                    'purchase.type as purchase_type',
                    'purchase.id as purchase_id',
                    'suppliers.name as supplier',
                    'suppliers.supplier_business_name',
                    'PL.purchase_price_inc_tax as purchase_price',
                    'sale.transaction_date as sell_date',
                    'stock_adjustment.transaction_date as stock_adjustment_date',
                    'sale.invoice_no as sale_invoice_no',
                    'stock_adjustment.ref_no as stock_adjustment_ref_no',
                    'customers.name as customer',
                    'customers.supplier_business_name as customer_business_name',
                    'transaction_sell_lines_purchase_lines.quantity as quantity',
                    'SL.unit_price_inc_tax as selling_price',
                    'SAL.unit_price as stock_adjustment_price',
                    'transaction_sell_lines_purchase_lines.stock_adjustment_line_id',
                    'transaction_sell_lines_purchase_lines.sell_line_id',
                    'transaction_sell_lines_purchase_lines.purchase_line_id',
                    'transaction_sell_lines_purchase_lines.qty_returned',
                    'bl.name as location',
                    'SL.sell_line_note',
                    'PL.lot_number'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('purchase.location_id', $permitted_locations);
            }

            if (! empty(request()->purchase_start) && ! empty(request()->purchase_end)) {
                $start = request()->purchase_start;
                $end = request()->purchase_end;
                $query->whereDate('purchase.transaction_date', '>=', $start)
                            ->whereDate('purchase.transaction_date', '<=', $end);
            }
            if (! empty(request()->sale_start) && ! empty(request()->sale_end)) {
                $start = request()->sale_start;
                $end = request()->sale_end;
                $query->where(function ($q) use ($start, $end) {
                    $q->where(function ($qr) use ($start, $end) {
                        $qr->whereDate('sale.transaction_date', '>=', $start)
                           ->whereDate('sale.transaction_date', '<=', $end);
                    })->orWhere(function ($qr) use ($start, $end) {
                        $qr->whereDate('stock_adjustment.transaction_date', '>=', $start)
                           ->whereDate('stock_adjustment.transaction_date', '<=', $end);
                    });
                });
            }

            $supplier_id = request()->get('supplier_id', null);
            if (! empty($supplier_id)) {
                $query->where('suppliers.id', $supplier_id);
            }

            $customer_id = request()->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('customers.id', $customer_id);
            }

            $location_id = request()->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('purchase.location_id', $location_id);
            }

            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (! empty($only_mfg_products)) {
                $query->where('purchase.type', 'production_purchase');
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $product_name;
                })
                ->editColumn('purchase_date', '{{@format_datetime($purchase_date)}}')
                ->editColumn('purchase_ref_no', function ($row) {
                    $html = $row->purchase_type == 'purchase' ? '<a data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->purchase_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->purchase_ref_no.'</a>' : $row->purchase_ref_no;
                    if ($row->purchase_type == 'opening_stock') {
                        $html .= '('.__('lang_v1.opening_stock').')';
                    }

                    return $html;
                })
                ->editColumn('purchase_price', function ($row) {
                    return '<span 
                    class="purchase_price" data-orig-value="'.$row->purchase_price.'">'.
                    $this->transactionUtil->num_f($row->purchase_price, true).'</span>';
                })
                ->editColumn('sell_date', '@if(!empty($sell_line_id)) {{@format_datetime($sell_date)}} @else {{@format_datetime($stock_adjustment_date)}} @endif')

                ->editColumn('sale_invoice_no', function ($row) {
                    $invoice_no = ! empty($row->sell_line_id) ? $row->sale_invoice_no : $row->stock_adjustment_ref_no.'<br><small>('.__('stock_adjustment.stock_adjustment').'</small)>';

                    return $invoice_no;
                })
                ->editColumn('quantity', function ($row) {
                    $html = '<span data-is_quantity="true" class="display_currency quantity" data-currency_symbol=false data-orig-value="'.(float) $row->quantity.'" data-unit="'.$row->unit.'" >'.(float) $row->quantity.'</span> '.$row->unit;

                    if (empty($row->sell_line_id)) {
                        $html .= '<br><small>('.__('stock_adjustment.stock_adjustment').'</small)>';
                    }
                    if ($row->qty_returned > 0) {
                        $html .= '<small><i>(<span data-is_quantity="true" class="display_currency" data-currency_symbol=false>'.(float) $row->quantity.'</span> '.$row->unit.' '.__('lang_v1.returned').')</i></small>';
                    }

                    return $html;
                })
                 ->editColumn('selling_price', function ($row) {
                     $selling_price = ! empty($row->sell_line_id) ? $row->selling_price : $row->stock_adjustment_price;

                     return '<span class="row_selling_price" data-orig-value="'.$selling_price.
                      '">'.$this->transactionUtil->num_f($selling_price, true).'</span>';
                 })

                 ->addColumn('subtotal', function ($row) {
                     $selling_price = ! empty($row->sell_line_id) ? $row->selling_price : $row->stock_adjustment_price;
                     $subtotal = $selling_price * $row->quantity;

                     return '<span class="row_subtotal" data-orig-value="'.$subtotal.'">'.
                     $this->transactionUtil->num_f($subtotal, true).'</span>';
                 })
                 ->editColumn('supplier', '@if(!empty($supplier_business_name))
                 {{$supplier_business_name}},<br> @endif {{$supplier}}')
                 ->editColumn('customer', '@if(!empty($customer_business_name))
                 {{$customer_business_name}},<br> @endif {{$customer}}')
                ->filterColumn('sale_invoice_no', function ($query, $keyword) {
                    $query->where('sale.invoice_no', 'like', ["%{$keyword}%"])
                          ->orWhere('stock_adjustment.ref_no', 'like', ["%{$keyword}%"]);
                })

                ->rawColumns(['subtotal', 'selling_price', 'quantity', 'purchase_price', 'sale_invoice_no', 'purchase_ref_no', 'supplier', 'customer'])
                ->make(true);
        }

        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('report.items_report')->with(compact('suppliers', 'customers', 'business_locations'));
    }

    /**
     * Shows purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function purchaseReport()
    {
        if ((! auth()->user()->can('purchase.view') && ! auth()->user()->can('purchase.create') && ! auth()->user()->can('view_own_purchase')) || empty(config('constants.show_report_606'))) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
            $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS BS',
                        'transactions.location_id',
                        '=',
                        'BS.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->with(['payment_lines'])
                    ->select(
                        'transactions.id',
                        'transactions.ref_no',
                        'contacts.name',
                        'contacts.contact_id',
                        'final_total',
                        'total_before_tax',
                        'discount_amount',
                        'discount_type',
                        'tax_amount',
                        DB::raw('DATE_FORMAT(transaction_date, "%Y/%m") as purchase_year_month'),
                        DB::raw('DATE_FORMAT(transaction_date, "%d") as purchase_day')
                    )
                    ->groupBy('transactions.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->supplier_id)) {
                $purchases->where('contacts.id', request()->supplier_id);
            }
            if (! empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }
            if (! empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $purchases->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $purchases->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }

            if (! empty(request()->status)) {
                $purchases->where('transactions.status', request()->status);
            }

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (! auth()->user()->can('purchase.view') && auth()->user()->can('view_own_purchase')) {
                $purchases->where('transactions.created_by', request()->session()->get('user.id'));
            }

            return Datatables::of($purchases)
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn(
                    'total_before_tax',
                    '<span class="display_currency total_before_tax" data-currency_symbol="true" data-orig-value="{{$total_before_tax}}">{{$total_before_tax}}</span>'
                )
                ->editColumn(
                    'tax_amount',
                    '<span class="display_currency tax_amount" data-currency_symbol="true" data-orig-value="{{$tax_amount}}">{{$tax_amount}}</span>'
                )
                ->editColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = ! empty($row->discount_amount) ? $row->discount_amount : 0;

                        if (! empty($discount) && $row->discount_type == 'percentage') {
                            $discount = $row->total_before_tax * ($discount / 100);
                        }

                        return '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="'.$discount.'">'.$discount.'</span>';
                    }
                )
                ->addColumn('payment_year_month', function ($row) {
                    $year_month = '';
                    if (! empty($row->payment_lines->first())) {
                        $year_month = \Carbon::parse($row->payment_lines->first()->paid_on)->format('Y/m');
                    }

                    return $year_month;
                })
                ->addColumn('payment_day', function ($row) {
                    $payment_day = '';
                    if (! empty($row->payment_lines->first())) {
                        $payment_day = \Carbon::parse($row->payment_lines->first()->paid_on)->format('d');
                    }

                    return $payment_day;
                })
                ->addColumn('payment_method', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = $payment_types[$methods[0]];
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = ! empty($payment_method) ? '<span class="payment-method" data-orig-value="'.$payment_method.'" data-status-name="'.$payment_method.'">'.$payment_method.'</span>' : '';

                    return $html;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('purchase.view')) {
                            return  action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    }, ])
                ->rawColumns(['final_total', 'total_before_tax', 'tax_amount', 'discount_amount', 'payment_method'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $orderStatuses = $this->productUtil->orderStatuses();

        return view('report.purchase_report')
            ->with(compact('business_locations', 'suppliers', 'orderStatuses'));
    }

    /**
     * Shows sale report
     *
     * @return \Illuminate\Http\Response
     */
    public function saleReport()
    {
        if ((! auth()->user()->can('sell.view') && ! auth()->user()->can('sell.create') && ! auth()->user()->can('direct_sell.access') && ! auth()->user()->can('view_own_sell_only')) || empty(config('constants.show_report_607'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        return view('report.sale_report')
            ->with(compact('business_locations', 'customers'));
    }

    /**
     * Calculates stock values
     *
     * @return array
     */
    public function getStockValue()
    {
        $business_id = request()->session()->get('user.business_id');
        $end_date = \Carbon::now()->format('Y-m-d');
        $location_id = request()->input('location_id');
        $filters = request()->only(['category_id', 'sub_category_id', 'brand_id', 'unit_id']);

        $permitted_locations = auth()->user()->permitted_locations();
        //Get Closing stock
        $closing_stock_by_pp = $this->transactionUtil->getOpeningClosingStock(
            $business_id,
            $end_date,
            $location_id,
            false,
            false,
            $filters,
            $permitted_locations
        );
        $closing_stock_by_sp = $this->transactionUtil->getOpeningClosingStock(
            $business_id,
            $end_date,
            $location_id,
            false,
            true,
            $filters,
            $permitted_locations
        );
        $potential_profit = $closing_stock_by_sp - $closing_stock_by_pp;
        $profit_margin = empty($closing_stock_by_sp) ? 0 : ($potential_profit / $closing_stock_by_sp) * 100;

        return [
            'closing_stock_by_pp' => $closing_stock_by_pp,
            'closing_stock_by_sp' => $closing_stock_by_sp,
            'potential_profit' => $potential_profit,
            'profit_margin' => $profit_margin,
        ];
    }

    public function activityLog()
    {
        $business_id = request()->session()->get('user.business_id');
        $transaction_types = [
            'contact' => __('report.contact'),
            'user' => __('report.user'),
            'sell' => __('sale.sale'),
            'purchase' => __('lang_v1.purchase'),
            'sales_order' => __('lang_v1.sales_order'),
            'purchase_order' => __('lang_v1.purchase_order'),
            'sell_return' => __('lang_v1.sell_return'),
            'purchase_return' => __('lang_v1.purchase_return'),
            'sell_transfer' => __('lang_v1.stock_transfer'),
            'stock_adjustment' => __('stock_adjustment.stock_adjustment'),
            'expense' => __('lang_v1.expense'),
        ];

        if (request()->ajax()) {
            $activities = Activity::with(['subject'])
                                ->leftjoin('users as u', 'u.id', '=', 'activity_log.causer_id')
                                ->where('activity_log.business_id', $business_id)
                                ->select(
                                    'activity_log.*',
                                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as created_by")
                                );

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $activities->whereDate('activity_log.created_at', '>=', $start)
                            ->whereDate('activity_log.created_at', '<=', $end);
            }

            if (! empty(request()->user_id)) {
                $activities->where('causer_id', request()->user_id);
            }

            $subject_type = request()->subject_type;
            if (! empty($subject_type)) {
                if ($subject_type == 'contact') {
                    $activities->where('subject_type', \App\Contact::class);
                } elseif ($subject_type == 'user') {
                    $activities->where('subject_type', \App\User::class);
                } elseif (in_array($subject_type, ['sell', 'purchase',
                    'sales_order', 'purchase_order', 'sell_return', 'purchase_return', 'sell_transfer', 'expense', 'purchase_order', ])) {
                    $activities->where('subject_type', \App\Transaction::class);
                    $activities->whereHasMorph('subject', Transaction::class, function ($q) use ($subject_type) {
                        $q->where('type', $subject_type);
                    });
                }
            }

            $sell_statuses = Transaction::sell_statuses();
            $sales_order_statuses = Transaction::sales_order_statuses(true);
            $purchase_statuses = $this->transactionUtil->orderStatuses();
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $statuses = array_merge($sell_statuses, $sales_order_statuses, $purchase_statuses);

            return Datatables::of($activities)
                            ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                            ->addColumn('subject_type', function ($row) use ($transaction_types) {
                                $subject_type = '';
                                if ($row->subject_type == \App\Contact::class) {
                                    $subject_type = __('contact.contact');
                                } elseif ($row->subject_type == \App\User::class) {
                                    $subject_type = __('report.user');
                                } elseif ($row->subject_type == \App\Transaction::class && ! empty($row->subject->type)) {
                                    $subject_type = isset($transaction_types[$row->subject->type]) ? $transaction_types[$row->subject->type] : '';
                                } elseif (($row->subject_type == \App\TransactionPayment::class)) {
                                    $subject_type = __('lang_v1.payment');
                                }

                                return $subject_type;
                            })
                            ->addColumn('note', function ($row) use ($statuses, $shipping_statuses) {
                                $html = '';
                                if (! empty($row->subject->ref_no)) {
                                    $html .= __('purchase.ref_no').': '.$row->subject->ref_no.'<br>';
                                }
                                if (! empty($row->subject->invoice_no)) {
                                    $html .= __('sale.invoice_no').': '.$row->subject->invoice_no.'<br>';
                                }
                                if ($row->subject_type == \App\Transaction::class && ! empty($row->subject) && in_array($row->subject->type, ['sell', 'purchase'])) {
                                    $html .= view('sale_pos.partials.activity_row', ['activity' => $row, 'statuses' => $statuses, 'shipping_statuses' => $shipping_statuses])->render();
                                } else {
                                    $update_note = $row->getExtraProperty('update_note');
                                    if (! empty($update_note) && ! is_array($update_note)) {
                                        $html .= $update_note;
                                    }
                                }

                                if ($row->description == 'contact_deleted') {
                                    $html .= $row->getExtraProperty('supplier_business_name') ?? '';
                                    $html .= '<br>';
                                }

                                if (! empty($row->getExtraProperty('name'))) {
                                    $html .= __('user.name').': '.$row->getExtraProperty('name').'<br>';
                                }

                                if (! empty($row->getExtraProperty('id'))) {
                                    $html .= 'id: '.$row->getExtraProperty('id').'<br>';
                                }
                                if (! empty($row->getExtraProperty('invoice_no'))) {
                                    $html .= __('sale.invoice_no').': '.$row->getExtraProperty('invoice_no');
                                }

                                if (! empty($row->getExtraProperty('ref_no'))) {
                                    $html .= __('purchase.ref_no').': '.$row->getExtraProperty('ref_no');
                                }

                                return $html;
                            })
                            ->filterColumn('created_by', function ($query, $keyword) {
                                $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                            })
                            ->editColumn('description', function ($row) {
                                return __('lang_v1.'.$row->description);
                            })
                            ->rawColumns(['note'])
                            ->make(true);
        }

        $users = User::allUsersDropdown($business_id, false);

        return view('report.activity_log')->with(compact('users', 'transaction_types'));
    }

    public function gstSalesReport(Request $request)
    {
        if (! auth()->user()->can('tax_report.view') || empty(config('constants.enable_gst_report_india'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                        ->where('is_tax_group', 0)
                        ->select(['id', 'name', 'amount'])
                        ->get()
                        ->toArray();

        if ($request->ajax()) {
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'transaction_sell_lines.product_id', '=', 'p.id')
                ->leftjoin('categories as cat', 'p.category_id', '=', 'cat.id')
                ->leftjoin('tax_rates as tr', 'transaction_sell_lines.tax_id', '=', 'tr.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereNull('parent_sell_line_id')
                ->select(
                    'c.name as customer',
                    'c.supplier_business_name',
                    'c.contact_id',
                    'c.tax_number',
                    'cat.short_code',
                    't.id as transaction_id',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    'transaction_sell_lines.unit_price_before_discount as unit_price',
                    'transaction_sell_lines.unit_price as unit_price_after_discount',
                    DB::raw('(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as sell_qty'),
                    'transaction_sell_lines.line_discount_type as discount_type',
                    'transaction_sell_lines.line_discount_amount as discount_amount',
                    'transaction_sell_lines.item_tax',
                    'tr.amount as tax_percent',
                    'tr.is_tax_group',
                    'transaction_sell_lines.tax_id',
                    'u.short_name as unit',
                    'transaction_sell_lines.parent_sell_line_id',
                    DB::raw('((transaction_sell_lines.quantity- transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as line_total'),
                )
                ->groupBy('transaction_sell_lines.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereDate('t.transaction_date', '>=', $start_date)
                    ->whereDate('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            $datatable = Datatables::of($query);

            $raw_cols = ['invoice_no', 'taxable_value', 'discount_amount', 'unit_price', 'tax', 'customer', 'line_total'];
            $group_taxes_array = TaxRate::groupTaxes($business_id);
            $group_taxes = [];
            foreach ($group_taxes_array as $group_tax) {
                foreach ($group_tax['sub_taxes'] as $sub_tax) {
                    $group_taxes[$group_tax->id]['sub_taxes'][$sub_tax->id] = $sub_tax;
                }
            }
            foreach ($taxes as $tax) {
                $col = 'tax_'.$tax['id'];
                $raw_cols[] = $col;
                $datatable->addColumn($col, function ($row) use ($tax, $col, $group_taxes) {
                    $sub_tax_share = 0;
                    if ($row->is_tax_group == 1 && array_key_exists($tax['id'], $group_taxes[$row->tax_id]['sub_taxes'])) {
                        $sub_tax_share = $this->transactionUtil->calc_percentage($row->unit_price_after_discount, $group_taxes[$row->tax_id]['sub_taxes'][$tax['id']]->amount) * $row->sell_qty;
                    }

                    if ($sub_tax_share > 0) {
                        //ignore child sell line of combo product
                        $class = is_null($row->parent_sell_line_id) ? $col : '';

                        return '<span class="'.$class.'" data-orig-value="'.$sub_tax_share.'">'.$this->transactionUtil->num_f($sub_tax_share).'</span>';
                    } else {
                        return '';
                    }
                });
            }

            return $datatable->addColumn('taxable_value', function ($row) {
                $taxable_value = $row->unit_price_after_discount * $row->sell_qty;
                //ignore child sell line of combo product
                $class = is_null($row->parent_sell_line_id) ? 'taxable_value' : '';

                return '<span class="'.$class.'"data-orig-value="'.$taxable_value.'">'.$this->transactionUtil->num_f($taxable_value).'</span>';
            })
                 ->editColumn('invoice_no', function ($row) {
                     return '<a data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->invoice_no.'</a>';
                 })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('sell_qty', function ($row) {
                    return $this->transactionUtil->num_f($row->sell_qty, false, null, true).' '.$row->unit;
                })
                ->editColumn('unit_price', function ($row) {
                    return '<span data-orig-value="'.$row->unit_price.'">'.$this->transactionUtil->num_f($row->unit_price).'</span>';
                })
                ->editColumn('line_total', function ($row) {
                    return '<span data-orig-value="'.$row->line_total.'">'.$this->transactionUtil->num_f($row->line_total).'</span>';
                })
                ->editColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = ! empty($row->discount_amount) ? $row->discount_amount : 0;

                        if (! empty($discount) && $row->discount_type == 'percentage') {
                            $discount = $row->unit_price * ($discount / 100);
                        }

                        return $this->transactionUtil->num_f($discount);
                    }
                )
                ->editColumn('tax_percent', '@if(!empty($tax_percent)){{@num_format($tax_percent)}}% @endif
                    ')
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$customer}}')
                ->rawColumns($raw_cols)
                ->make(true);
        }

        $customers = Contact::customersDropdown($business_id);

        return view('report.gst_sales_report')->with(compact('customers', 'taxes'));
    }

    public function gstPurchaseReport(Request $request)
    {
        if (! auth()->user()->can('tax_report.view') || empty(config('constants.enable_gst_report_india'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                        ->where('is_tax_group', 0)
                        ->select(['id', 'name', 'amount'])
                        ->get()
                        ->toArray();

        if ($request->ajax()) {
            $query = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'purchase_lines.product_id', '=', 'p.id')
                ->leftjoin('categories as cat', 'p.category_id', '=', 'cat.id')
                ->leftjoin('tax_rates as tr', 'purchase_lines.tax_id', '=', 'tr.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'purchase')
                ->where('t.status', 'received')
                ->select(
                    'c.name as supplier',
                    'c.supplier_business_name',
                    'c.contact_id',
                    'c.tax_number',
                    'cat.short_code',
                    't.id as transaction_id',
                    't.ref_no',
                    't.transaction_date as transaction_date',
                    'purchase_lines.pp_without_discount as unit_price',
                    'purchase_lines.purchase_price as unit_price_after_discount',
                    DB::raw('(purchase_lines.quantity - purchase_lines.quantity_returned) as purchase_qty'),
                    'purchase_lines.discount_percent',
                    'purchase_lines.item_tax',
                    'tr.amount as tax_percent',
                    'tr.is_tax_group',
                    'purchase_lines.tax_id',
                    'u.short_name as unit',
                    DB::raw('((purchase_lines.quantity- purchase_lines.quantity_returned) * purchase_lines.purchase_price_inc_tax) as line_total')
                )
                ->groupBy('purchase_lines.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $supplier_id = $request->get('supplier_id', null);
            if (! empty($supplier_id)) {
                $query->where('t.contact_id', $supplier_id);
            }

            $datatable = Datatables::of($query);

            $raw_cols = ['ref_no', 'taxable_value', 'discount_amount', 'unit_price', 'tax', 'supplier', 'line_total'];
            $group_taxes_array = TaxRate::groupTaxes($business_id);
            $group_taxes = [];
            foreach ($group_taxes_array as $group_tax) {
                foreach ($group_tax['sub_taxes'] as $sub_tax) {
                    $group_taxes[$group_tax->id]['sub_taxes'][$sub_tax->id] = $sub_tax;
                }
            }
            foreach ($taxes as $tax) {
                $col = 'tax_'.$tax['id'];
                $raw_cols[] = $col;
                $datatable->addColumn($col, function ($row) use ($tax, $group_taxes) {
                    $sub_tax_share = 0;
                    if ($row->is_tax_group == 1 && array_key_exists($tax['id'], $group_taxes[$row->tax_id]['sub_taxes'])) {
                        $sub_tax_share = $this->transactionUtil->calc_percentage($row->unit_price_after_discount, $group_taxes[$row->tax_id]['sub_taxes'][$tax['id']]->amount) * $row->purchase_qty;
                    }

                    if ($sub_tax_share > 0) {
                        return '<span data-orig-value="'.$sub_tax_share.'">'.$this->transactionUtil->num_f($sub_tax_share).'</span>';
                    } else {
                        return '';
                    }
                });
            }

            return $datatable->addColumn('taxable_value', function ($row) {
                $taxable_value = $row->unit_price_after_discount * $row->purchase_qty;

                return '<span class="taxable_value"data-orig-value="'.$taxable_value.'">'.$this->transactionUtil->num_f($taxable_value).'</span>';
            })
                 ->editColumn('ref_no', function ($row) {
                     return '<a data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->ref_no.'</a>';
                 })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('purchase_qty', function ($row) {
                    return $this->transactionUtil->num_f($row->purchase_qty, false, null, true).' '.$row->unit;
                })
                ->editColumn('unit_price', function ($row) {
                    return '<span data-orig-value="'.$row->unit_price.'">'.$this->transactionUtil->num_f($row->unit_price).'</span>';
                })
                ->editColumn('line_total', function ($row) {
                    return '<span data-orig-value="'.$row->line_total.'">'.$this->transactionUtil->num_f($row->line_total).'</span>';
                })
                ->addColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = ! empty($row->discount_percent) ? $row->discount_percent : 0;

                        if (! empty($discount)) {
                            $discount = $row->unit_price * ($discount / 100);
                        }

                        return $this->transactionUtil->num_f($discount);
                    }
                )
                ->editColumn('tax_percent', '@if(!empty($tax_percent)){{@num_format($tax_percent)}}% @endif
                    ')
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$supplier}}')
                ->rawColumns($raw_cols)
                ->make(true);
        }

        $suppliers = Contact::suppliersDropdown($business_id);

        return view('report.gst_purchase_report')->with(compact('suppliers', 'taxes'));
    }
    /**
     * Shows low stock velocity report with sales analysis
     *
     * @return \Illuminate\Http\Response
     */
    public function getLowStockVelocityReport(Request $request)
    {
        if (!auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $now = \Carbon::now();
            
            // Build query for products with stock and sales velocity
            $query = VariationLocationDetails::join('variations as v', 'variation_location_details.variation_id', '=', 'v.id')
                ->join('products as p', 'v.product_id', '=', 'p.id')
                ->join('business_locations as bl', 'variation_location_details.location_id', '=', 'bl.id')
                ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
                ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->where('p.business_id', $business_id)
                ->where('p.is_inactive', 0)
                ->select([
                    'p.id as product_id',
                    'p.name as product_name',
                    'p.sku',
                    'p.alert_quantity',
                    'v.id as variation_id',
                    'v.name as variation_name',
                    'v.sub_sku',
                    'variation_location_details.qty_available as current_stock',
                    'variation_location_details.location_id',
                    'bl.name as location_name',
                    'c.name as category_name',
                    'b.name as brand_name',
                ]);

            // Location filter
            $location_id = $request->get('location_id');
            if (!empty($location_id)) {
                $query->where('variation_location_details.location_id', $location_id);
            }

            // Category filter
            $category_id = $request->get('category_id');
            if (!empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            // Brand filter
            $brand_id = $request->get('brand_id');
            if (!empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            // Only low stock filter
            $only_low_stock = $request->get('only_low_stock');
            if (!empty($only_low_stock)) {
                $query->whereNotNull('p.alert_quantity')
                      ->whereRaw('variation_location_details.qty_available <= p.alert_quantity');
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('variation_location_details.location_id', $permitted_locations);
            }

            // Get the data
            $products = $query->get();

            // Calculate sales velocity for each product
            $result = [];
            foreach ($products as $product) {
                // Get sales for different periods
                $sold_1d = $this->getSoldQuantity($product->variation_id, $product->location_id, 1);
                $sold_7d = $this->getSoldQuantity($product->variation_id, $product->location_id, 7);
                $sold_30d = $this->getSoldQuantity($product->variation_id, $product->location_id, 30);
                $sold_90d = $this->getSoldQuantity($product->variation_id, $product->location_id, 90);
                $sold_180d = $this->getSoldQuantity($product->variation_id, $product->location_id, 180);
                $sold_270d = $this->getSoldQuantity($product->variation_id, $product->location_id, 270);
                $sold_365d = $this->getSoldQuantity($product->variation_id, $product->location_id, 365);

                // Calculate daily average (based on 30 days)
                $daily_avg = $sold_30d / 30;
                
                // Calculate days until stockout
                $days_until_stockout = $daily_avg > 0 ? round($product->current_stock / $daily_avg) : null;

                $result[] = [
                    'product_name' => $product->product_name,
                    'variation_name' => $product->variation_name,
                    'sku' => $product->sku ?: $product->sub_sku,
                    'location_name' => $product->location_name,
                    'category_name' => $product->category_name ?? '-',
                    'brand_name' => $product->brand_name ?? '-',
                    'current_stock' => $product->current_stock,
                    'alert_quantity' => $product->alert_quantity ?? '-',
                    'sold_1d' => $sold_1d,
                    'sold_7d' => $sold_7d,
                    'sold_30d' => $sold_30d,
                    'sold_90d' => $sold_90d,
                    'sold_180d' => $sold_180d,
                    'sold_270d' => $sold_270d,
                    'sold_365d' => $sold_365d,
                    'daily_avg' => round($daily_avg, 2),
                    'days_until_stockout' => $days_until_stockout,
                    'status' => $this->getStockStatus($product->current_stock, $product->alert_quantity, $days_until_stockout),
                ];
            }

            // Calculate summary counts
            $summary = [
                'out_of_stock' => collect($result)->where('status', 'Out of Stock')->count(),
                'critical' => collect($result)->where('status', 'Critical')->count(),
                'low' => collect($result)->where('status', 'Low')->count(),
                'ok' => collect($result)->where('status', 'OK')->count(),
            ];

            // Filter by stock status if requested
            $stock_status = $request->get('stock_status');
            if (!empty($stock_status)) {
                $statusMap = [
                    'out_of_stock' => 'Out of Stock',
                    'critical' => 'Critical',
                    'low' => 'Low',
                    'ok' => 'OK',
                ];
                if (isset($statusMap[$stock_status])) {
                    $result = collect($result)->where('status', $statusMap[$stock_status])->values()->all();
                }
            }

            return Datatables::of(collect($result))
                ->addColumn('status_raw', function ($row) {
                    return $row['status'];
                })
                ->editColumn('current_stock', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="false">' . number_format($row['current_stock'], 2) . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $badge = 'success';
                    if ($row['status'] == 'Out of Stock') {
                        $badge = 'danger';
                    } elseif ($row['status'] == 'Critical') {
                        $badge = 'danger';
                    } elseif ($row['status'] == 'Low') {
                        $badge = 'warning';
                    }
                    return '<span class="badge badge-' . $badge . '">' . $row['status'] . '</span>';
                })
                ->editColumn('days_until_stockout', function ($row) {
                    if ($row['days_until_stockout'] === null) {
                        return '<span class="text-muted">N/A</span>';
                    }
                    $class = $row['days_until_stockout'] <= 7 ? 'text-danger font-weight-bold' : 
                             ($row['days_until_stockout'] <= 30 ? 'text-warning' : 'text-success');
                    return '<span class="' . $class . '">' . $row['days_until_stockout'] . ' days</span>';
                })
                ->rawColumns(['current_stock', 'status', 'days_until_stockout'])
                ->with('summary', $summary)
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);

        return view('report.low_stock_velocity_report')->with(compact('business_locations', 'categories', 'brands'));
    }

    /**
     * Get sold quantity for a variation in a location for given days
     */
    private function getSoldQuantity($variation_id, $location_id, $days)
    {
        $start_date = \Carbon::now()->subDays($days)->format('Y-m-d');
        
        return TransactionSellLine::join('transactions as t', 'transaction_sell_lines.transaction_id', '=', 't.id')
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->where('t.location_id', $location_id)
            ->where('transaction_sell_lines.variation_id', $variation_id)
            ->whereDate('t.transaction_date', '>=', $start_date)
            ->sum(DB::raw('transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned'));
    }

    /**
     * Get stock status based on current stock and alert quantity
     */
    private function getStockStatus($current_stock, $alert_quantity, $days_until_stockout)
    {
        if ($current_stock <= 0) {
            return 'Out of Stock';
        }
        
        if ($alert_quantity && $current_stock <= $alert_quantity) {
            return 'Critical';
        }
        
        if ($days_until_stockout !== null && $days_until_stockout <= 7) {
            return 'Critical';
        }
        
        if ($days_until_stockout !== null && $days_until_stockout <= 30) {
            return 'Low';
        }
        

        return 'OK';
    }

    public function getDailyProductProfitReport(Request $request)
    {
        if (!auth()->user()->can('report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('variations', 'transaction_sell_lines.variation_id', '=', 'variations.id')
                ->join('products', 'variations.product_id', '=', 'products.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('purchase_lines', function($join) {
                    $join->on('transaction_sell_lines.variation_id', '=', 'purchase_lines.variation_id')
                        ->on('transaction_sell_lines.lot_no_line_id', '=', 'purchase_lines.id');
                })
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->select([
                    'transactions.transaction_date',
                    'products.sku',
                    'products.name as product_name',
                    'transaction_sell_lines.quantity',
                    DB::raw('COALESCE(purchase_lines.purchase_price_inc_tax, variations.default_purchase_price, 0) as unit_purchase_price'),
                    'transaction_sell_lines.unit_price_inc_tax as unit_sale_price',
                    DB::raw('(transaction_sell_lines.unit_price_inc_tax - COALESCE(purchase_lines.purchase_price_inc_tax, variations.default_purchase_price, 0)) as unit_profit'),
                    DB::raw('((transaction_sell_lines.unit_price_inc_tax - COALESCE(purchase_lines.purchase_price_inc_tax, variations.default_purchase_price, 0)) * transaction_sell_lines.quantity) as total_profit'),
                    DB::raw('(transaction_sell_lines.unit_price_inc_tax * transaction_sell_lines.quantity) as total_amount'),
                    DB::raw('CASE WHEN transaction_sell_lines.unit_price_inc_tax > 0 THEN ((transaction_sell_lines.unit_price_inc_tax - COALESCE(purchase_lines.purchase_price_inc_tax, variations.default_purchase_price, 0)) / transaction_sell_lines.unit_price_inc_tax * 100) ELSE 0 END as profit_percentage')
                ]);

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$request->start_date, $request->end_date]);
            }

            return Datatables::of($query)
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('unit_purchase_price', '<span class="display_currency" data-currency_symbol="true">{{$unit_purchase_price}}</span>')
                ->editColumn('unit_sale_price', '<span class="display_currency" data-currency_symbol="true">{{$unit_sale_price}}</span>')
                ->editColumn('unit_profit', '<span class="display_currency" data-currency_symbol="true">{{$unit_profit}}</span>')
                ->editColumn('total_profit', '<span class="display_currency" data-currency_symbol="true">{{$total_profit}}</span>')
                ->editColumn('total_amount', '<span class="display_currency" data-currency_symbol="true">{{$total_amount}}</span>')
                ->editColumn('profit_percentage', function($row) {
                    return '<span class="profit_percentage" data-orig-value="' . $row->profit_percentage . '">' . number_format($row->profit_percentage, 2) . '%</span>';
                })
                ->rawColumns(['unit_purchase_price', 'unit_sale_price', 'unit_profit', 'total_profit', 'total_amount', 'profit_percentage'])
                ->make(true);
        }

        return view('report.daily_product_profit');
    }

    /**
     * Shows product stock details
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Shows customer credit sale report
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerCreditReport(Request $request)
    {
        if (! auth()->user()->can('sell.view') && ! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $sells = Transaction::leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
                ->leftJoin(
                    DB::raw('(SELECT transaction_id, SUM(IF(is_return = 1, -1 * amount, amount)) as total_paid FROM transaction_payments GROUP BY transaction_id) as tp'),
                    'transactions.id',
                    '=',
                    'tp.transaction_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->whereIn('transactions.payment_status', ['due', 'partial'])
                ->select(
                    'transactions.id',
                    'c.name as customer_name',
                    'c.mobile',
                    'transactions.invoice_no',
                    'transactions.transaction_date',
                    'transactions.final_total',
                    DB::raw('COALESCE(tp.total_paid, 0) as total_paid'),
                    DB::raw('(transactions.final_total - COALESCE(tp.total_paid, 0)) as total_due'),
                    'transactions.payment_status'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty($request->input('customer_id'))) {
                $sells->where('transactions.contact_id', $request->input('customer_id'));
            }

            if (! empty($request->input('location_id'))) {
                $sells->where('transactions.location_id', $request->input('location_id'));
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $sells->whereDate('transactions.transaction_date', '>=', $start_date)
                    ->whereDate('transactions.transaction_date', '<=', $end_date);
            }

            return Datatables::of($sells)
                ->editColumn('customer_name', function ($row) {
                    return '<a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '" target="_blank" class="no-print">' . $row->customer_name . '</a>';
                })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('final_total', '<span class="final_total display_currency" data-orig-value="{{$final_total}}" data-currency_symbol="true">{{$final_total}}</span>')
                ->editColumn('total_paid', '<span class="total_paid display_currency" data-orig-value="{{$total_paid}}" data-currency_symbol="true">{{$total_paid}}</span>')
                ->editColumn('total_due', '<span class="total_due display_currency" data-orig-value="{{$total_due}}" data-currency_symbol="true">{{$total_due}}</span>')
                ->editColumn('payment_status', function ($row) {
                    $status_class = $row->payment_status == 'due' ? 'bg-danger' : 'bg-warning';
                    $status_label = $row->payment_status == 'due' ? __('lang_v1.due') : __('lang_v1.partial');
                    return '<span class="label ' . $status_class . '">' . $status_label . '</span>';
                })
                ->rawColumns(['customer_name', 'final_total', 'total_paid', 'total_due', 'payment_status'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $customers = Contact::customersDropdown($business_id, false, false);

        return view('report.customer_credit_report')
            ->with(compact('business_locations', 'customers'));
    }

    /**
     * Shows Purchase Price Variance Report
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchasePriceVarianceReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $variations = Variation::join('products as p', 'variations.product_id', '=', 'p.id')
                ->where('p.business_id', $business_id)
                ->where('p.type', '!=', 'modifier')
                ->select([
                    'p.name as product_name',
                    'variations.sub_sku',
                    'variations.id as variation_id'
                ]);

            return Datatables::of($variations)
                ->addColumn('supplier_1', function ($row) use ($business_id) {
                    $pl = $this->_get_ppv_lines($row->variation_id, $business_id);
                    return isset($pl[0]) ? $pl[0]->supplier_name : '--';
                })
                ->addColumn('price_1', function ($row) use ($business_id) {
                    $pl = $this->_get_ppv_lines($row->variation_id, $business_id);
                    if (!isset($pl[0])) return '--';
                    
                    $price = $pl[0]->purchase_price;
                    $html = '<b>' . $this->transactionUtil->num_f($price, true) . '</b>';
                    
                    if (isset($pl[1])) {
                        $diff = $price - $pl[1]->purchase_price;
                        $color = $diff > 0 ? 'text-danger' : ($diff < 0 ? 'text-success' : 'text-muted');
                        $icon = $diff > 0 ? 'fa-arrow-up' : ($diff < 0 ? 'fa-arrow-down' : 'fa-minus');
                        $html .= ' (<span class="'.$color.'"><i class="fa '.$icon.'"></i> ' . $this->transactionUtil->num_f(abs($diff), true) . '</span>)';
                    }
                    return $html;
                })
                ->addColumn('supplier_2', function ($row) use ($business_id) {
                    $pl = $this->_get_ppv_lines($row->variation_id, $business_id);
                    return isset($pl[1]) ? $pl[1]->supplier_name : '--';
                })
                ->addColumn('price_2', function ($row) use ($business_id) {
                    $pl = $this->_get_ppv_lines($row->variation_id, $business_id);
                    if (!isset($pl[1])) return '--';
                    
                    $price = $pl[1]->purchase_price;
                    $html = $this->transactionUtil->num_f($price, true);
                    
                    if (isset($pl[2])) {
                        $diff = $price - $pl[2]->purchase_price;
                        $color = $diff > 0 ? 'text-danger' : ($diff < 0 ? 'text-success' : 'text-muted');
                        $icon = $diff > 0 ? 'fa-arrow-up' : ($diff < 0 ? 'fa-arrow-down' : 'fa-minus');
                        $html .= ' (<span class="'.$color.'"><i class="fa '.$icon.'"></i> ' . $this->transactionUtil->num_f(abs($diff), true) . '</span>)';
                    }
                    return $html;
                })
                ->addColumn('supplier_3', function ($row) use ($business_id) {
                    $pl = $this->_get_ppv_lines($row->variation_id, $business_id);
                    return isset($pl[2]) ? $pl[2]->supplier_name : '--';
                })
                ->addColumn('price_3', function ($row) use ($business_id) {
                    $pl = $this->_get_ppv_lines($row->variation_id, $business_id);
                    return isset($pl[2]) ? $this->transactionUtil->num_f($pl[2]->purchase_price, true) : '--';
                })
                ->rawColumns(['price_1', 'price_2', 'price_3'])
                ->make(true);
        }

        return view('report.purchase_price_variance');
    }

    private function _get_ppv_lines($variation_id, $business_id)
    {
        return PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
            ->join('contacts as c', 't.contact_id', '=', 'c.id')
            ->where('t.business_id', $business_id)
            ->where('purchase_lines.variation_id', $variation_id)
            ->where('t.type', 'purchase')
            ->where('t.status', 'received')
            ->orderBy('t.transaction_date', 'desc')
            ->limit(3)
            ->select([
                'purchase_lines.purchase_price',
                'c.name as supplier_name'
            ])
            ->get();
    }

    /**
     * Daily Summary Report - shows all activity for a selected date
     */
    public function getDailySummaryReport(Request $request)
    {
        if (!auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.daily_summary', compact('business_locations'));
    }

    /**
     * Daily Summary Report Data - AJAX
     */
    public function getDailySummaryData(Request $request)
    {
        if (!auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $date       = $request->get('date', \Carbon\Carbon::today()->format('Y-m-d'));
        $location_id = $request->get('location_id');

        $start = $date . ' 00:00:00';
        $end   = $date . ' 23:59:59';

        $base = \App\Transaction::where('business_id', $business_id)
            ->whereBetween('transaction_date', [$start, $end]);

        if ($location_id) {
            $base->where('location_id', $location_id);
        }

        // --- Sales ---
        $salesQuery = (clone $base)->where('type', 'sell')->where('status', 'final');
        $sales = $salesQuery->select(
            DB::raw('COUNT(id) as count'),
            DB::raw('SUM(final_total) as total'),
            DB::raw('SUM(tax_amount) as tax'),
            DB::raw('SUM(discount_amount) as discount')
        )->first();

        // Sales by payment method
        $payments = DB::table('transaction_payments')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', $business_id)
            ->whereBetween('transactions.transaction_date', [$start, $end])
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->when($location_id, fn($q) => $q->where('transactions.location_id', $location_id))
            ->select('transaction_payments.method', DB::raw('SUM(transaction_payments.amount) as total'))
            ->groupBy('transaction_payments.method')
            ->get();

        // --- Sell Returns ---
        $sellReturns = (clone $base)->where('type', 'sell_return')
            ->select(DB::raw('COUNT(id) as count'), DB::raw('SUM(final_total) as total'))
            ->first();

        // --- Purchases ---
        $purchases = (clone $base)->where('type', 'purchase')
            ->select(DB::raw('COUNT(id) as count'), DB::raw('SUM(final_total) as total'))
            ->first();

        // --- Expenses ---
        $expenses = (clone $base)->where('type', 'expense')
            ->select(DB::raw('COUNT(id) as count'), DB::raw('SUM(final_total) as total'))
            ->first();

        // --- Stock Adjustments ---
        $stockAdj = (clone $base)->where('type', 'stock_adjustment')
            ->select(DB::raw('COUNT(id) as count'))
            ->first();

        // --- Top 10 selling products ---
        $topProducts = \App\TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
            ->where('transactions.business_id', $business_id)
            ->whereBetween('transactions.transaction_date', [$start, $end])
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->when($location_id, fn($q) => $q->where('transactions.location_id', $location_id))
            ->select(
                'products.name as product_name',
                'products.sku',
                DB::raw('SUM(transaction_sell_lines.quantity) as qty'),
                DB::raw('SUM(transaction_sell_lines.unit_price_before_discount * transaction_sell_lines.quantity) as revenue')
            )
            ->groupBy('transaction_sell_lines.product_id', 'products.name', 'products.sku')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        // --- Transactions list ---
        $transactions = \App\Transaction::where('transactions.business_id', $business_id)
            ->whereBetween('transactions.transaction_date', [$start, $end])
            ->whereIn('transactions.type', ['sell', 'sell_return', 'purchase', 'expense', 'stock_adjustment'])
            ->when($location_id, fn($q) => $q->where('transactions.location_id', $location_id))
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->leftJoin(
                DB::raw('(SELECT transaction_id, GROUP_CONCAT(DISTINCT method ORDER BY id SEPARATOR ",") as methods FROM transaction_payments GROUP BY transaction_id) as tp'),
                'tp.transaction_id', '=', 'transactions.id'
            )
            ->select(
                'transactions.id',
                'transactions.type',
                'transactions.ref_no',
                'transactions.invoice_no',
                'transactions.final_total',
                'transactions.transaction_date',
                'transactions.status',
                'contacts.name as contact_name',
                'tp.methods as payment_methods'
            )
            ->orderBy('transactions.transaction_date')
            ->get();

        // --- Lost Sales ---
        $lostSalesQuery = DB::table('lost_sales')
            ->whereDate('lost_sales.created_at', $date)
            ->where('lost_sales.business_id', $business_id);
        if ($location_id) {
            $lostSalesQuery->where('lost_sales.location_id', $location_id);
        }
        $lostSalesSummary = (clone $lostSalesQuery)
            ->select(
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(selling_price * quantity) as potential_revenue')
            )->first();

        $lostSalesTop = (clone $lostSalesQuery)
            ->select(
                'product_name',
                'sku',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(selling_price * quantity) as potential_revenue'),
                DB::raw('COUNT(*) as times_requested')
            )
            ->groupBy('product_name', 'sku')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // --- Follow-ups ---
        $followupsQuery = DB::table('followups')
            ->whereDate('followups.created_at', $date)
            ->where('followups.business_id', $business_id);
        if ($location_id) {
            $followupsQuery->where('followups.location_id', $location_id);
        }
        $followupsSummary = (clone $followupsQuery)
            ->select(
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN status = "contacted" THEN 1 ELSE 0 END) as contacted'),
                DB::raw('SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved')
            )->first();

        $followupsList = (clone $followupsQuery)
            ->leftJoin('products', 'followups.product_id', '=', 'products.id')
            ->select(
                'followups.customer_name',
                'followups.customer_phone',
                'followups.status',
                'followups.comment',
                'followups.quantity',
                'followups.created_at',
                'products.name as product_name'
            )
            ->orderBy('followups.created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'date'            => \Carbon\Carbon::parse($date)->format('d M Y'),
            'sales'           => $sales,
            'payments'        => $payments,
            'sell_returns'    => $sellReturns,
            'purchases'       => $purchases,
            'expenses'        => $expenses,
            'stock_adj'       => $stockAdj,
            'top_products'    => $topProducts,
            'transactions'    => $transactions,
            'lost_sales'      => $lostSalesSummary,
            'lost_sales_top'  => $lostSalesTop,
            'followups'       => $followupsSummary,
            'followups_list'  => $followupsList,
        ]);
    }

    /**
     * Daily Cash Reconciliation Report
     */
    public function getDailyReconciliation(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business    = \App\Business::find($business_id);

        $paymentLabels = [
            'cash'          => 'Cash',
            'card'          => 'Card',
            'cheque'        => 'Cheque',
            'bank_transfer' => 'Bank Transfer',
            'custom_pay_1'  => !empty($business->custom_pay_1) ? $business->custom_pay_1 : 'MPESA',
            'custom_pay_2'  => !empty($business->custom_pay_2) ? $business->custom_pay_2 : 'Custom Pay 2',
            'custom_pay_3'  => !empty($business->custom_pay_3) ? $business->custom_pay_3 : 'Custom Pay 3',
            'other'         => 'Other',
        ];

        if ($request->ajax()) {
            $date        = $request->get('date', now()->toDateString());
            $location_id = $request->get('location_id');
            $start       = $date . ' 00:00:00';
            $end         = $date . ' 23:59:59';

            // Sell transactions for the day
            $sellsQ = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereBetween('transaction_date', [$start, $end]);
            if ($location_id) $sellsQ->where('location_id', $location_id);

            $sells = (clone $sellsQ)->select(
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('COALESCE(SUM(final_total), 0) as gross_sales'),
                DB::raw('COALESCE(SUM(discount_amount), 0) as total_discount'),
                DB::raw('COALESCE(SUM(tax_amount), 0) as total_tax')
            )->first();

            $grossSales        = (float)($sells->gross_sales ?? 0);
            $totalDiscount     = (float)($sells->total_discount ?? 0);
            $totalTax          = (float)($sells->total_tax ?? 0);
            $totalTransactions = (int)($sells->total_transactions ?? 0);
            $netSales          = $grossSales - $totalDiscount;

            // Payment breakdown
            $txIds = (clone $sellsQ)->pluck('id');

            $paymentBreakdown = DB::table('transaction_payments')
                ->whereIn('transaction_id', $txIds)
                ->select('method', DB::raw('COALESCE(SUM(amount),0) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('method')
                ->get()
                ->keyBy('method');

            // COGS
            $cogs = 0;
            if ($txIds->isNotEmpty()) {
                $cogs = (float) DB::table('transaction_sell_lines as tsl')
                    ->join('transaction_sell_lines_purchase_lines as tspl', 'tsl.id', '=', 'tspl.sell_line_id')
                    ->join('purchase_lines as pl', 'tspl.purchase_line_id', '=', 'pl.id')
                    ->whereIn('tsl.transaction_id', $txIds)
                    ->selectRaw('COALESCE(SUM(tspl.quantity * pl.purchase_price_inc_tax), 0) as cogs')
                    ->value('cogs');
            }

            $grossProfit  = $netSales - $cogs;
            $profitMargin = $netSales > 0 ? round(($grossProfit / $netSales) * 100, 2) : 0;
            $avgBasket    = $totalTransactions > 0 ? round($netSales / $totalTransactions, 2) : 0;

            // Sell returns
            $returnsQ = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell_return')
                ->whereBetween('transaction_date', [$start, $end]);
            if ($location_id) $returnsQ->where('location_id', $location_id);
            $totalReturns = (float) $returnsQ->sum('final_total');

            // Expenses
            $expQ = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$start, $end]);
            if ($location_id) $expQ->where('location_id', $location_id);
            $totalExpenses = (float) $expQ->sum('final_total');

            return response()->json([
                'success'            => true,
                'date_label'         => \Carbon\Carbon::parse($date)->format('D, d M Y'),
                'gross_sales'        => $grossSales,
                'total_discount'     => $totalDiscount,
                'total_tax'          => $totalTax,
                'net_sales'          => $netSales,
                'total_returns'      => $totalReturns,
                'total_expenses'     => $totalExpenses,
                'cogs'               => $cogs,
                'gross_profit'       => $grossProfit,
                'profit_margin'      => $profitMargin,
                'total_transactions' => $totalTransactions,
                'avg_basket'         => $avgBasket,
                'payment_breakdown'  => $paymentBreakdown,
                'payment_labels'     => $paymentLabels,
            ]);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        return view('report.daily_reconciliation', compact('business_locations', 'paymentLabels', 'payment_types'));
    }

    /**
     * Returns summary totals for the sale report (KPI cards).
     */
    public function getSaleReportSummary(Request $request)
    {
        if (! auth()->user()->can('sell.view') && ! auth()->user()->can('sell.create') && ! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');

        $query = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween(DB::raw('DATE(transaction_date)'), [$request->start_date, $request->end_date]);
        }
        if ($request->filled('location_id')) {
            $query->where('transactions.location_id', $request->location_id);
        }
        if ($request->filled('customer_id')) {
            $query->where('transactions.contact_id', $request->customer_id);
        }

        $summary = $query->select(
            DB::raw('COUNT(*) as count'),
            DB::raw('COALESCE(SUM(final_total), 0) as total'),
            DB::raw('COALESCE(SUM(tax_amount), 0) as tax'),
            DB::raw('COALESCE(SUM(discount_amount), 0) as discount')
        )->first();

        return response()->json([
            'count'    => (int)$summary->count,
            'total'    => (float)$summary->total,
            'tax'      => (float)$summary->tax,
            'discount' => (float)$summary->discount,
        ]);
    }

    /**
     * Returns summary totals for the purchase report (KPI cards).
     */
    public function getPurchaseReportSummary(Request $request)
    {
        if (! auth()->user()->can('purchase.view') && ! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');

        $query = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'purchase');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween(DB::raw('DATE(transaction_date)'), [$request->start_date, $request->end_date]);
        }
        if ($request->filled('location_id')) {
            $query->where('transactions.location_id', $request->location_id);
        }
        if ($request->filled('supplier_id')) {
            $query->where('transactions.contact_id', $request->supplier_id);
        }
        if ($request->filled('status')) {
            $query->where('transactions.status', $request->status);
        }

        $summary = $query->select(
            DB::raw('COUNT(*) as count'),
            DB::raw('COALESCE(SUM(final_total), 0) as total'),
            DB::raw('COALESCE(SUM(tax_amount), 0) as tax'),
            DB::raw('COALESCE(SUM(discount_amount), 0) as discount')
        )->first();

        return response()->json([
            'count'    => (int)$summary->count,
            'total'    => (float)$summary->total,
            'tax'      => (float)$summary->tax,
            'discount' => (float)$summary->discount,
        ]);
    }
}

