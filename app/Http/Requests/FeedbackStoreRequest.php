<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackStoreRequest extends FormRequest
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
            'petugas_ti' => ['required', 'string'],
            'laptop_vicon' => ['required', 'string'],
            'kendala_ti' => ['nullable', 'string'],
            'kendala_umum' => ['nullable', 'string'],
            'kendala_ex' => ['nullable', 'string'],
        ];
    }
}
