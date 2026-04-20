<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\HospitalBill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HospitalBillingController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('hospital_billing.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = HospitalBill::where('hospital_bills.business_id', $business_id)
                ->with(['creator', 'patient'])
                ->select('hospital_bills.*');

            if ($request->filled('location_id')) {
                $query->where('location_id', $request->location_id);
            }
            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }
            if ($request->filled('visit_type')) {
                $query->where('visit_type', $request->visit_type);
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
                $end   = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
                $query->whereBetween('visit_date', [$start, $end]);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    $html .= '<a href="' . route('hospital-billing.show', $row->id) . '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a> ';
                    $html .= '<a href="' . route('hospital-billing.edit', $row->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a> ';
                    $html .= '<a href="' . route('hospital-billing.print', $row->id) . '" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-print"></i></a>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('payment_status_badge', function ($row) {
                    $colors = HospitalBill::paymentStatusColors();
                    $color  = $colors[$row->payment_status] ?? 'default';
                    return '<span class="label label-' . $color . '">' . ucfirst($row->payment_status) . '</span>';
                })
                ->editColumn('visit_date', fn($r) => $r->visit_date ? $r->visit_date->format('d M Y') : '')
                ->editColumn('total_amount', fn($r) => number_format($r->total_amount, 2))
                ->editColumn('balance', fn($r) => number_format($r->balance, 2))
                ->rawColumns(['action', 'payment_status_badge'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $visit_types        = HospitalBill::visitTypes();
        $payment_statuses   = ['unpaid' => 'Unpaid', 'partial' => 'Partial', 'paid' => 'Paid'];

        return view('hospital_billing.index', compact(
            'business_locations', 'visit_types', 'payment_statuses'
        ));
    }

    public function create()
    {
        if (! auth()->user()->can('hospital_billing.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id        = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $patients           = Contact::customersDropdown($business_id);
        $visit_types        = HospitalBill::visitTypes();

        $default_services = [
            ['name' => 'Consultation Fee', 'category' => 'consultation'],
            ['name' => 'Lab Test', 'category' => 'laboratory'],
            ['name' => 'Pharmacy', 'category' => 'pharmacy'],
            ['name' => 'Nursing Care', 'category' => 'nursing'],
            ['name' => 'Physiotherapy', 'category' => 'therapy'],
            ['name' => 'X-Ray', 'category' => 'radiology'],
            ['name' => 'Ultrasound', 'category' => 'radiology'],
            ['name' => 'Admission Fee', 'category' => 'inpatient'],
            ['name' => 'Ward Charges (per day)', 'category' => 'inpatient'],
            ['name' => 'Theatre Fee', 'category' => 'surgery'],
            ['name' => 'Ambulance', 'category' => 'other'],
        ];

        return view('hospital_billing.create', compact(
            'business_locations', 'patients', 'visit_types', 'default_services'
        ));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('hospital_billing.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'patient_name' => 'required|string|max:191',
            'visit_date'   => 'required|date',
            'visit_type'   => 'required|in:outpatient,inpatient,emergency',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            $items       = $request->input('bill_items', []);
            $subtotal    = collect($items)->sum('total');
            $nhif        = (float) $request->input('nhif_amount', 0);
            $discount    = (float) $request->input('discount', 0);
            $total       = $subtotal - $nhif - $discount;
            $paid        = (float) $request->input('paid_amount', 0);
            $balance     = $total - $paid;
            $pay_status  = $balance <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');

            HospitalBill::create([
                'business_id'     => $business_id,
                'location_id'     => $request->location_id,
                'bill_number'     => HospitalBill::generateBillNumber($business_id),
                'patient_id'      => $request->patient_id ?: null,
                'patient_name'    => $request->patient_name,
                'patient_phone'   => $request->patient_phone,
                'patient_dob'     => $request->patient_dob,
                'gender'          => $request->gender,
                'nhif_number'     => $request->nhif_number,
                'doctor_name'     => $request->doctor_name,
                'visit_date'      => $request->visit_date,
                'visit_type'      => $request->visit_type,
                'diagnosis'       => $request->diagnosis,
                'bill_items'      => $items,
                'subtotal'        => $subtotal,
                'nhif_amount'     => $nhif,
                'discount'        => $discount,
                'total_amount'    => $total,
                'paid_amount'     => $paid,
                'balance'         => $balance,
                'payment_status'  => $pay_status,
                'payment_method'  => $request->payment_method,
                'mpesa_code'      => $request->mpesa_code,
                'status'          => 'active',
                'created_by'      => auth()->id(),
                'notes'           => $request->notes,
            ]);

            $output = ['success' => 1, 'msg' => 'Bill created successfully.'];
        } catch (\Exception $e) {
            \Log::emergency('HospitalBilling store: ' . $e->getMessage());
            $output = ['success' => 0, 'msg' => 'Something went wrong. ' . $e->getMessage()];
        }

        return redirect()->route('hospital-billing.index')->with('status', $output);
    }

    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $bill = HospitalBill::where('business_id', $business_id)->with('creator')->findOrFail($id);
        return view('hospital_billing.show', compact('bill'));
    }

    public function edit($id)
    {
        if (! auth()->user()->can('hospital_billing.create')) {
            abort(403);
        }
        $business_id        = request()->session()->get('user.business_id');
        $bill               = HospitalBill::where('business_id', $business_id)->findOrFail($id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $patients           = Contact::customersDropdown($business_id);
        $visit_types        = HospitalBill::visitTypes();

        return view('hospital_billing.edit', compact('bill', 'business_locations', 'patients', 'visit_types'));
    }

    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('hospital_billing.create')) {
            abort(403);
        }

        $business_id = request()->session()->get('user.business_id');
        $bill        = HospitalBill::where('business_id', $business_id)->findOrFail($id);

        $items      = $request->input('bill_items', []);
        $subtotal   = collect($items)->sum('total');
        $nhif       = (float) $request->input('nhif_amount', 0);
        $discount   = (float) $request->input('discount', 0);
        $total      = $subtotal - $nhif - $discount;
        $paid       = (float) $request->input('paid_amount', 0);
        $balance    = $total - $paid;
        $pay_status = $balance <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');

        $bill->update([
            'patient_name'   => $request->patient_name,
            'patient_phone'  => $request->patient_phone,
            'patient_dob'    => $request->patient_dob,
            'gender'         => $request->gender,
            'nhif_number'    => $request->nhif_number,
            'doctor_name'    => $request->doctor_name,
            'visit_date'     => $request->visit_date,
            'visit_type'     => $request->visit_type,
            'diagnosis'      => $request->diagnosis,
            'bill_items'     => $items,
            'subtotal'       => $subtotal,
            'nhif_amount'    => $nhif,
            'discount'       => $discount,
            'total_amount'   => $total,
            'paid_amount'    => $paid,
            'balance'        => $balance,
            'payment_status' => $pay_status,
            'payment_method' => $request->payment_method,
            'mpesa_code'     => $request->mpesa_code,
            'notes'          => $request->notes,
        ]);

        return redirect()->route('hospital-billing.index')
            ->with('status', ['success' => 1, 'msg' => 'Bill updated successfully.']);
    }

    public function printBill($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $bill        = HospitalBill::where('business_id', $business_id)->with('creator')->findOrFail($id);
        $business    = request()->session()->get('business');
        return view('hospital_billing.print', compact('bill', 'business'));
    }

    public function destroy($id)
    {
        if (! auth()->user()->can('hospital_billing.create')) {
            abort(403);
        }
        $business_id = request()->session()->get('user.business_id');
        HospitalBill::where('business_id', $business_id)->findOrFail($id)->delete();
        return response()->json(['success' => true, 'msg' => 'Deleted.']);
    }
}
