<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function send(
        int $userId,
        string $title,
        string $message,
        string $type = 'system',
        ?string $referenceType = null,
        ?int $referenceId = null
    ): Notification {
        return Notification::create([
            'user_id'        => $userId,
            'title'          => $title,
            'message'        => $message,
            'type'           => $type,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
        ]);
    }

    public function sendToRole(
        string $role,
        string $title,
        string $message,
        string $type = 'system',
        ?string $referenceType = null,
        ?int $referenceId = null
    ): void {
        User::where('role', $role)
            ->where('status', true)
            ->pluck('id')
            ->each(fn (int $id) => $this->send($id, $title, $message, $type, $referenceType, $referenceId));
    }

    public function notifyLoanStatusChanged(
        Loan $loan,
        string $fromStatus,
        string $toStatus,
        ?string $remarks = null
    ): void {
        $labels = [
            'pending'      => 'Pending',
            'under_review' => 'Under Review',
            'approved'     => 'Approved',
            'rejected'     => 'Rejected',
            'disbursed'    => 'Disbursed',
        ];

        $from  = $labels[$fromStatus] ?? ucfirst($fromStatus);
        $to    = $labels[$toStatus]   ?? ucfirst($toStatus);
        $title = "Loan Status Updated: {$to}";

        // Notify customer's portal account
        if ($loan->customer && $loan->customer->user_id) {
            $msg = "Your loan #{$loan->loan_number} status has been updated from {$from} to {$to}.";
            if ($remarks) {
                $msg .= " Remarks: {$remarks}";
            }
            $this->send($loan->customer->user_id, $title, $msg, 'loan_status', Loan::class, $loan->id);
        }

        // Notify assigned HR
        if ($loan->assigned_hr_id) {
            $msg = "Loan #{$loan->loan_number} for {$loan->customer->name} has been updated to {$to}.";
            $this->send($loan->assigned_hr_id, $title, $msg, 'loan_status', Loan::class, $loan->id);
        }

        // Notify all admins
        $adminMsg = "Loan #{$loan->loan_number} ({$loan->customer->name}) status changed from {$from} to {$to}.";
        $this->sendToRole('admin', $title, $adminMsg, 'loan_status', Loan::class, $loan->id);
    }

    public function notifyNewLoanApplication(Loan $loan): void
    {
        $amount  = '₹' . number_format($loan->amount_requested);
        $title   = 'New Loan Application';
        $message = "New loan #{$loan->loan_number} received from {$loan->customer->name} for {$amount}.";

        $this->sendToRole('admin', $title, $message, 'loan_status', Loan::class, $loan->id);

        if ($loan->assigned_hr_id) {
            $this->send($loan->assigned_hr_id, $title, $message, 'loan_status', Loan::class, $loan->id);
        }
    }

    public function notifyCustomerAssigned(Customer $customer, User $hr, User $assignedBy): void
    {
        $title = 'New Customer Assigned';

        // Notify the HR
        $this->send(
            $hr->id,
            $title,
            "Customer {$customer->name} has been assigned to you by {$assignedBy->name}.",
            'assignment',
            Customer::class,
            $customer->id
        );

        // Notify all admins
        $this->sendToRole(
            'admin',
            $title,
            "Customer {$customer->name} has been assigned to HR {$hr->name} by {$assignedBy->name}.",
            'assignment',
            Customer::class,
            $customer->id
        );
    }

    public function notifyDocumentVerified(LoanDocument $document): void
    {
        $loan   = $document->load('loan.customer')->loan;
        $status = $document->status === 'verified' ? 'verified' : 'rejected';
        $type   = ucwords(str_replace('_', ' ', $document->document_type));
        $title  = "Document " . ucfirst($status);

        // Notify customer
        if ($loan->customer && $loan->customer->user_id) {
            $this->send(
                $loan->customer->user_id,
                $title,
                "Your {$type} document for loan #{$loan->loan_number} has been {$status}.",
                'document',
                LoanDocument::class,
                $document->id
            );
        }

        // Notify assigned HR
        if ($loan->assigned_hr_id) {
            $this->send(
                $loan->assigned_hr_id,
                $title,
                "{$type} document for loan #{$loan->loan_number} ({$loan->customer->name}) has been {$status}.",
                'document',
                LoanDocument::class,
                $document->id
            );
        }
    }
}
