<?php

namespace App\Http\Requests;

class PasswordResetRequest extends BaseRequest
{
    /**
     * Получает валидацию, которая применяется к запросу
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }
}
