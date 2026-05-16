<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    private function assignedIds(): \Illuminate\Support\Collection
    {
        return CustomerAssignment::where('hr_id', Auth::id())
            ->where('is_active', true)
            ->pluck('customer_id');
    }

    public function index(Request $request)
    {
        $ids = $this->assignedIds();

        $query = Customer::whereIn('id', $ids)
            ->with(['loans' => fn ($q) => $q->latest('applied_at')->take(1)])
            ->withCount('loans');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('loan_status')) {
            $query->whereHas('loans', fn ($q) => $q->where('status', $request->loan_status));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'      => $ids->count(),
            'active'     => Customer::whereIn('id', $ids)->where('status', 'active')->count(),
            'with_loans' => Customer::whereIn('id', $ids)->whereHas('loans')->count(),
            'pending'    => Customer::whereIn('id', $ids)
                                ->whereHas('loans', fn ($q) => $q->where('status', 'pending'))
                                ->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('hr.customers._table', compact('customers'))->render(),
                'pagination' => $customers->links()->toHtml(),
            ]);
        }

        return view('hr.customers.index', compact('customers', 'stats'));
    }

    public function show(Customer $customer)
    {
        $assigned = CustomerAssignment::where('hr_id', Auth::id())
            ->where('customer_id', $customer->id)
            ->where('is_active', true)
            ->exists();

        abort_unless($assigned, 403, 'You are not assigned to this customer.');

        $customer->load([
            'loans.documents',
            'loans.statusHistories',
        ]);

        $assignment = CustomerAssignment::where('hr_id', Auth::id())
            ->where('customer_id', $customer->id)
            ->where('is_active', true)
            ->with('assignedBy')
            ->first();

        return view('hr.customers.show', compact('customer', 'assignment'));
    }

    public function addRemark(Request $request, Customer $customer)
    {
        $assigned = CustomerAssignment::where('hr_id', Auth::id())
            ->where('customer_id', $customer->id)
            ->where('is_active', true)
            ->exists();

        abort_unless($assigned, 403);

        $request->validate([
            'remark' => ['required', 'string', 'max:1000'],
        ]);

        $existing   = $customer->notes ? trim($customer->notes) . "\n\n" : '';
        $timestamp  = now()->format('d M Y, h:i A');
        $hrName     = Auth::user()->name;

        $customer->update([
            'notes' => $existing . "[{$timestamp} — {$hrName}]: " . $request->remark,
        ]);

        return back()->with('success', 'Remark added successfully.');
    }
}
