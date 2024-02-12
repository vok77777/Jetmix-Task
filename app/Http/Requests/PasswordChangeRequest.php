<?php

namespace App\Http\Requests;

class PasswordChangeRequest extends BaseRequest
{
    /**
     * Получает валидацию, которая применяется к запросу
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'token' => 'required|exists:password_reset_tokens,token',
            'password' => 'required|min:8|confirmed',
        ];
    }
}
