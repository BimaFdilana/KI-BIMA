<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('update users') || auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('users')->ignore($userId)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
                Rule::unique('users')->ignore($userId)
            ],
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today|after:1900-01-01',
            'address' => 'nullable|string|max:500',
            'profile_photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'ktp_number' => [
                'nullable',
                'string',
                'digits:16',
                Rule::unique('users')->ignore($userId)
            ],
            'ktp_name' => 'nullable|string|max:255',
            'ktp_address' => 'nullable|string|max:500',
            'ktp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB for KTP
            'ktp_verified' => 'nullable|boolean',
            'ktp_verification_status' => 'nullable|in:pending,verified,rejected',
            'phone_verified' => 'nullable|boolean',
            'email_verified' => 'nullable|boolean',
            'two_factor_enabled' => 'nullable|boolean',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive,suspended',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Full name is required.',
            'name.max' => 'Full name cannot exceed 255 characters.',
            'username.required' => 'Username is required.',
            'username.unique' => 'This username is already taken.',
            'username.regex' => 'Username can only contain letters, numbers, dots, underscores, and hyphens.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone_number.regex' => 'Please provide a valid phone number.',
            'phone_number.unique' => 'This phone number is already registered.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'date_of_birth.after' => 'Please provide a valid date of birth.',
            'profile_photo_path.image' => 'Profile picture must be an image file.',
            'profile_photo_path.mimes' => 'Profile picture must be in JPEG, PNG, JPG, GIF, or WebP format.',
            'profile_photo_path.max' => 'Profile picture size cannot exceed 2MB.',
            'ktp_number.digits' => 'KTP number must be exactly 16 digits.',
            'ktp_number.unique' => 'This KTP number is already registered.',
            'ktp_image.image' => 'KTP image must be an image file.',
            'ktp_image.mimes' => 'KTP image must be in JPEG, PNG, JPG, GIF, or WebP format.',
            'ktp_image.max' => 'KTP image size cannot exceed 5MB.',
            'role.required' => 'User role is required.',
            'role.exists' => 'Selected role does not exist.',
            'status.required' => 'User status is required.',
            'status.in' => 'Invalid user status selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'username' => 'username',
            'email' => 'email address',
            'phone_number' => 'phone number',
            'date_of_birth' => 'date of birth',
            'profile_photo_path' => 'profile picture',
            'ktp_number' => 'KTP number',
            'ktp_name' => 'KTP name',
            'ktp_address' => 'KTP address',
            'ktp_image' => 'KTP image',
            'two_factor_enabled' => 'two-factor authentication',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('phone_number')) {
            $this->merge([
                'phone_number' => preg_replace('/[^\d\+]/', '', $this->phone_number)
            ]);
        }

        // Clean KTP number
        if ($this->has('ktp_number')) {
            $this->merge([
                'ktp_number' => preg_replace('/[^\d]/', '', $this->ktp_number)
            ]);
        }

        // Convert boolean fields
        $booleanFields = ['ktp_verified', 'phone_verified', 'email_verified', 'two_factor_enabled'];
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN)
                ]);
            }
        }
    }
}