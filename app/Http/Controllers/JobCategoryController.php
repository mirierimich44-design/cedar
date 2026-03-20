<?php

namespace App\Http\Controllers;

use App\JobCategory;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JobCategoryController extends Controller
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

            $categories = JobCategory::where('business_id', $business_id)
                        ->select(['name', 'short_code', 'description', 'id']);

            return Datatables::of($categories)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'App\Http\Controllers\JobCategoryController@edit\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\JobCategoryController@destroy\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_category_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('job_category.index');
    }

    public function create()
    {
        if (!auth()->user()->can('job.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('job_category.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('job.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'short_code', 'description']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['is_active'] = 1;

            JobCategory::create($input);
            $output = ['success' => true, 'msg' => __('job.updated_success')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    public function edit($id)
    {
        if (!auth()->user()->can('job.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $category = JobCategory::where('business_id', $business_id)->findOrFail($id);

            return view('job_category.edit')->with(compact('category'));
        }
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('job.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'short_code', 'description']);
                $business_id = $request->session()->get('user.business_id');

                $category = JobCategory::where('business_id', $business_id)->findOrFail($id);
                $category->update($input);

                $output = ['success' => true, 'msg' => __('job.updated_success')];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
            }

            return $output;
        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('job.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $category = JobCategory::where('business_id', $business_id)->findOrFail($id);
                $category->delete();

                $output = ['success' => true, 'msg' => __('job.deleted_success')];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
            }

            return $output;
        }
    }
}
