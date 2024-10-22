<?php

namespace App\Http\Requests\Api;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePresensiRequest extends FormRequest
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
            'id' => 'required|exists:sendvicon,id',
            'nama' => 'required',
            'jabatan' => 'required',
            'instansi' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Nama Acara Tidak Boleh Kosong',
            'id.exists' => 'Acara tidak ada',
            'nama.required' => 'Nama Tidak Boleh Kosong',
            'jabatan.required' => 'Jabatan Tidak Boleh Kosong',
            'instansi.required' => 'Instansi Tidak Boleh Kosong',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error('validation error', $validator->errors(), 422)
        );
    }
}
