<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HrRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class HrController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'hr');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->input('status') !== '') {
            $query->where('status', (bool) $request->input('status'));
        }

        $hrs = $query->latest()->paginate(10)->withQueryString();

        return view('admin.hr.index', compact('hrs'));
    }

    public function create(): View
    {
        return view('admin.hr.create');
    }

    public function store(HrRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['role']   = 'hr';
        $data['status'] = (bool) $data['status'];

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $this->uploadImage($request->file('profile_image'));
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        unset($data['password_confirmation']);

        User::create($data);

        return redirect()->route('admin.hr.index')
            ->with('success', 'HR member added successfully.');
    }

    public function edit(User $hr): View
    {
        abort_if($hr->role !== 'hr', 404);

        return view('admin.hr.edit', compact('hr'));
    }

    public function update(HrRequest $request, User $hr): RedirectResponse
    {
        abort_if($hr->role !== 'hr', 404);

        $data = $request->validated();
        $data['status'] = (bool) $data['status'];

        if ($request->hasFile('profile_image')) {
            $this->deleteImage($hr->profile_image);
            $data['profile_image'] = $this->uploadImage($request->file('profile_image'));
        } else {
            unset($data['profile_image']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        unset($data['password_confirmation']);

        $hr->update($data);

        return redirect()->route('admin.hr.index')
            ->with('success', 'HR member updated successfully.');
    }

    public function destroy(User $hr): RedirectResponse
    {
        abort_if($hr->role !== 'hr', 404);

        $this->deleteImage($hr->profile_image);
        $hr->delete();

        return redirect()->route('admin.hr.index')
            ->with('success', 'HR member deleted successfully.');
    }

    public function toggleStatus(User $hr)
    {
        abort_if($hr->role !== 'hr', 404);

        $hr->update(['status' => ! $hr->status]);

        return response()->json([
            'status'  => true,
            'message' => 'Status updated successfully.',
            'active'  => $hr->status,
        ]);
    }

    private function uploadImage($file): string
    {
        $dir = public_path('uploads/hr');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return 'uploads/hr/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
