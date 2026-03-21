<?php

namespace App\Http\Controllers;

use App\CoolerDealer;
use App\CoolerDocument;
use App\Utils\CoolerDocumentService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CoolerDealerController extends Controller
{
    protected CoolerDocumentService $docService;

    public function __construct(CoolerDocumentService $docService)
    {
        $this->docService = $docService;
    }

    public function index(Request $request)
    {
        if (!auth()->user()->can('cooler.dealer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $dealers = CoolerDealer::where('cooler_dealers.business_id', $business_id)
                ->select([
                    'id', 'name', 'outlet_name', 'channel', 'phone',
                    'area', 'compliance_score', 'status',
                ]);

            return DataTables::of($dealers)
                ->addColumn('status_badge', fn ($row) => (new CoolerDealer($row->toArray()))->status_badge)
                ->addColumn('compliance', fn ($row) => $this->complianceBadge($row->compliance_score))
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    if (auth()->user()->can('cooler.dealer.view')) {
                        $html .= '<a href="' . route('cooler.dealers.show', $row->id) . '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a> ';
                    }
                    if (auth()->user()->can('cooler.dealer.update')) {
                        $html .= '<a href="' . route('cooler.dealers.edit', $row->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a> ';
                    }
                    if (auth()->user()->can('cooler.dealer.delete')) {
                        $html .= '<button class="btn btn-xs btn-danger btn-delete-dealer" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['status_badge', 'compliance', 'action'])
                ->make(true);
        }

        return view('cooler.dealers.index');
    }

    public function create()
    {
        if (!auth()->user()->can('cooler.dealer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $channels = array_combine(CoolerDealer::$channels, array_map('ucfirst', CoolerDealer::$channels));
        $brands   = CoolerDealer::$brandOptions;

        return view('cooler.dealers.create', compact('channels', 'brands'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('cooler.dealer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'                          => 'required|string|max:255',
            'id_number'                     => 'required|string|max:50',
            'kra_pin'                       => 'required|string|max:20',
            'phone'                         => 'required|string|max:20',
            'postal_address'                => 'nullable|string|max:255',
            'outlet_name'                   => 'required|string|max:255',
            'channel'                       => 'required|in:' . implode(',', CoolerDealer::$channels),
            'building'                      => 'nullable|string|max:100',
            'road'                          => 'nullable|string|max:100',
            'area'                          => 'nullable|string|max:100',
            'years_in_business'             => 'required|integer|min:0',
            'brands_stocked'                => 'nullable|array',
            // Required uploads
            'id_copy'                       => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'kra_pin_certificate'           => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'passport_photo'                => 'required|image|mimes:jpg,jpeg,png|max:2048',
            // Legal docs — at least one required
            'county_business_permit'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificate_of_registration'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificate_of_incorporation'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tax_certificate'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            // Expiry dates
            'county_business_permit_expiry' => 'nullable|date',
            'tax_certificate_expiry'        => 'nullable|date',
        ]);

        // Ensure at least one legal document
        $legalFields = ['county_business_permit', 'certificate_of_registration', 'certificate_of_incorporation', 'tax_certificate'];
        $hasLegal    = collect($legalFields)->contains(fn($f) => $request->hasFile($f));
        if (!$hasLegal) {
            return back()->withErrors(['legal_docs' => 'At least one legal business document is required.'])->withInput();
        }

        $business_id = $request->session()->get('user.business_id');

        $dealer = CoolerDealer::create([
            'business_id'       => $business_id,
            'name'              => $request->name,
            'id_number'         => $request->id_number,
            'kra_pin'           => $request->kra_pin,
            'phone'             => $request->phone,
            'postal_address'    => $request->postal_address,
            'outlet_name'       => $request->outlet_name,
            'channel'           => $request->channel,
            'building'          => $request->building,
            'road'              => $request->road,
            'area'              => $request->area,
            'years_in_business' => $request->years_in_business,
            'brands_stocked'    => $request->brands_stocked,
            'created_by'        => auth()->id(),
        ]);

        // Upload documents
        $uploads = [
            'id_copy'                      => [],
            'kra_pin_certificate'          => [],
            'passport_photo'               => [],
            'county_business_permit'       => ['expires_at' => $request->county_business_permit_expiry],
            'certificate_of_registration'  => [],
            'certificate_of_incorporation' => [],
            'tax_certificate'              => ['expires_at' => $request->tax_certificate_expiry],
        ];

        foreach ($uploads as $field => $options) {
            if ($request->hasFile($field)) {
                try {
                    $this->docService->upload($request->file($field), $dealer, $field, $options);
                } catch (\Exception $e) {
                    // Log but don't block — dealer is created
                    \Log::warning("Failed to upload {$field} for dealer {$dealer->id}: " . $e->getMessage());
                }
            }
        }

        return redirect()->route('cooler.dealers.show', $dealer->id)
            ->with('success', 'Dealer registered successfully.');
    }

    public function show(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.dealer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $dealer      = CoolerDealer::forBusiness($business_id)
            ->with(['coolers', 'activeAgreement.cooler', 'documents', 'complianceLogs' => fn($q) => $q->latest()->take(10)])
            ->findOrFail($id);

        $docTypes = CoolerDocument::$typeLabels ?? [];

        return view('cooler.dealers.show', compact('dealer', 'docTypes'));
    }

    public function edit(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.dealer.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $dealer      = CoolerDealer::forBusiness($business_id)->with('documents')->findOrFail($id);
        $channels    = array_combine(CoolerDealer::$channels, array_map('ucfirst', CoolerDealer::$channels));
        $brands      = CoolerDealer::$brandOptions;

        return view('cooler.dealers.edit', compact('dealer', 'channels', 'brands'));
    }

    public function update(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.dealer.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $dealer      = CoolerDealer::forBusiness($business_id)->findOrFail($id);

        $request->validate([
            'name'              => 'required|string|max:255',
            'id_number'         => 'required|string|max:50',
            'kra_pin'           => 'required|string|max:20',
            'phone'             => 'required|string|max:20',
            'outlet_name'       => 'required|string|max:255',
            'channel'           => 'required|in:' . implode(',', CoolerDealer::$channels),
            'years_in_business' => 'required|integer|min:0',
        ]);

        $dealer->update($request->only([
            'name', 'id_number', 'kra_pin', 'phone', 'postal_address',
            'outlet_name', 'channel', 'building', 'road', 'area',
            'years_in_business', 'brands_stocked', 'status',
        ]));

        // Handle optional new file uploads
        $uploadFields = [
            'id_copy', 'kra_pin_certificate', 'passport_photo',
            'county_business_permit', 'certificate_of_registration',
            'certificate_of_incorporation', 'tax_certificate',
        ];

        foreach ($uploadFields as $field) {
            if ($request->hasFile($field)) {
                $options = [];
                if ($field === 'county_business_permit' && $request->county_business_permit_expiry) {
                    $options['expires_at'] = $request->county_business_permit_expiry;
                }
                if ($field === 'tax_certificate' && $request->tax_certificate_expiry) {
                    $options['expires_at'] = $request->tax_certificate_expiry;
                }
                try {
                    $this->docService->upload($request->file($field), $dealer, $field, $options);
                } catch (\Exception $e) {
                    \Log::warning("Failed to upload {$field} for dealer {$dealer->id}: " . $e->getMessage());
                }
            }
        }

        return redirect()->route('cooler.dealers.show', $dealer->id)
            ->with('success', 'Dealer updated successfully.');
    }

    public function destroy(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.dealer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $dealer      = CoolerDealer::forBusiness($business_id)->findOrFail($id);
        $dealer->delete();

        return response()->json(['success' => true]);
    }

    protected function complianceBadge(float $score): string
    {
        $class = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
        return "<span class=\"label label-{$class}\">{$score}%</span>";
    }
}
