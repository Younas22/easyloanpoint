<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLoanStatusRequest;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    private function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Loan::with(['customer', 'assignedHR'])
            ->where('assigned_hr_id', auth()->id())
            ->latest('applied_at');
    }

    public function index(Request $request)
    {
        $query = $this->baseQuery();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('loan_number', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($c) =>
                      $c->where('name', 'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%")
                  );
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('loan_type')) {
            $query->where('loan_type', $request->loan_type);
        }

        $loans = $query->paginate(15)->withQueryString();

        $base = fn() => Loan::where('assigned_hr_id', auth()->id());
        $stats = [
            'total'        => $base()->count(),
            'pending'      => $base()->where('status', 'pending')->count(),
            'under_review' => $base()->where('status', 'under_review')->count(),
            'approved'     => $base()->where('status', 'approved')->count(),
            'disbursed'    => $base()->where('status', 'disbursed')->count(),
        ];

        return view('hr.loans.index', compact('loans', 'stats'));
    }

    public function show(Loan $loan)
    {
        if ($loan->assigned_hr_id !== auth()->id()) {
            abort(403, 'You are not assigned to this loan.');
        }

        $loan->load(['customer', 'assignedHR', 'statusHistories.changedBy', 'documents.verifiedBy']);

        return view('hr.loans.show', compact('loan'));
    }

    public function updateStatus(UpdateLoanStatusRequest $request, Loan $loan)
    {
        if ($loan->assigned_hr_id !== auth()->id()) {
            abort(403);
        }

        $allowed = ['under_review', 'approved', 'rejected'];
        if (!in_array($request->status, $allowed)) {
            return back()->with('error', 'You can only set status to Under Review, Approved, or Rejected.');
        }

        $fromStatus = $loan->status;
        $toStatus   = $request->status;

        if ($fromStatus === $toStatus) {
            return back()->with('error', 'Status is already ' . $loan->status_label . '.');
        }

        DB::transaction(function () use ($request, $loan, $fromStatus, $toStatus) {
            $updates = ['status' => $toStatus, 'remarks' => $request->remarks];

            if (in_array($toStatus, ['under_review', 'approved'])) {
                $updates['reviewed_at'] = now();
                if ($request->filled('amount_approved')) {
                    $updates['amount_approved'] = $request->amount_approved;
                }
                if ($request->filled('interest_rate')) {
                    $updates['interest_rate'] = $request->interest_rate;
                }
            }

            $loan->update($updates);

            $loan->statusHistories()->create([
                'changed_by'  => auth()->id(),
                'from_status' => $fromStatus,
                'to_status'   => $toStatus,
                'remarks'     => $request->remarks,
            ]);
        });

        return back()->with('success', "Loan status updated to " . $loan->fresh()->status_label . ".");
    }

    public function verifyDocument(Request $request, Loan $loan)
    {
        if ($loan->assigned_hr_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'document_id' => ['required', 'exists:loan_documents,id'],
            'status'      => ['required', 'in:verified,rejected'],
            'remarks'     => ['nullable', 'string', 'max:500'],
        ]);

        $doc = $loan->documents()->findOrFail($request->document_id);
        $doc->update([
            'status'      => $request->status,
            'remarks'     => $request->remarks,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Document updated to ' . ucfirst($request->status) . '.');
    }
}
