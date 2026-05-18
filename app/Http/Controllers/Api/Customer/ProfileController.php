<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

    public function update(Request $request): JsonResponse
    {
        $user     = Auth::user();
        $input    = $request->all();
        $customer = Customer::where('user_id', $user->id)->first();
        $custId   = $customer?->id;

        $validator = Validator::make($input, [
            'name'             => ['sometimes', 'string', 'min:2', 'max:100'],
            'email'            => ['sometimes', 'nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'profile_image'    => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'aadhaar_number'   => ['sometimes', 'digits:12', Rule::unique('customers', 'aadhaar_number')->ignore($custId)],
            'aadhaar_document' => ['sometimes', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'pan_number'       => ['sometimes', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/i', Rule::unique('customers', 'pan_number')->ignore($custId)],
            'pan_document'     => ['sometimes', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'selfie_document'  => ['sometimes', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'address'          => ['sometimes', 'string', 'max:500'],
            'city'             => ['sometimes', 'string', 'max:100'],
            'state'            => ['sometimes', 'string', 'max:100'],
            'pincode'          => ['sometimes', 'digits:6'],
            'dob'              => ['sometimes', 'date', 'before:today'],
            'gender'           => ['sometimes', 'in:male,female,other'],
            'employment_type'  => ['sometimes', 'in:salaried,self_employed,business,unemployed'],
            'salary'           => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ], [
            'aadhaar_number.digits'   => 'Aadhaar number must be exactly 12 digits.',
            'aadhaar_number.unique'   => 'This Aadhaar number is already registered.',
            'pan_number.regex'        => 'PAN must be in format: ABCDE1234F.',
            'pan_number.unique'       => 'This PAN number is already registered.',
            'pincode.digits'          => 'Pincode must be exactly 6 digits.',
            'dob.before'              => 'Date of birth must be in the past.',
            'profile_image.max'       => 'Profile image must not exceed 2MB.',
            'aadhaar_document.max'    => 'Aadhaar document must not exceed 5MB.',
            'pan_document.max'        => 'PAN document must not exceed 5MB.',
            'selfie_document.max'     => 'Selfie must not exceed 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation errors.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // ── User fields ───────────────────────────────────────────────────────

        $userFields = array_filter([
            'name'  => $input['name']  ?? null,
            'email' => $input['email'] ?? null,
        ], fn ($v) => ! is_null($v) && $v !== '');

        if ($request->hasFile('profile_image')) {
            $userFields['profile_image'] = $this->storeFile(
                $request->file('profile_image'),
                "uploads/profiles/{$user->id}"
            );
        }

        if (! empty($userFields)) {
            $user->update($userFields);
        }

        // ── Normalize aadhaar and PAN before saving ───────────────────────────
        if (isset($input['aadhaar_number'])) {
            $input['aadhaar_number'] = preg_replace('/[\s\-]/', '', $input['aadhaar_number']);
        }
        if (isset($input['pan_number'])) {
            $input['pan_number'] = strtoupper(trim($input['pan_number']));
        }

        // ── KYC fields ────────────────────────────────────────────────────────

        $kycKeys   = ['aadhaar_number', 'pan_number', 'address', 'city', 'state', 'pincode', 'dob', 'gender', 'employment_type', 'salary'];
        $kycFields = array_filter(
            array_intersect_key($input, array_flip($kycKeys)),
            fn ($v) => ! is_null($v) && $v !== ''
        );

        foreach (['aadhaar_document', 'pan_document', 'selfie_document'] as $docKey) {
            if ($request->hasFile($docKey)) {
                $type = str_replace('_document', '', $docKey);
                $kycFields[$docKey] = $this->storeFile(
                    $request->file($docKey),
                    "uploads/kyc/{$user->id}/{$type}"
                );
            }
        }

        if (! empty($kycFields)) {
            $fresh    = $user->fresh();
            $customer = Customer::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($kycFields, [
                    'name'       => $fresh->name,
                    'phone'      => $fresh->phone,
                    'email'      => $fresh->email,
                    'status'     => $customer?->status ?? 'active',
                    'created_by' => $customer?->created_by ?? $user->id,
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

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function storeFile($file, string $subDir): string
    {
        $dir = public_path($subDir);

        if (! file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $file->move($dir, $filename);

        return "{$subDir}/{$filename}";
    }

    private function formatUser($user): array
    {
        return [
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'phone'          => $user->phone,
            'profile_image'  => $user->profile_image ? asset($user->profile_image) : null,
            'phone_verified' => ! is_null($user->phone_verified_at),
            'role'           => $user->role,
            'status'         => $user->isActive(),
            'member_since'   => $user->created_at->toISOString(),
        ];
    }

    private function formatCustomer(Customer $customer): array
    {
        return [
            'id'               => $customer->id,
            'aadhaar_number'   => $customer->masked_aadhaar,   // masked: XXXX-XXXX-XXXX (last 4 visible)
            'aadhaar_document' => $customer->aadhaar_document ? asset($customer->aadhaar_document) : null,
            'pan_number'       => $customer->pan_number,
            'pan_document'     => $customer->pan_document ? asset($customer->pan_document) : null,
            'selfie_document'  => $customer->selfie_document ? asset($customer->selfie_document) : null,
            'address'          => $customer->address,
            'city'             => $customer->city,
            'state'            => $customer->state,
            'pincode'          => $customer->pincode,
            'dob'              => $customer->dob?->toDateString(),
            'gender'           => $customer->gender,
            'employment_type'  => $customer->employment_type,
            'employment_label' => $customer->employment_label,
            'salary'           => $customer->salary ? (float) $customer->salary : null,
            'status'           => $customer->status,
            'status_label'     => $customer->status_label,
        ];
    }
}
