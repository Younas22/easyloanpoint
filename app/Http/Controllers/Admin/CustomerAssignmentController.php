<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssignmentRequest;
use App\Models\Customer;
use App\Models\CustomerAssignment;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerAssignmentController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index(Request $request): View
    {
        $hrUsers = User::where('role', 'hr')
            ->where('status', true)
            ->withCount(['customerAssignments as active_customers_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $query = Customer::with(['currentAssignment.hr'])->withCount('activeLoans');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        match ($request->input('filter')) {
            'assigned'   => $query->whereHas('currentAssignment'),
            'unassigned' => $query->whereDoesntHave('currentAssignment'),
            default      => null,
        };

        if ($request->filled('hr_id')) {
            $query->whereHas('currentAssignment', fn ($q) =>
                $q->where('hr_id', $request->integer('hr_id'))->where('is_active', true)
            );
        }

        $customers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.assignments.index', compact('customers', 'hrUsers'));
    }

    public function store(StoreAssignmentRequest $request): JsonResponse
    {
        $customer = Customer::findOrFail($request->integer('customer_id'));
        $hr       = User::findOrFail($request->integer('hr_id'));

        CustomerAssignment::where('customer_id', $customer->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $assignment = CustomerAssignment::create([
            'customer_id' => $customer->id,
            'hr_id'       => $hr->id,
            'assigned_by' => auth()->id(),
            'notes'       => $request->input('notes'),
            'is_active'   => true,
        ]);

        $customer->loans()
            ->whereIn('status', ['pending', 'under_review'])
            ->update(['assigned_hr_id' => $hr->id]);

        $this->notifications->notifyCustomerAssigned($customer, $hr, auth()->user());

        return response()->json([
            'status'  => true,
            'message' => "Customer assigned to {$hr->name} successfully.",
            'data'    => [
                'assignment_id' => $assignment->id,
                'hr_name'       => $hr->name,
                'assigned_at'   => $assignment->created_at->format('d M Y, h:i A'),
            ],
        ]);
    }

    public function history(Customer $customer): JsonResponse
    {
        $history = CustomerAssignment::where('customer_id', $customer->id)
            ->with(['hr:id,name', 'assignedBy:id,name'])
            ->latest()
            ->get()
            ->map(fn ($a) => [
                'id'          => $a->id,
                'hr_name'     => $a->hr?->name ?? 'Unknown',
                'assigned_by' => $a->assignedBy?->name ?? 'System',
                'notes'       => $a->notes,
                'is_active'   => $a->is_active,
                'assigned_at' => $a->created_at->format('d M Y, h:i A'),
            ]);

        return response()->json([
            'status' => true,
            'data'   => [
                'customer_name' => $customer->name,
                'history'       => $history,
            ],
        ]);
    }

    public function hrStats(): JsonResponse
    {
        $stats = User::where('role', 'hr')
            ->where('status', true)
            ->withCount(['customerAssignments as active_customers_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get()
            ->map(fn ($hr) => [
                'id'             => $hr->id,
                'name'           => $hr->name,
                'customer_count' => $hr->active_customers_count,
            ]);

        return response()->json(['status' => true, 'data' => $stats]);
    }
}
