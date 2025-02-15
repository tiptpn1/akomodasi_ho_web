<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreViconAdminRequest extends FormRequest
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
            'acara' => 'required',
            'jenisrapat' => 'required|exists:jenisrapat,id',
            'bagian' => 'required|exists:master_bagian,master_bagian_id',
            'tanggal' => 'required',
            'waktu' => 'required|date_format:H:i',
            'waktu2' => 'required|date_format:H:i',
            'ruangan' => 'nullable',
            'vicon' => 'required|in:Ya,Tidak',
            'ruangan2' => 'nullable',
            'jenis_link' => 'nullable|required_if:vicon,Ya|in:Internal,Eksternal',
            'peserta' => 'nullable',
            'jumlahpeserta' => 'nullable|integer',
            'sk' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:5120',
            'keterangan' => 'nullable',
            'petugasruangrapat' => 'nullable|array',
            'petugasruangrapat.*' => 'nullable|exists:user,username',
            'petugasti' => 'nullable|array',
            'petugasti.*' => 'nullable|exists:user,username',
            'nopersonel' => 'required|string',
        ];
    }


    public function messages()
    {
        return [
            'acara.required' => 'Acara wajib diisi!!!',
            'privat.in' => 'Privat harus berupa "Ya" or "Tidak".',
            'jenisrapat.required' => 'Jenis Rapat wajib diisi!!!',
            'jenisrapat.exists' => 'Jenis Rapat tidak ditemukan',
            'agenda_direksi.in' => 'Agenda Direksi harus berupa "Ya" or "Tidak".',
            'bagian.required' => 'Bagian wajib diisi!!!',
            'bagian.exists' => 'Bagian tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi!!!',
            'waktu.required' => 'Waktu wajib diisi!!!',
            'waktu.date_format' => 'Waktu harus dalam format "HH:mm".',
            'waktu2.required' => 'Waktu wajib diisi!!!',
            'waktu2.date_format' => 'Waktu harus dalam format "HH:mm".',
            'vicon.required' => 'Vicon wajib diisi!!!',
            'vicon.in' => 'Vicon harus berupa "Ya" or "Tidak".',
            'jenis_link.required_if' => 'Jenis Link wajib diisi!!!',
            'jenis_link.in' => 'Jenis Link harus berupa "Internal" or "Eksternal".',
            'jumlahpeserta.integer' => 'Jumlah Peserta harus berupa angka!!!',
            'sk.file' => 'File Surat/Memo Undangan harus berupa pdf, word, excel atau gambar.',
            'sk.mimes' => 'File Surat/Memo Undangan harus berupa pdf, word, excel atau gambar.',
            'petugasruangrapat.array' => 'Petugas Ruang Rapat wajib diisi!!!',
            'petugasruangrapat.*.exists' => 'Petugas Ruang Rapat tidak ditemukan',
            'petugasti.array' => 'Petugas Direksi wajib diisi!!!',
            'petugasti.*.exists' => 'Petugas Direksi tidak ditemukan',
            'link.exists' => 'Link tidak ditemukan',
            'nopersonel.required' => 'Nomor Personel wajib diisi!!!',
            'nopersonel.string' => 'Nomor Personel harus berupa string',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 422));
    }
}
