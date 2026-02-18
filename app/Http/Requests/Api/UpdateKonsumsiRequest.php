<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKonsumsiRequest extends FormRequest
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
            'm_pagi' => 'nullable|in:0,1',
            'm_siang' => 'nullable|in:0,1',
            'm_malam' => 'nullable|in:0,1',
            's_pagi' => 'nullable|in:0,1',
            's_siang' => 'nullable|in:0,1',
            's_sore' => 'nullable|in:0,1',
            'biaya_m_pagi' => 'nullable|integer',
            'biaya_m_siang' => 'nullable|integer',
            'biaya_m_malam' => 'nullable|integer',
            'biaya_s_pagi' => 'nullable|integer',
            'biaya_s_siang' => 'nullable|integer',
            'biaya_s_sore' => 'nullable|integer',
            'biaya_lain' => 'nullable|integer',
            'keterangan' => 'nullable|string',
        ];
    }
}
