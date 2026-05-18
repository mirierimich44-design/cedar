<?php

namespace Modules\Hospital\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Hospital\Entities\MortuaryRecord;
use Yajra\DataTables\Facades\DataTables;

class MortuaryController extends Controller
{
    /**
     * Display a listing of mortuary records.
     */
    public function index(Request $request)
    {
        if (! auth()->user()->can('hospital.mortuary') && ! auth()->user()->can('hospital.admin')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session()->get('user.business_id');

        if ($request->ajax()) {
            $records = MortuaryRecord::where('business_id', $business_id)
                ->select(['id', 'body_reference', 'deceased_name', 'gender', 'date_of_death', 'storage_date', 'status', 'brought_by', 'relationship', 'created_at']);

            return DataTables::of($records)
                ->addColumn('status_badge', function ($r) {
                    $map = ['stored' => 'info', 'released' => 'success', 'transferred' => 'warning'];
                    $class = $map[$r->status] ?? 'secondary';
                    return "<span class=\"badge badge-{$class}\">" . ucfirst($r->status) . '</span>';
                })
                ->addColumn('action', function ($r) {
                    $html = '';
                    if ($r->status === 'stored') {
                        $html .= '<button class="btn btn-xs btn-success btn-release-body" data-id="' . $r->id . '" data-toggle="modal" data-target="#releaseModal">'
                               . '<i class="fa fa-check"></i> Release</button> ';
                    }
                    return $html;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('hospital::mortuary.index');
    }

    /**
     * Show the form for creating a new mortuary record.
     */
    public function create()
    {
        if (! auth()->user()->can('hospital.mortuary') && ! auth()->user()->can('hospital.admin')) {
            abort(403, 'Unauthorized');
        }

        return view('hospital::mortuary.create');
    }

    /**
     * Store a newly created mortuary record.
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('hospital.mortuary') && ! auth()->user()->can('hospital.admin')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        $request->validate([
            'deceased_name'    => 'required|string|max:150',
            'deceased_dob'     => 'nullable|date',
            'gender'           => 'required|in:M,F,Other',
            'cause_of_death'   => 'nullable|string',
            'date_of_death'    => 'required|date',
            'time_of_death'    => 'nullable|date_format:H:i',
            'brought_by'       => 'required|string|max:150',
            'brought_by_phone' => 'required|string|max:30',
            'relationship'     => 'required|string|max:100',
            'storage_location' => 'nullable|string|max:100',
            'storage_date'     => 'required|date',
            'notes'            => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $business_id   = session()->get('user.business_id');
            $body_reference = MortuaryRecord::generateBodyReference($business_id);

            $record = MortuaryRecord::create(array_merge(
                $request->only([
                    'deceased_name', 'deceased_dob', 'gender', 'cause_of_death',
                    'date_of_death', 'time_of_death', 'brought_by', 'brought_by_phone',
                    'relationship', 'storage_location', 'storage_date', 'notes',
                ]),
                [
                    'business_id'    => $business_id,
                    'body_reference' => $body_reference,
                    'status'         => 'stored',
                ]
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'msg'     => 'Mortuary record created.',
                'data'    => ['id' => $record->id, 'body_reference' => $record->body_reference],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark a body as released.
     */
    public function release(Request $request, $id)
    {
        if (! auth()->user()->can('hospital.mortuary') && ! auth()->user()->can('hospital.admin')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        $request->validate([
            'released_to'       => 'required|string|max:150',
            'released_to_phone' => 'nullable|string|max:30',
            'release_date'      => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $business_id = session()->get('user.business_id');
            $record      = MortuaryRecord::where('business_id', $business_id)->findOrFail($id);

            $record->update([
                'status'            => 'released',
                'released_to'       => $request->released_to,
                'released_to_phone' => $request->released_to_phone,
                'release_date'      => $request->release_date,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'msg' => 'Body released successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}
