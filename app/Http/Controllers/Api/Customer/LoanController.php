<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApplyLoanRequest;
use App\Http\Requests\Api\UploadDocumentRequest;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    use ApiResponse;

    public function apply(ApplyLoanRequest $request): JsonResponse
    {
        $user     = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (! $customer) {
            return $this->error('Please complete your profile before applying for a loan.', 422);
        }

        if (! $customer->isActive()) {
            return $this->error('Your account is not active. Please contact support.', 403);
        }

        $loan = Loan::create([
            'customer_id'      => $customer->id,
            'loan_type'        => $request->loan_type,
            'amount_requested' => $request->amount_requested,
            'tenure_months'    => $request->tenure_months,
            'purpose'          => $request->purpose,
            'status'           => 'pending',
            'applied_at'       => now(),
        ]);

        return $this->success('Loan application submitted successfully.', [
            'loan' => $this->formatLoan($loan),
        ], 201);
    }

    public function uploadDocument(UploadDocumentRequest $request, int $loanId, string $type): JsonResponse
    {
        $validTypes = ['aadhaar', 'pan', 'selfie'];

        if (! in_array($type, $validTypes)) {
            return $this->error('Invalid document type. Allowed: aadhaar, pan, selfie.', 422);
        }

        $user     = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (! $customer) {
            return $this->notFound('Customer profile not found.');
        }

        $loan = Loan::where('id', $loanId)
            ->where('customer_id', $customer->id)
            ->first();

        if (! $loan) {
            return $this->notFound('Loan not found.');
        }

        $dir = public_path("uploads/documents/{$loan->id}/{$type}");

        if (! file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $file     = $request->file('file');
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $file->move($dir, $filename);

        $filePath = "uploads/documents/{$loan->id}/{$type}/{$filename}";

        // Replace existing document of same type
        LoanDocument::where('loan_id', $loan->id)
            ->where('document_type', $type)
            ->delete();

        $document = LoanDocument::create([
            'loan_id'       => $loan->id,
            'document_type' => $type,
            'file_path'     => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'status'        => 'pending',
        ]);

        return $this->success(ucfirst($type) . ' uploaded successfully.', [
            'document' => [
                'id'          => $document->id,
                'type'        => $document->document_type,
                'type_label'  => $document->document_type_label,
                'url'         => asset("public/{$filePath}"),
                'status'      => $document->status,
                'uploaded_at' => $document->created_at->toISOString(),
            ],
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $user     = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (! $customer) {
            return $this->success('No loan history found.', [
                'loans' => [],
                'meta'  => ['total' => 0, 'current_page' => 1, 'last_page' => 1, 'per_page' => 10],
            ]);
        }

        $query = Loan::with('documents')
            ->where('customer_id', $customer->id)
            ->orderByDesc('applied_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('loan_type')) {
            $query->where('loan_type', $request->loan_type);
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $loans   = $query->paginate($perPage);

        return $this->success('Loan history retrieved.', [
            'loans' => collect($loans->items())->map(fn ($l) => $this->formatLoan($l))->values(),
            'meta'  => [
                'current_page' => $loans->currentPage(),
                'last_page'    => $loans->lastPage(),
                'per_page'     => $loans->perPage(),
                'total'        => $loans->total(),
            ],
        ]);
    }

    public function statusTracking(int $loanId): JsonResponse
    {
        $user     = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (! $customer) {
            return $this->notFound('Loan not found.');
        }

        $loan = Loan::with(['statusHistories', 'documents'])
            ->where('id', $loanId)
            ->where('customer_id', $customer->id)
            ->first();

        if (! $loan) {
            return $this->notFound('Loan not found.');
        }

        $timeline = $loan->statusHistories->map(fn ($h) => [
            'from_status' => $h->from_status,
            'to_status'   => $h->to_status,
            'remarks'     => $h->remarks,
            'changed_at'  => $h->created_at->toISOString(),
        ]);

        $documents = $loan->documents->map(fn ($d) => [
            'type'         => $d->document_type,
            'type_label'   => $d->document_type_label,
            'url'          => asset("public/{$d->file_path}"),
            'status'       => $d->status,
            'status_label' => ucfirst($d->status),
            'remarks'      => $d->remarks,
        ]);

        return $this->success('Loan status retrieved.', [
            'loan'      => $this->formatLoan($loan),
            'timeline'  => $timeline,
            'documents' => $documents,
        ]);
    }

    private function formatLoan(Loan $loan): array
    {
        return [
            'id'               => $loan->id,
            'loan_number'      => $loan->loan_number,
            'loan_type'        => $loan->loan_type,
            'loan_type_label'  => $loan->loan_type_label,
            'amount_requested' => (float) $loan->amount_requested,
            'amount_approved'  => $loan->amount_approved ? (float) $loan->amount_approved : null,
            'interest_rate'    => $loan->interest_rate ? (float) $loan->interest_rate : null,
            'tenure_months'    => $loan->tenure_months,
            'emi_amount'       => $loan->emi_amount,
            'total_payable'    => $loan->total_payable,
            'purpose'          => $loan->purpose,
            'status'           => $loan->status,
            'status_label'     => $loan->status_label,
            'remarks'          => $loan->remarks,
            'applied_at'       => $loan->applied_at?->toISOString(),
            'reviewed_at'      => $loan->reviewed_at?->toISOString(),
            'disbursed_at'     => $loan->disbursed_at?->toISOString(),
        ];
    }
}
