<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
        $this->merge([
            'password' => str_replace(' ', '', $this->password ?? ''),
            'password_confirmation' => str_replace(' ', '', $this->password_confirmation ?? ''),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'confirmed',
                'regex:/^\S*$/',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
            'password_confirmation' => ['required', 'string']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'token.required' => 'Token reset password tidak valid.',
            'password.required' => 'Password baru harus diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password tidak boleh mengandung spasi.',
            'password_confirmation.required' => 'Konfirmasi password harus diisi.'
        ];
    }
}
