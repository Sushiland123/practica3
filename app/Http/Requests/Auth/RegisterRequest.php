<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'correo' => 'required|string|email|max:255|unique:users',
            'nombreUsuario' => 'required|string|max:255|unique:users',
            'edad' => 'required|integer|min:18',
            'país' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}