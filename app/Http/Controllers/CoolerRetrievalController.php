<?php

namespace App\Http\Controllers;

use App\CoolerAgreement;
use App\CoolerAsset;
use App\CoolerDealer;
use App\CoolerRetrieval;
use App\Utils\CoolerDocumentService;
use App\Utils\CoolerPdfService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CoolerRetrievalController extends Controller
{
    protected CoolerDocumentService $docService;
    protected CoolerPdfService $pdfService;

    public function __construct(CoolerDocumentService $docService, CoolerPdfService $pdfService)
    {
        $this->docService = $docService;
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        if (!auth()->user()->can('cooler.retrieval.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $retrievals = CoolerRetrieval::where('cooler_retrievals.business_id', $business_id)
                ->join('cooler_dealers as cd', 'cooler_retrievals.dealer_id', '=', 'cd.id')
                ->join('cooler_assets as ca', 'cooler_retrievals.cooler_id', '=', 'ca.id')
                ->select([
                    'cooler_retrievals.id', 'cooler_retrievals.retrieval_date',
                    'cooler_retrievals.reason', 'cooler_retrievals.status',
                    'cd.name as dealer_name', 'cd.outlet_name',
                    'ca.asset_number', 'ca.serial_number',
                ]);

            return DataTables::of($retrievals)
                ->addColumn('status_badge', fn ($row) => (new CoolerRetrieval(['status' => $row->status]))->status_badge)
                ->addColumn('reason_label', fn ($row) => CoolerRetrieval::$reasons[$row->reason] ?? ucfirst(str_replace('_', ' ', $row->reason)))
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    $html .= '<a href="' . route('cooler.retrievals.show', $row->id) . '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a> ';
                    if ($row->status !== 'completed' && auth()->user()->can('cooler.retrieval.execute')) {
                        $html .= '<a href="' . route('cooler.retrievals.execute', $row->id) . '" class="btn btn-xs btn-warning"><i class="fa fa-truck"></i> Execute</a> ';
                    }
                    $html .= '<a href="' . route('cooler.retrievals.letter', $row->id) . '" class="btn btn-xs btn-default" target="_blank"><i class="fa fa-file-pdf"></i></a>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('cooler.retrievals.index');
    }

    public function create(Request $request)
    {
        if (!auth()->user()->can('cooler.retrieval.initiate')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $dealers     = CoolerDealer::forBusiness($business_id)->active()->pluck('outlet_name', 'id');
        $reasons     = CoolerRetrieval::$reasons;
        $user        = auth()->user();

        return view('cooler.retrievals.create', compact('dealers', 'reasons', 'user'));
    }

    public function getDealerCoolers(Request $request, int $dealerId)
    {
        $business_id = $request->session()->get('user.business_id');
        $dealer      = CoolerDealer::forBusiness($business_id)->findOrFail($dealerId);

        $coolers = CoolerAsset::where('current_dealer_id', $dealerId)
            ->where('status', 'deployed')
            ->get(['id', 'asset_number', 'serial_number', 'asset_type']);

        $agreement = $dealer->activeAgreement;

        return response()->json([
            'coolers'      => $coolers,
            'agreement_id' => $agreement?->id,
        ]);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('cooler.retrieval.initiate')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'dealer_id'              => 'required|integer|exists:cooler_dealers,id',
            'cooler_id'              => 'required|integer|exists:cooler_assets,id',
            'retrieval_date'         => 'required|date',
            'reason'                 => 'required|in:' . implode(',', array_keys(CoolerRetrieval::$reasons)),
            'reason_notes'           => 'nullable|string',
            'authorized_staff_name'  => 'required|string|max:255',
            'authorized_staff_id_no' => 'nullable|string|max:50',
            'authorized_staff_tel'   => 'nullable|string|max:20',
        ]);

        $business_id = $request->session()->get('user.business_id');
        $agreement   = CoolerAgreement::where('dealer_id', $request->dealer_id)
            ->where('cooler_id', $request->cooler_id)
            ->where('status', 'active')
            ->first();

        $retrieval = CoolerRetrieval::create([
            'business_id'            => $business_id,
            'agreement_id'           => $agreement?->id,
            'dealer_id'              => $request->dealer_id,
            'cooler_id'              => $request->cooler_id,
            'retrieval_date'         => $request->retrieval_date,
            'reason'                 => $request->reason,
            'reason_notes'           => $request->reason_notes,
            'authorized_staff_name'  => $request->authorized_staff_name,
            'authorized_staff_id_no' => $request->authorized_staff_id_no,
            'authorized_staff_tel'   => $request->authorized_staff_tel,
            'status'                 => 'initiated',
            'created_by'             => auth()->id(),
        ]);

        return redirect()->route('cooler.retrievals.execute', $retrieval->id)
            ->with('success', 'Retrieval initiated. Proceed with field execution.');
    }

    public function show(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.retrieval.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $retrieval   = CoolerRetrieval::forBusiness($business_id)
            ->with(['dealer', 'cooler', 'agreement', 'documents'])
            ->findOrFail($id);

        return view('cooler.retrievals.show', compact('retrieval'));
    }

    public function execute(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.retrieval.execute')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $retrieval   = CoolerRetrieval::forBusiness($business_id)
            ->with(['dealer', 'cooler', 'documents'])
            ->findOrFail($id);

        return view('cooler.retrievals.execute', compact('retrieval'));
    }

    public function uploadPhoto(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.retrieval.execute')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'photo_type' => 'required|in:retrieval_photo_before,retrieval_photo_during,retrieval_photo_after',
            'photos'     => 'required|array|max:10',
            'photos.*'   => 'required|image|mimes:jpg,jpeg,png,heic|max:10240',
        ]);

        $business_id = $request->session()->get('user.business_id');
        $retrieval   = CoolerRetrieval::forBusiness($business_id)->findOrFail($id);

        $uploaded = [];
        foreach ($request->file('photos') as $photo) {
            $doc = $this->docService->upload($photo, $retrieval, $request->photo_type, [
                'metadata' => ['gps' => $request->gps_coordinates],
            ]);
            $uploaded[] = [
                'id'            => $doc->id,
                'thumbnail_url' => $doc->thumbnail_url,
                'url'           => $doc->url,
                'file_name'     => $doc->file_name,
            ];
        }

        $retrieval->update(['status' => 'in_progress']);

        return response()->json(['success' => true, 'uploaded' => $uploaded]);
    }

    public function captureSignature(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.retrieval.execute')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'signature_data'  => 'required|string',
            'gps_coordinates' => 'nullable|string',
        ]);

        $business_id = $request->session()->get('user.business_id');
        $retrieval   = CoolerRetrieval::forBusiness($business_id)->findOrFail($id);

        $path = $retrieval->captureCustomerSignature($request->signature_data);

        if ($request->gps_coordinates) {
            $retrieval->update(['gps_coordinates' => $request->gps_coordinates]);
        }

        return response()->json(['success' => true, 'path' => $path]);
    }

    public function uploadSignedLetter(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.retrieval.execute')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'signed_letter' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $business_id = $request->session()->get('user.business_id');
        $retrieval   = CoolerRetrieval::forBusiness($business_id)->findOrFail($id);

        $doc  = $this->docService->upload($request->file('signed_letter'), $retrieval, 'signed_retrieval_letter');
        $retrieval->update(['retrieval_letter_path' => $doc->file_path]);

        return response()->json(['success' => true]);
    }

    public function complete(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.retrieval.execute')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $retrieval   = CoolerRetrieval::forBusiness($business_id)
            ->with(['cooler', 'agreement'])
            ->findOrFail($id);

        // Mark cooler as retrieved
        $retrieval->cooler->update([
            'status'            => 'retrieved',
            'current_dealer_id' => null,
            'deployment_date'   => null,
        ]);

        // Terminate agreement if exists
        if ($retrieval->agreement && $retrieval->agreement->status === 'active') {
            $retrieval->agreement->update([
                'status'             => 'terminated',
                'termination_reason' => $retrieval->reason_label,
                'termination_date'   => now()->toDateString(),
            ]);
        }

        $retrieval->update(['status' => 'completed']);

        return response()->json(['success' => true, 'redirect' => route('cooler.retrievals.show', $id)]);
    }

    public function downloadLetter(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.retrieval.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $retrieval   = CoolerRetrieval::forBusiness($business_id)
            ->with(['dealer', 'cooler'])
            ->findOrFail($id);

        $html     = $this->pdfService->generateRetrievalLetterHtml($retrieval);
        $pdfPath  = "uploads/cooler/retrievals/{$retrieval->id}/retrieval_letter.pdf";
        $savedPath = $this->pdfService->savePdf($html, $pdfPath);

        $retrieval->update(['retrieval_letter_path' => $savedPath]);

        return response()->download(public_path($savedPath), "RetrievalLetter-{$retrieval->id}.pdf");
    }
}
