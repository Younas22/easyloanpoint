<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLoanRequest;
use App\Http\Requests\Admin\UpdateLoanRequest;
use App\Http\Requests\Admin\UpdateLoanStatusRequest;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index(Request $request)
    {
        $query = Loan::with(['customer', 'assignedHR'])->latest('applied_at');

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

        if ($request->filled('hr_id')) {
            $query->where('assigned_hr_id', $request->hr_id);
        }

        $loans   = $query->paginate(15)->withQueryString();
        $hrUsers = User::where('role', 'hr')->where('status', true)->orderBy('name')->get();

        $stats = [
            'total'        => Loan::count(),
            'pending'      => Loan::where('status', 'pending')->count(),
            'under_review' => Loan::where('status', 'under_review')->count(),
            'approved'     => Loan::where('status', 'approved')->count(),
            'rejected'     => Loan::where('status', 'rejected')->count(),
            'disbursed'    => Loan::where('status', 'disbursed')->count(),
        ];

        return view('admin.loans.index', compact('loans', 'hrUsers', 'stats'));
    }

    public function create(Request $request)
    {
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $hrUsers   = User::where('role', 'hr')->where('status', true)->orderBy('name')->get();
        $selected  = $request->filled('customer_id')
            ? Customer::find($request->customer_id)
            : null;

        return view('admin.loans.create', compact('customers', 'hrUsers', 'selected'));
    }

    public function store(StoreLoanRequest $request)
    {
        $loan = Loan::create(array_merge($request->validated(), ['applied_at' => now()]));

        $loan->load('customer');
        $this->notifications->notifyNewLoanApplication($loan);

        return redirect()
            ->route('admin.loans.show', $loan)
            ->with('success', "Loan {$loan->loan_number} created successfully.");
    }

    public function show(Loan $loan)
    {
        $loan->load([
            'customer',
            'assignedHR',
            'statusHistories' => fn ($q) => $q->with('changedBy')->oldest(),
            'documents.verifiedBy',
        ]);
        $hrUsers = User::where('role', 'hr')->where('status', true)->orderBy('name')->get();

        return view('admin.loans.show', compact('loan', 'hrUsers'));
    }

    public function edit(Loan $loan)
    {
        $hrUsers = User::where('role', 'hr')->where('status', true)->orderBy('name')->get();
        return view('admin.loans.edit', compact('loan', 'hrUsers'));
    }

    public function update(UpdateLoanRequest $request, Loan $loan)
    {
        $loan->update($request->validated());

        return redirect()
            ->route('admin.loans.show', $loan)
            ->with('success', 'Loan updated successfully.');
    }

    public function updateStatus(UpdateLoanStatusRequest $request, Loan $loan)
    {
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

            if ($toStatus === 'disbursed') {
                $updates['disbursed_at'] = now();
            }

            $loan->update($updates);

            $loan->statusHistories()->create([
                'changed_by'  => auth()->id(),
                'from_status' => $fromStatus,
                'to_status'   => $toStatus,
                'remarks'     => $request->remarks,
            ]);
        });

        $this->notifications->notifyLoanStatusChanged(
            $loan->load('customer'),
            $fromStatus,
            $toStatus,
            $request->remarks
        );

        return back()->with('success', "Loan status updated to " . $loan->fresh()->status_label . ".");
    }

    public function verifyDocument(Request $request, Loan $loan, LoanDocument $document)
    {
        $request->validate([
            'status'  => ['required', 'in:verified,rejected'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $document->update([
            'status'      => $request->status,
            'remarks'     => $request->remarks,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $this->notifications->notifyDocumentVerified($document->fresh());

        return back()->with('success', 'Document status updated to ' . ucfirst($request->status) . '.');
    }

    public function uploadDocument(Request $request, Loan $loan)
    {
        $request->validate([
            'document_type' => ['required', 'in:aadhaar,pan,selfie,income_proof,bank_statement,other'],
            'file'          => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $file = $request->file('file');
        $dir  = public_path('uploads/loan_documents/' . $loan->id);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $fileName);

        $loan->documents()->create([
            'document_type' => $request->document_type,
            'file_path'     => 'uploads/loan_documents/' . $loan->id . '/' . $fileName,
            'original_name' => $file->getClientOriginalName(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy(Loan $loan)
    {
        if (in_array($loan->status, ['approved', 'disbursed'])) {
            return back()->with('error', 'Cannot delete approved or disbursed loans.');
        }

        $loan->documents->each(function (LoanDocument $doc) {
            $path = public_path($doc->file_path);
            if (file_exists($path)) {
                unlink($path);
            }
        });

        $loan->delete();

        return redirect()
            ->route('admin.loans.index')
            ->with('success', 'Loan deleted successfully.');
    }
}
