<?php

namespace App\Http\Requests;

class UserLoginRequest extends BaseRequest
{
    /**
     * Получает валидацию, которая применяется к запросу
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }
}
