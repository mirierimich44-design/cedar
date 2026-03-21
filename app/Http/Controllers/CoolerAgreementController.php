<?php

namespace App\Http\Controllers;

use App\CoolerAgreement;
use App\CoolerAsset;
use App\CoolerDealer;
use App\Utils\CoolerPdfService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CoolerAgreementController extends Controller
{
    protected CoolerPdfService $pdfService;

    public function __construct(CoolerPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        if (!auth()->user()->can('cooler.agreement.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $agreements = CoolerAgreement::where('cooler_agreements.business_id', $business_id)
                ->join('cooler_dealers as cd', 'cooler_agreements.dealer_id', '=', 'cd.id')
                ->join('cooler_assets as ca', 'cooler_agreements.cooler_id', '=', 'ca.id')
                ->select([
                    'cooler_agreements.id', 'cooler_agreements.agreement_date',
                    'cooler_agreements.status', 'cooler_agreements.sales_volume_target',
                    'cd.name as dealer_name', 'cd.outlet_name',
                    'ca.asset_number', 'ca.asset_type',
                ]);

            return DataTables::of($agreements)
                ->addColumn('status_badge', fn ($row) => (new CoolerAgreement(['status' => $row->status]))->status_badge)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    $html .= '<a href="' . route('cooler.agreements.show', $row->id) . '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a> ';
                    if (auth()->user()->can('cooler.agreement.sign')) {
                        $html .= '<a href="' . route('cooler.agreements.sign', $row->id) . '" class="btn btn-xs btn-warning"><i class="fa fa-pen"></i> Sign</a> ';
                    }
                    $html .= '<a href="' . route('cooler.agreements.pdf', $row->id) . '" class="btn btn-xs btn-default" target="_blank"><i class="fa fa-file-pdf"></i></a>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('cooler.agreements.index');
    }

    public function create(Request $request)
    {
        if (!auth()->user()->can('cooler.agreement.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $dealers     = CoolerDealer::forBusiness($business_id)->active()->pluck('outlet_name', 'id');
        $coolers     = CoolerAsset::forBusiness($business_id)->available()->pluck('asset_number', 'id');

        return view('cooler.agreements.create', compact('dealers', 'coolers'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('cooler.agreement.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'dealer_id'           => 'required|integer|exists:cooler_dealers,id',
            'cooler_id'           => 'required|integer|exists:cooler_assets,id',
            'agreement_date'      => 'required|date',
            'sales_volume_target' => 'nullable|numeric|min:0',
        ]);

        $business_id = $request->session()->get('user.business_id');
        $dealer      = CoolerDealer::forBusiness($business_id)->findOrFail($request->dealer_id);

        if (!$dealer->hasRequiredDocuments()) {
            return back()->withErrors(['dealer_id' => 'Dealer does not have all required documents uploaded. Please complete dealer registration first.'])->withInput();
        }

        $agreement = CoolerAgreement::create([
            'business_id'         => $business_id,
            'dealer_id'           => $request->dealer_id,
            'cooler_id'           => $request->cooler_id,
            'agreement_date'      => $request->agreement_date,
            'sales_volume_target' => $request->sales_volume_target,
            'status'              => 'draft',
            'created_by'          => auth()->id(),
        ]);

        // Mark cooler as deployed
        CoolerAsset::find($request->cooler_id)->update([
            'status'            => 'deployed',
            'current_dealer_id' => $request->dealer_id,
            'deployment_date'   => $request->agreement_date,
        ]);

        return redirect()->route('cooler.agreements.sign', $agreement->id)
            ->with('success', 'Agreement created. Please capture all signatures.');
    }

    public function show(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.agreement.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $agreement   = CoolerAgreement::forBusiness($business_id)
            ->with(['dealer.documents', 'cooler', 'retrievals'])
            ->findOrFail($id);

        return view('cooler.agreements.show', compact('agreement'));
    }

    public function sign(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.agreement.sign')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $agreement   = CoolerAgreement::forBusiness($business_id)
            ->with(['dealer', 'cooler'])
            ->findOrFail($id);

        return view('cooler.agreements.sign', compact('agreement'));
    }

    public function captureSignature(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.agreement.sign')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'signatory_type'   => 'required|in:company_legal,rsm_tsm,dealer,distributor',
            'signature_data'   => 'required|string',
            'signatory_name'   => 'required|string|max:255',
        ]);

        $business_id = $request->session()->get('user.business_id');
        $agreement   = CoolerAgreement::forBusiness($business_id)->findOrFail($id);

        $path = $agreement->captureSignature(
            $request->signatory_type,
            $request->signature_data,
            $request->signatory_name
        );

        // If all signatures captured, activate agreement
        $agreement->refresh();
        if ($agreement->isFullySigned()) {
            $agreement->update(['status' => 'active']);
        }

        return response()->json(['success' => true, 'path' => $path, 'fully_signed' => $agreement->isFullySigned()]);
    }

    public function terminate(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.agreement.terminate')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'termination_reason' => 'required|string',
        ]);

        $business_id = $request->session()->get('user.business_id');
        $agreement   = CoolerAgreement::forBusiness($business_id)->findOrFail($id);

        $agreement->update([
            'status'             => 'terminated',
            'termination_reason' => $request->termination_reason,
            'termination_date'   => now()->toDateString(),
        ]);

        return redirect()->route('cooler.agreements.show', $id)
            ->with('success', 'Agreement terminated.');
    }

    public function downloadPdf(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.agreement.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $agreement   = CoolerAgreement::forBusiness($business_id)
            ->with(['dealer.documents', 'cooler'])
            ->findOrFail($id);

        $html     = $this->pdfService->generateAgreementHtml($agreement);
        $pdfPath  = "uploads/cooler/agreements/{$agreement->id}/agreement.pdf";
        $savedPath = $this->pdfService->savePdf($html, $pdfPath);

        $agreement->update(['generated_pdf_path' => $savedPath]);

        return response()->download(public_path($savedPath), "Agreement-{$agreement->id}.pdf");
    }
}
