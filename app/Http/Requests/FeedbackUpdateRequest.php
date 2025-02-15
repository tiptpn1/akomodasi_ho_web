<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackUpdateRequest extends FormRequest
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
            'petugas_ti' => ['nullable', 'string'],
            'laptop_vicon' => ['nullable', 'string'],
            'kendala_ti' => ['nullable', 'string'],
            'kendala_umum' => ['nullable', 'string'],
            'kendala_ex' => ['nullable', 'string'],
        ];
    }
}
