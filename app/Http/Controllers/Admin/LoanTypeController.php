<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoanTypeController extends Controller
{
    public function index()
    {
        $loanTypes = LoanType::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.loan-types.index', compact('loanTypes'));
    }

    public function create()
    {
        return view('admin.loan-types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:100', Rule::unique('loan_types', 'name')],
            'description'    => ['nullable', 'string', 'max:500'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'repayment_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'status'         => ['required', 'in:active,inactive'],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
        ]);

        LoanType::create($data);

        return redirect()->route('admin.loan-types.index')
            ->with('success', 'Loan type created successfully.');
    }

    public function edit(LoanType $loanType)
    {
        return view('admin.loan-types.edit', compact('loanType'));
    }

    public function update(Request $request, LoanType $loanType)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:100', Rule::unique('loan_types', 'name')->ignore($loanType->id)],
            'description'    => ['nullable', 'string', 'max:500'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'repayment_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'status'         => ['required', 'in:active,inactive'],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
        ]);

        $loanType->update($data);

        return redirect()->route('admin.loan-types.index')
            ->with('success', 'Loan type updated successfully.');
    }

    public function destroy(LoanType $loanType)
    {
        if ($loanType->loans()->exists()) {
            return back()->with('error', 'Cannot delete: this loan type has existing loan applications.');
        }

        $loanType->delete();
        return back()->with('success', 'Loan type deleted.');
    }

    public function toggleStatus(LoanType $loanType)
    {
        $loanType->update([
            'status' => $loanType->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Status updated.');
    }
}
