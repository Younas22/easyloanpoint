<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanType;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class LoanTypeController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $types = LoanType::active()->get()->map(fn ($t) => [
            'id'             => $t->id,
            'name'           => $t->name,
            'description'    => $t->description,
            'amount'         => (float) $t->amount,
            'repayment_days' => $t->repayment_days,
        ]);

        return $this->success('Loan types retrieved.', ['loan_types' => $types]);
    }
}
