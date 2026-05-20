<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApplyLoanRequest;
use App\Http\Requests\Api\UploadDocumentRequest;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanPayment;
use App\Models\Setting;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    use ApiResponse;

    public function paymentBank(): JsonResponse
    {
        return $this->success('Payment bank details retrieved.', [
            'bank' => [
                'bank_name'      => Setting::get('payment_bank_name', ''),
                'account_number' => Setting::get('payment_account_number', ''),
                'ifsc_code'      => Setting::get('payment_ifsc_code', ''),
                'holder_name'    => Setting::get('payment_holder_name', ''),
            ],
        ]);
    }

    public function submitPayment(Request $request, int $loanId): JsonResponse
    {
        $user     = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (! $customer) return $this->notFound('Loan not found.');

        $loan = Loan::where('id', $loanId)->where('customer_id', $customer->id)->first();
        if (! $loan) return $this->notFound('Loan not found.');

        if ($loan->status !== 'approved') {
            return $this->error('Payment can only be submitted for approved loans.', 422);
        }

        $existing = LoanPayment::where('loan_id', $loanId)
            ->whereIn('status', ['pending', 'approved'])->first();
        if ($existing) {
            return $this->error('A payment has already been submitted for this loan.', 422);
        }

        if (! $request->hasFile('screenshot')) {
            return $this->error('Please upload a payment screenshot.', 422);
        }

        $file    = $request->file('screenshot');
        $dir     = public_path("uploads/payments/{$loan->id}");
        if (! file_exists($dir)) mkdir($dir, 0755, true);
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $file->move($dir, $filename);
        $path = "uploads/payments/{$loan->id}/{$filename}";

        $payment = LoanPayment::create([
            'loan_id'         => $loan->id,
            'customer_id'     => $customer->id,
            'screenshot_path' => $path,
            'status'          => 'pending',
        ]);

        return $this->success('Payment submitted successfully. Admin will verify within 24 hours.', [
            'payment' => $this->formatPayment($payment),
        ], 201);
    }

    public function getPayment(int $loanId): JsonResponse
    {
        $user     = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (! $customer) return $this->notFound('Loan not found.');

        $loan = Loan::where('id', $loanId)->where('customer_id', $customer->id)->first();
        if (! $loan) return $this->notFound('Loan not found.');

        $payment = LoanPayment::where('loan_id', $loanId)->latest()->first();

        return $this->success('Payment retrieved.', [
            'payment' => $payment ? $this->formatPayment($payment) : null,
        ]);
    }

    private function formatPayment(LoanPayment $payment): array
    {
        return [
            'id'          => $payment->id,
            'status'      => $payment->status,
            'screenshot'  => asset($payment->screenshot_path),
            'admin_notes' => $payment->admin_notes,
            'submitted_at'=> $payment->created_at->toISOString(),
            'approved_at' => $payment->approved_at?->toISOString(),
        ];
    }

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

        $existingLoan = Loan::where('customer_id', $customer->id)
            ->whereNotIn('status', ['rejected', 'closed'])
            ->first();
        if ($existingLoan) {
            return $this->error(
                'You already have an active loan application. Please wait for it to be completed before applying again.',
                422
            );
        }

        $loan = Loan::create([
            'customer_id'      => $customer->id,
            'loan_type_id'     => $request->loan_type_id ?? null,
            'loan_type'        => $request->loan_type ?? 'personal',
            'amount_requested' => $request->amount_requested,
            'repayment_days'   => $request->repayment_days ?? 6,
            'tenure_months'    => 1,
            'purpose'          => $request->purpose ?? 'Easy Loan',
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

        $query = Loan::with(['documents', 'loanType'])
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

        $loan = Loan::with(['statusHistories', 'documents', 'loanType'])
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
            'loan_type_id'     => $loan->loan_type_id,
            'loan_type'        => $loan->loan_type,
            'loan_type_label'  => $loan->loanType?->name ?? $loan->loan_type_label,
            'repayment_days'   => $loan->repayment_days,
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
            'return_date'      => $loan->return_date?->toDateString(),
            'is_overdue'       => $loan->is_overdue,
            'reviewed_at'      => $loan->reviewed_at?->toISOString(),
            'disbursed_at'     => $loan->disbursed_at?->toISOString(),
        ];
    }
}
