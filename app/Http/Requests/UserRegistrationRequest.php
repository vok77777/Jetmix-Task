<?php

namespace App\Http\Requests;

class UserRegistrationRequest extends BaseRequest
{
    /**
     * Получает валидацию, которая применяется к запросу
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users',
            'name' => 'required|string|min:2|max:255',
            'password' => 'required|min:8',
        ];
    }
}
