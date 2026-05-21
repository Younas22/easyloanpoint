<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PanelPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $permissions = PanelPermission::orderBy('panel')->orderBy('id')->get()->groupBy('panel');

        return view('admin.permissions.index', compact('permissions'));
    }

    public function toggle(PanelPermission $permission): JsonResponse
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $permission->update(['is_hidden' => ! $permission->is_hidden]);
        PanelPermission::bustCache();

        return response()->json([
            'status'    => true,
            'is_hidden' => $permission->is_hidden,
        ]);
    }
}
