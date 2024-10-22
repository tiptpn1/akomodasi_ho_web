<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSendviconAdminRequest extends FormRequest
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
            /**
             * @example Rapat Pembukaan 2024
             */
            'acara' => 'required|string',
            /**
             * @example Direksi
             */
            'bagian' => 'required|exists:master_bagian,master_bagian_nama',
            'agenda_direksi' => 'required|in:Ya,Tidak',
            /**
             * @example Rapat Umum
             */
            'jenisrapat' => 'required|exists:jenisrapat,nama',
            /**
             * @example 2024-10-10
             */
            'tanggal' => 'required|date_format:Y-m-d',
            /**
             * @example 10:00
             */
            'waktu' => 'required|date_format:H:i',
            /**
             * @example 12:00
             */
            'waktu2' => 'required|date_format:H:i',
            /**
             * @example Ruangan Kopi
             */
            'id_ruangan' => 'required',
            /**
             * @example Coffee shop
             */
            'ruangan_lain' => 'nullable|string',
            'privat' => 'required|in:Ya,Tidak',
            'vicon' => 'required|in:Ya,Tidak',
            'dokumentasi' => 'required|in:Ya,Tidak',
            /**
             * @example Jhon Doe (08xxxxxxx)
             */
            'personil' => 'required|string',
            /**
             * @example Direksi, Direktur, Direktur Utama
             */
            'peserta' => 'nullable|string',
            /**
             * @example 12
             */
            'jumlahpeserta' => 'nullable|integer',
            // 'petugasruangrapat' => 'nullable|string',
            // 'petugasti' => 'nullable|string',
            /**
             * @example Kakao
             */
            'link' => 'nullable|exists:masterlink,namalink',
            /**
             * @example 12233
             */
            'password' => 'nullable',
            /**
             * @example keterangan
             */
            'keterangan' => 'nullable|string',
            /**
             * @example Internal
             */
            'jenis_link' => 'nullable|required_if:vicon,Ya|in:Internal,Eksternal',
            /**
             * @example Jhon Doe
             */
            'user' => 'required|string',
            'sk' => 'nullable|file|mimes:pdf,jpg|max:20480',
            'makan.pagi' => 'nullable|in:0,1',
            'makan.siang' => 'nullable|in:0,1',
            'makan.malam' => 'nullable|in:0,1',
            'snack.pagi' => 'nullable|in:0,1',
            'snack.siang' => 'nullable|in:0,1',
            'snack.malam' => 'nullable|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Data :attribute tidak boleh kosong.',
            'in' => 'Data :attribute tidak valid.',
            'date_format' => 'Data :attribute tidak valid.',
            'exists' => 'Data :attribute tidak valid.',
            'file' => 'File :attribute tidak valid.',
            'integer' => 'Data :attribute harus berupa angka.',
        ];
    }

    public function attributes()
    {
        return [
            'acara' => 'Acara',
            'bagian' => 'Bagian',
            'agenda_direksi' => 'Agenda Direksi',
            'jenisrapat' => 'Jenis Rapat',
            'tanggal' => 'Tanggal',
            'waktu' => 'Waktu Mulai',
            'waktu2' => 'Waktu Akhir',
            'id_ruangan' => 'Ruangan',
            'ruangan_lain' => 'Ruangan Lain',
            'privat' => 'Privat',
            'vicon' => 'Vicon',
            'dokumentasi' => 'Dokumentasi',
            'personil' => 'Personil',
            'peserta' => 'Peserta',
            'jumlahpeserta' => 'Jumlah Peserta',
            'petugasruangrapat' => 'Petugas Ruang Rapat',
            'petugasti' => 'Petugas T.I',
            'link' => 'Link',
            'password' => 'Password',
            'keterangan' => 'Keterangan',
            'jenis_link' => 'Jenis Link',
            'user' => 'User',
            'sk' => 'SK',
            'makan.pagi' => 'Makan Pagi',
            'makan.siang' => 'Makan Siang',
            'makan.malam' => 'Makan Malam',
            'snack.pagi' => 'Snack Pagi',
            'snack.siang' => 'Snack Siang',
            'snack.malam' => 'Snack Malam',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 422));
    }
}
