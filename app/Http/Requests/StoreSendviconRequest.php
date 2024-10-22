<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSendviconRequest extends FormRequest
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
            'privat' => 'nullable',
            'bagian' => 'required|exists:bagian,id',
            'acara' => 'required',
            'dokumentasi' => 'required|in:Ya,Tidak',
            'peserta' => 'nullable',
            'tanggal' => 'required',
            'waktu1' => 'required|date_format:H:i',
            'waktu2' => 'required|date_format:H:i',
            'ruangan' => 'nullable',
            'vicon' => 'required|in:Ya,Tidak',
            'ruangan2' => 'nullable',
            'jenis_link' => 'required_if:vicon,Ya',
            'jumlahpeserta' => 'nullable|integer',
            'sk' => 'nullable|file|mimes:pdf,jpg|max:5120',
            'nopersonel' => 'required|string',
            'keterangan' => 'nullable',
            'captcha' => 'required|captcha',
            'passwordVerif' => 'required|exists:bagian,kode_pin'
        ];
    }

    public function messages()
    {
        return [
            'bagian.required' => 'Bagian wajib diisi!!!',
            'bagian.exists' => 'Bagian tidak ditemukan',
            'acara.required' => 'Acara wajib diisi!!!',
            'dokumentasi.required' => 'Dokumentasi wajib diisi!!!',
            'dokumentasi.in' => 'Dokumentasi harus berupa "Ya" or "Tidak".',
            'tanggal.required' => 'Tanggal wajib diisi!!!',
            'waktu1.required' => 'Waktu1 wajib diisi!!!',
            'waktu1.date_format' => 'Waktu harus dalam format "HH:mm".',
            'waktu2.required' => 'Waktu2 wajib diisi!!!',
            'waktu2.date_format' => 'Waktu harus dalam format "HH:mm".',
            'vicon.required' => 'Vicon wajib diisi!!!',
            'vicon.in' => 'Vicon harus berupa "Ya" or "Tidak".',
            'jenis_link.required_if' => 'Jenis Link wajib diisi!!!',
            'jumlahpeserta.integer' => 'Jumlah Peserta harus berupa angka!!!',
            'sk.mimes' => 'File Surat/Memo Undangan harus berupa pdf atau jpg.',
            'nopersonel.required' => 'Nomor Personel wajib diisi!!!',
            'captcha.required' => 'Captcha wajib diisi!!!',
            'captcha.captcha' => 'Captcha tidak sesuai',
            'passwordVerif.required' => 'Kode Verif wajib diisi!!!',
            'passwordVerif.exists' => 'Kode tidak valid'
        ];
    }
}
