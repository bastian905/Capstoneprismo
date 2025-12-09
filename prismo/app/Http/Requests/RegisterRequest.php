<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Remove spaces from password fields
        if ($this->has('password')) {
            $this->merge([
                'password' => str_replace(' ', '', $this->password),
            ]);
        }
        
        if ($this->has('confirmPassword')) {
            $this->merge([
                'confirmPassword' => str_replace(' ', '', $this->confirmPassword),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'unique:users,email'],
            'password' => [
                'required',
                Password::min(6)
                    ->letters()
                    ->numbers(),
                'regex:/^\S*$/', // No spaces allowed
            ],
            'confirmPassword' => ['required', 'same:password'],
            'terms' => ['accepted'],
            'role' => ['required', 'in:customer,mitra'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Email ini sudah terdaftar. Silakan login atau gunakan email lain.',
            'email.email' => 'Format email tidak valid.',
            'password.regex' => 'Password tidak boleh mengandung spasi.',
            'password.min' => 'Password minimal 6 karakter.',
            'confirmPassword.same' => 'Konfirmasi password tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ];
    }
}
