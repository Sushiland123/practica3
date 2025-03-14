<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'nombre' => 'string|max:255',
            'nombreUsuario' => 'string|max:255|unique:users,nombreUsuario,' . auth()->id(),
            'edad' => 'integer|min:18',
            'país' => 'string|max:255',
        ];
    }
}