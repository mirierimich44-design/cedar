<?php

namespace App\Http\Controllers;

use App\Approval;
use App\ApprovalFlow;
use App\ApprovalFlowStep;
use App\ApprovalDecision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ApprovalController extends Controller
{
    // ── Inbox: all pending approvals awaiting MY action ────────────
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $user = auth()->user();

        $all = Approval::where('business_id', $business_id)
            ->with(['requester', 'flow', 'decisions.decider'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Split into mine-to-act and others
        $pending_mine = $all->filter(fn($a) => $a->canBeActedOnBy($user));
        $all_pending  = $all->where('status', 'pending');
        $history      = $all->whereIn('status', ['approved', 'rejected', 'returned']);

        return view('approvals.index', compact('pending_mine', 'all_pending', 'history'));
    }

    // ── Act on an approval (approve / reject / return) ─────────────
    public function decide(Request $request, Approval $approval)
    {
        $request->validate([
            'decision' => 'required|in:approved,rejected,returned',
            'comment'  => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();

        if (!$approval->canBeActedOnBy($user)) {
            return back()->with('status', ['success' => 0, 'msg' => 'You are not authorised to act on this step.']);
        }

        DB::beginTransaction();
        try {
            // Record the decision
            ApprovalDecision::create([
                'approval_id'  => $approval->id,
                'step_number'  => $approval->current_step,
                'decided_by'   => $user->id,
                'decision'     => $request->decision,
                'comment'      => $request->comment,
            ]);

            if ($request->decision === 'approved') {
                $nextStep = $approval->current_step + 1;
                $hasMore  = $approval->flow
                    && $approval->flow->steps()->where('order', $nextStep)->exists();

                if ($hasMore) {
                    $approval->update(['current_step' => $nextStep]);
                } else {
                    // Final approval
                    $approval->update(['status' => 'approved']);
                    $this->onFinalApproval($approval);
                }
            } elseif ($request->decision === 'rejected') {
                $approval->update(['status' => 'rejected']);
            } elseif ($request->decision === 'returned') {
                $approval->update(['status' => 'returned', 'current_step' => 1]);
            }

            DB::commit();
            return redirect()->route('approvals.index')
                ->with('status', ['success' => 1, 'msg' => 'Decision recorded successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('status', ['success' => 0, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    // ── Re-submit a returned approval ──────────────────────────────
    public function resubmit(Request $request, Approval $approval)
    {
        if ($approval->status !== 'returned' || $approval->requested_by !== auth()->id()) {
            return back()->with('status', ['success' => 0, 'msg' => 'Cannot resubmit this request.']);
        }

        $approval->update([
            'status'       => 'pending',
            'current_step' => 1,
            'notes'        => $request->notes ?? $approval->notes,
        ]);

        return redirect()->route('approvals.index')
            ->with('status', ['success' => 1, 'msg' => 'Request resubmitted for approval.']);
    }

    // ── Flows management ───────────────────────────────────────────
    public function flows()
    {
        $business_id = request()->session()->get('user.business_id');
        $flows = ApprovalFlow::where('business_id', $business_id)
            ->with('steps')
            ->get();
        $roles = Role::all();
        return view('approvals.flows', compact('flows', 'roles'));
    }

    public function storeFlow(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'approvable_type'=> 'required|string|max:255',
            'steps'          => 'required|array|min:1',
        ]);

        $business_id = $request->session()->get('user.business_id');

        DB::beginTransaction();
        try {
            $flow = ApprovalFlow::create([
                'business_id'     => $business_id,
                'name'            => $request->name,
                'approvable_type' => $request->approvable_type,
                'is_active'       => true,
            ]);

            foreach ($request->steps as $i => $step) {
                ApprovalFlowStep::create([
                    'approval_flow_id' => $flow->id,
                    'role_id'          => $step['role_id'] ?? null,
                    'user_id'          => $step['user_id'] ?? null,
                    'label'            => $step['label'] ?? null,
                    'order'            => $i + 1,
                ]);
            }

            DB::commit();
            return redirect()->route('approvals.flows')
                ->with('status', ['success' => 1, 'msg' => 'Approval flow created.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('status', ['success' => 0, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroyFlow(ApprovalFlow $flow)
    {
        $flow->delete();
        return back()->with('status', ['success' => 1, 'msg' => 'Flow deleted.']);
    }

    // ── Static helper: submit any model for approval ───────────────
    public static function submit($model, string $title, int $business_id, ?int $flow_id = null): Approval
    {
        // Auto-detect flow if not specified
        if (!$flow_id) {
            $flow = ApprovalFlow::where('business_id', $business_id)
                ->where('approvable_type', get_class($model))
                ->where('is_active', true)
                ->first();
            $flow_id = $flow?->id;
        }

        return Approval::create([
            'business_id'      => $business_id,
            'approvable_type'  => get_class($model),
            'approvable_id'    => $model->id,
            'title'            => $title,
            'requested_by'     => auth()->id(),
            'approval_flow_id' => $flow_id,
            'current_step'     => 1,
            'status'           => 'pending',
        ]);
    }

    // ── Called when all steps are approved ─────────────────────────
    private function onFinalApproval(Approval $approval): void
    {
        // If the approvable model has an onApproved() method, call it
        $model = $approval->approvable;
        if ($model && method_exists($model, 'onApproved')) {
            $model->onApproved($approval);
        }
    }
}
