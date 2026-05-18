<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Models\Customer;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use ApiResponse;

    public function show(Request $request): JsonResponse
    {
        $user     = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        return $this->success('Profile retrieved.', [
            'user'         => $this->formatUser($user),
            'kyc'          => $customer ? $this->formatCustomer($customer) : null,
            'kyc_complete' => ! is_null($customer),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = Auth::user();

        $userFields = $request->only(['name', 'email']);

        if ($request->hasFile('profile_image')) {
            $dir = public_path("uploads/profiles/{$user->id}");

            if (! file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $file     = $request->file('profile_image');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move($dir, $filename);

            $userFields['profile_image'] = "uploads/profiles/{$user->id}/{$filename}";
        }

        if (! empty($userFields)) {
            $user->update($userFields);
        }

        $kycFields = $request->only([
            'aadhaar_number', 'pan_number', 'address', 'city',
            'state', 'pincode', 'dob', 'gender', 'employment_type', 'salary',
        ]);

        $customer = null;

        if (! empty($kycFields)) {
            $customer = Customer::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($kycFields, [
                    'name'       => $user->fresh()->name,
                    'phone'      => $user->phone,
                    'email'      => $user->fresh()->email,
                    'status'     => 'active',
                    'created_by' => $user->id,
                ])
            );
        } else {
            $customer = Customer::where('user_id', $user->id)->first();
        }

        return $this->success('Profile updated successfully.', [
            'user' => $this->formatUser($user->fresh()),
            'kyc'  => $customer ? $this->formatCustomer($customer) : null,
        ]);
    }

    private function formatUser($user): array
    {
        return [
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'phone'          => $user->phone,
            'profile_image'  => $user->profile_image ? url("public/{$user->profile_image}") : null,
            'phone_verified' => ! is_null($user->phone_verified_at),
            'role'           => $user->role,
            'status'         => $user->isActive(),
            'member_since'   => $user->created_at->toISOString(),
        ];
    }

    private function formatCustomer(Customer $customer): array
    {
        return [
            'id'              => $customer->id,
            'aadhaar_number'  => $customer->masked_aadhaar,
            'pan_number'      => $customer->pan_number,
            'address'         => $customer->address,
            'city'            => $customer->city,
            'state'           => $customer->state,
            'pincode'         => $customer->pincode,
            'dob'             => $customer->dob?->toDateString(),
            'gender'          => $customer->gender,
            'employment_type' => $customer->employment_type,
            'employment_label'=> $customer->employment_label,
            'salary'          => $customer->salary ? (float) $customer->salary : null,
            'status'          => $customer->status,
            'status_label'    => $customer->status_label,
        ];
    }
}
