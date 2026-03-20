<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Job;
use App\JobCategory;
use App\JobTemplate;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class JobController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @param  ModuleUtil  $moduleUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('job.view') && !auth()->user()->can('job.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $jobs = Job::where('job_cards.business_id', $business_id)
                ->leftJoin('business_locations as bl', 'job_cards.location_id', '=', 'bl.id')
                ->leftJoin('contacts as c', 'job_cards.contact_id', '=', 'c.id')
                ->leftJoin('users as u', 'job_cards.assigned_to', '=', 'u.id')
                ->leftJoin('job_categories as cat', 'job_cards.category_id', '=', 'cat.id')
                ->select([
                    'job_cards.id',
                    'job_cards.ref_no',
                    'job_cards.title',
                    'job_cards.priority',
                    'job_cards.status',
                    'job_cards.due_date',
                    'bl.name as location_name',
                    'c.name as contact_name',
                    'cat.name as category_name',
                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as assigned_to_name")
                ]);

            if (!empty(request()->status)) {
                $jobs->where('job_cards.status', request()->status);
            }
            if (!empty(request()->priority)) {
                $jobs->where('job_cards.priority', request()->priority);
            }
            if (!empty(request()->location_id)) {
                $jobs->where('job_cards.location_id', request()->location_id);
            }
            if (!empty(request()->contact_id)) {
                $jobs->where('job_cards.contact_id', request()->contact_id);
            }

            return Datatables::of($jobs)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        if (auth()->user()->can("job.view")) {
                            $html .= '<li><a href="' . action([\App\Http\Controllers\JobController::class, 'show'], [$row->id]) . '"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        }

                        if (auth()->user()->can("job.update")) {
                            $html .= '<li><a href="' . action([\App\Http\Controllers\JobController::class, 'edit'], [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }

                        if (auth()->user()->can("job.delete")) {
                            $html .= '<li><a href="' . action([\App\Http\Controllers\JobController::class, 'destroy'], [$row->id]) . '" class="delete_job_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->editColumn('status', function($row) {
                    $status_color = [
                        'pending' => 'bg-yellow',
                        'assigned' => 'bg-aqua',
                        'in_progress' => 'bg-blue',
                        'completed' => 'bg-green',
                        'approved' => 'bg-navy',
                        'cancelled' => 'bg-red'
                    ];
                    $label = __('job.' . $row->status);
                    $color = isset($status_color[$row->status]) ? $status_color[$row->status] : 'bg-gray';
                    return '<span class="label ' . $color . '">' . $label . '</span>';
                })
                ->editColumn('priority', function($row) {
                    $priority_color = [
                        'low' => 'bg-gray',
                        'medium' => 'bg-aqua',
                        'high' => 'bg-orange',
                        'urgent' => 'bg-red'
                    ];
                    $label = __('job.' . $row->priority);
                    $color = isset($priority_color[$row->priority]) ? $priority_color[$row->priority] : 'bg-gray';
                    return '<span class="label ' . $color . '">' . $label . '</span>';
                })
                ->editColumn('due_date', '{{@format_datetime($due_date)}}')
                ->removeColumn('id')
                ->rawColumns(['action', 'status', 'priority'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $contacts = Contact::customersDropdown($business_id, false);
        
        $statuses = [
            'pending' => __('job.pending'),
            'assigned' => __('job.assigned'),
            'in_progress' => __('job.in_progress'),
            'completed' => __('job.completed'),
            'approved' => __('job.approved'),
            'cancelled' => __('job.cancelled'),
        ];

        $priorities = [
            'low' => __('job.low'),
            'medium' => __('job.medium'),
            'high' => __('job.high'),
            'urgent' => __('job.urgent'),
        ];

        return view('job.index')->with(compact('business_locations', 'contacts', 'statuses', 'priorities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('job.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id);
        $contacts = Contact::customersDropdown($business_id, false);
        $categories = JobCategory::where('business_id', $business_id)->pluck('name', 'id');
        $templates = JobTemplate::where('business_id', $business_id)->pluck('name', 'id');
        $users = User::forDropdown($business_id, false);

        $priorities = [
            'low' => __('job.low'),
            'medium' => __('job.medium'),
            'high' => __('job.high'),
            'urgent' => __('job.urgent'),
        ];

        return view('job.create')->with(compact('business_locations', 'contacts', 'categories', 'templates', 'users', 'priorities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('job.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only([
                'location_id', 'contact_id', 'category_id', 'template_id', 'title', 
                'description', 'priority', 'due_date', 'assigned_to', 'estimated_hours', 
                'estimated_cost', 'requires_approval'
            ]);
            
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');
            $input['status'] = !empty($input['assigned_to']) ? 'assigned' : 'pending';
            $input['ref_no'] = Job::generateRefNo($business_id);
            $input['due_date'] = !empty($input['due_date']) ? $this->commonUtil->uf_date_datetime($input['due_date']) : null;
            $input['requires_approval'] = !empty($input['requires_approval']) ? 1 : 0;

            if (!empty($input['template_id'])) {
                $template = JobTemplate::findOrFail($input['template_id']);
                $job = Job::createFromTemplate($template, $input);
            } else {
                $job = Job::create($input);
            }

            $output = ['success' => true,
                'msg' => __('job.added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->action([\App\Http\Controllers\JobController::class, 'index'])->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('job.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $job = Job::where('business_id', $business_id)
            ->with(['location', 'contact', 'category', 'assignedUser', 'checklists', 'logs', 'images'])
            ->findOrFail($id);

        return view('job.show')->with(compact('job'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('job.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $job = Job::where('business_id', $business_id)->findOrFail($id);

        $business_locations = BusinessLocation::forDropdown($business_id);
        $contacts = Contact::customersDropdown($business_id, false);
        $categories = JobCategory::where('business_id', $business_id)->pluck('name', 'id');
        $users = User::forDropdown($business_id, false);

        $priorities = [
            'low' => __('job.low'),
            'medium' => __('job.medium'),
            'high' => __('job.high'),
            'urgent' => __('job.urgent'),
        ];

        $statuses = [
            'pending' => __('job.pending'),
            'assigned' => __('job.assigned'),
            'in_progress' => __('job.in_progress'),
            'completed' => __('job.completed'),
            'approved' => __('job.approved'),
            'cancelled' => __('job.cancelled'),
        ];

        return view('job.edit')->with(compact('job', 'business_locations', 'contacts', 'categories', 'users', 'priorities', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('job.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only([
                'location_id', 'contact_id', 'category_id', 'title', 
                'description', 'priority', 'status', 'due_date', 'assigned_to', 
                'estimated_hours', 'actual_hours', 'estimated_cost', 'actual_cost',
                'worker_notes', 'admin_notes', 'requires_approval'
            ]);
            
            $business_id = $request->session()->get('user.business_id');
            $job = Job::where('business_id', $business_id)->findOrFail($id);

            $input['due_date'] = !empty($input['due_date']) ? $this->commonUtil->uf_date_datetime($input['due_date']) : null;
            $input['requires_approval'] = !empty($input['requires_approval']) ? 1 : 0;

            $job->update($input);

            $output = ['success' => true,
                'msg' => __('job.updated_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->action([\App\Http\Controllers\JobController::class, 'index'])->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('job.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $job = Job::where('business_id', $business_id)->findOrFail($id);
                $job->delete();

                $output = ['success' => true,
                    'msg' => __('job.deleted_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Toggle checklist item status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleChecklist($id)
    {
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $checklist = \App\JobChecklist::join('jobs', 'job_checklists.job_id', '=', 'jobs.id')
                    ->where('jobs.business_id', $business_id)
                    ->select('job_checklists.*')
                    ->findOrFail($id);

                $user_id = request()->session()->get('user.id');
                
                if ($checklist->is_completed) {
                    $checklist->update([
                        'is_completed' => false,
                        'completed_at' => null,
                        'completed_by' => null,
                    ]);
                } else {
                    $checklist->markCompleted($user_id);
                }

                $output = ['success' => true,
                    'msg' => __('job.updated_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
}
