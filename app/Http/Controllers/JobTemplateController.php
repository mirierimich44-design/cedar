<?php

namespace App\Http\Controllers;

use App\JobCategory;
use App\JobTemplate;
use App\JobTemplateItem;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JobTemplateController extends Controller
{
    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    public function index()
    {
        if (!auth()->user()->can('job.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $templates = JobTemplate::where('job_templates.business_id', $business_id)
                        ->leftJoin('job_categories as cat', 'job_templates.category_id', '=', 'cat.id')
                        ->select(['job_templates.name', 'cat.name as category_name', 'job_templates.estimated_hours', 'job_templates.estimated_cost', 'job_templates.id']);

            return Datatables::of($templates)
                ->addColumn(
                    'action',
                    '<a href="{{action(\'App\Http\Controllers\JobTemplateController@edit\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
                    &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\JobTemplateController@destroy\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_template_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('job_template.index');
    }

    public function create()
    {
        if (!auth()->user()->can('job.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $categories = JobCategory::where('business_id', $business_id)->pluck('name', 'id');

        return view('job_template.create')->with(compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('job.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'category_id', 'description', 'estimated_hours', 'estimated_cost', 'requires_approval']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['requires_approval'] = !empty($input['requires_approval']) ? 1 : 0;
            $input['is_active'] = 1;

            $template = JobTemplate::create($input);

            // Add checklist items if any
            if (!empty($request->items)) {
                foreach ($request->items as $item) {
                    if (!empty($item['title'])) {
                        JobTemplateItem::create([
                            'job_template_id' => $template->id,
                            'title' => $item['title'],
                            'description' => $item['description'] ?? null,
                            'sort_order' => $item['sort_order'] ?? 0
                        ]);
                    }
                }
            }

            $output = ['success' => true, 'msg' => __('job.updated_success')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return redirect()->action([\App\Http\Controllers\JobTemplateController::class, 'index'])->with('status', $output);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('job.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $template = JobTemplate::where('business_id', $business_id)->with('items')->findOrFail($id);
        $categories = JobCategory::where('business_id', $business_id)->pluck('name', 'id');

        return view('job_template.edit')->with(compact('template', 'categories'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('job.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'category_id', 'description', 'estimated_hours', 'estimated_cost', 'requires_approval']);
            $business_id = $request->session()->get('user.business_id');
            $template = JobTemplate::where('business_id', $business_id)->findOrFail($id);
            
            $input['requires_approval'] = !empty($input['requires_approval']) ? 1 : 0;
            $template->update($input);

            // Update checklist items
            JobTemplateItem::where('job_template_id', $template->id)->delete();
            if (!empty($request->items)) {
                foreach ($request->items as $item) {
                    if (!empty($item['title'])) {
                        JobTemplateItem::create([
                            'job_template_id' => $template->id,
                            'title' => $item['title'],
                            'description' => $item['description'] ?? null,
                            'sort_order' => $item['sort_order'] ?? 0
                        ]);
                    }
                }
            }

            $output = ['success' => true, 'msg' => __('job.updated_success')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return redirect()->action([\App\Http\Controllers\JobTemplateController::class, 'index'])->with('status', $output);
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('job.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $template = JobTemplate::where('business_id', $business_id)->findOrFail($id);
                $template->delete();

                $output = ['success' => true, 'msg' => __('job.deleted_success')];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
            }

            return $output;
        }
    }
}
