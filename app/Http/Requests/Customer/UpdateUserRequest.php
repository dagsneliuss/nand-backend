<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['filled', 'alpha', 'max:255'],
            'surname' => ['filled', 'alpha_dash', 'max:255'],
            'email_address' => ['filled', 'email', 'unique:users', 'max:255'],
            'password' => ['filled', 'confirmed', 'max:255', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(3)
            ],
            'age' => ['filled', 'integer', 'min:13', 'max:150'],
            'gender' => ['filled', Rule::in(['male', 'female'])]
        ];
    }
}
