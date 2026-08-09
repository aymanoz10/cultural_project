<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendRegisterOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone'        => 'required|string',
            'name'         => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender'       => 'required|in:male,female',
        ];
    }
}
