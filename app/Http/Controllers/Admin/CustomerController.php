<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignCustomerRequest;
use App\Http\Requests\Admin\CustomerRequest;
use App\Models\Customer;
use App\Models\CustomerAssignment;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    private const INDIAN_STATES = [
        'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
        'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka',
        'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya',
        'Mizoram', 'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 'Sikkim',
        'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand',
        'West Bengal', 'Delhi', 'Jammu & Kashmir', 'Ladakh',
        'Chandigarh', 'Puducherry',
    ];

    public function index(Request $request): View
    {
        $query = Customer::with(['currentAssignment.hr', 'loans']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('aadhaar_number', 'like', "%{$search}%")
                  ->orWhere('pan_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->input('employment_type'));
        }

        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        $customers = $query->latest()->paginate(15)->withQueryString();
        $states    = self::INDIAN_STATES;

        return view('admin.customers.index', compact('customers', 'states'));
    }

    public function create(): View
    {
        $states = self::INDIAN_STATES;
        return view('admin.customers.create', compact('states'));
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        $data               = $request->validated();
        $data['created_by'] = auth()->id();

        $customer = Customer::create($data);

        ActivityLogService::customerCreated($customer->name);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer added successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'loans' => fn ($q) => $q->latest()->with('assignedHR'),
            'assignments' => fn ($q) => $q->latest()->with(['hr', 'assignedBy']),
            'creator',
        ]);

        $hrs    = User::where('role', 'hr')->where('status', true)->orderBy('name')->get();
        $states = self::INDIAN_STATES;

        return view('admin.customers.show', compact('customer', 'hrs', 'states'));
    }

    public function edit(Customer $customer): View
    {
        $states = self::INDIAN_STATES;
        return view('admin.customers.edit', compact('customer', 'states'));
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    public function assignHr(AssignCustomerRequest $request, Customer $customer): RedirectResponse
    {
        CustomerAssignment::where('customer_id', $customer->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        CustomerAssignment::create([
            'customer_id' => $customer->id,
            'hr_id'       => $request->hr_id,
            'assigned_by' => auth()->id(),
            'notes'       => $request->notes,
            'is_active'   => true,
        ]);

        $customer->loans()
            ->whereIn('status', ['pending', 'under_review'])
            ->update(['assigned_hr_id' => $request->hr_id]);

        $hr = User::find($request->hr_id);
        ActivityLogService::customerAssigned($customer->name, $hr?->name ?? 'HR');

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer assigned to HR successfully.');
    }

    public function toggleStatus(Customer $customer)
    {
        $newStatus = match($customer->status) {
            'active'   => 'inactive',
            'inactive' => 'active',
            default    => $customer->status,
        };

        $customer->update(['status' => $newStatus]);

        return response()->json([
            'status'     => true,
            'message'    => 'Status updated.',
            'new_status' => $newStatus,
        ]);
    }

    public function updateNote(Request $request, Customer $customer)
    {
        $request->validate(['notes' => ['nullable', 'string', 'max:2000']]);
        $customer->update(['notes' => $request->notes]);

        return response()->json(['status' => true, 'message' => 'Note saved.']);
    }
}
