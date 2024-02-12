<?php

namespace App\Http\Requests;

class ReferenceCreateRequest extends BaseRequest
{
    /**
     * Получает валидацию, которая применяется к запросу
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'topic' => 'required|string|max:255|min:5',
            'message' => 'required|string|min:10',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|mimes:jpeg,jpg,png,webp,doc,pdf,docx,txt,rtf',
        ];
    }
}
