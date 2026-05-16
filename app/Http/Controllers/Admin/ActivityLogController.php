<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->getFilters($request);
        $logs    = $this->buildQuery($filters)->paginate(20)->withQueryString();

        $stats = [
            'total'   => ActivityLog::count(),
            'today'   => ActivityLog::whereDate('created_at', today())->count(),
            'week'    => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'users'   => ActivityLog::distinct('user_id')->count('user_id'),
        ];

        $users   = User::whereIn('role', ['admin', 'hr'])->orderBy('name')->get(['id', 'name', 'role']);
        $actions = ActivityLog::actions();

        return view('admin.logs.index', compact('logs', 'stats', 'filters', 'users', 'actions'));
    }

    public function ajax(Request $request): JsonResponse
    {
        $filters = $this->getFilters($request);
        $logs    = $this->buildQuery($filters)->paginate(20)->withQueryString();

        $html = view('admin.logs._table', compact('logs', 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    // ── Private ──────────────────────────────────────────────────────────────

    private function getFilters(Request $request): array
    {
        return [
            'search'  => $request->input('search'),
            'role'    => $request->input('role'),
            'action'  => $request->input('action'),
            'date_from' => $request->input('date_from'),
            'date_to'   => $request->input('date_to'),
        ];
    }

    private function buildQuery(array $filters)
    {
        $query = ActivityLog::with('user')->latest();

        if ($filters['search']) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }

        if ($filters['role'])   $query->where('role', $filters['role']);
        if ($filters['action']) $query->where('action', $filters['action']);

        if ($filters['date_from']) $query->whereDate('created_at', '>=', $filters['date_from']);
        if ($filters['date_to'])   $query->whereDate('created_at', '<=', $filters['date_to']);

        return $query;
    }
}
