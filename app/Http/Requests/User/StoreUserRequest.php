<?php

namespace App\Http\Requests\User;

use App\Rules\Profile\ValidProfileNameRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'firstName' => [
                'required',
                'string',
                'max:50',
                new ValidProfileNameRule,
            ],
            'lastName' => [
                'required',
                'string',
                'max:50',
                new ValidProfileNameRule,
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                Password::defaults(),
                'confirmed',
            ],
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'password_confirmation' => $this->input('passwordConfirmation'),
        ]);
    }
}
