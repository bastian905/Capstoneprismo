<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => str_replace(' ', '', $this->current_password ?? ''),
            'new_password' => str_replace(' ', '', $this->new_password ?? ''),
            'confirm_password' => str_replace(' ', '', $this->confirm_password ?? ''),
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
            'current_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'different:current_password',
                'regex:/^\S*$/',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
            'confirm_password' => ['required', 'string', 'same:new_password']
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
            'current_password.required' => 'Password saat ini harus diisi.',
            'new_password.required' => 'Password baru harus diisi.',
            'new_password.different' => 'Password baru harus berbeda dari password saat ini.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.regex' => 'Password tidak boleh mengandung spasi.',
            'confirm_password.required' => 'Konfirmasi password harus diisi.',
            'confirm_password.same' => 'Konfirmasi password tidak cocok.'
        ];
    }
}
