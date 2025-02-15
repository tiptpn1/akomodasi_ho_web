<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SendviconResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "acara" => $this->acara,
            "dokumentasi" => $this->dokumentasi,
            "bagian_id" => $this->bagian_id,
            "jenisrapat_id" => $this->jenisrapat_id,
            "tanggal" => $this->tanggal,
            "waktu" => $this->waktu,
            "waktu2" => $this->waktu2,
            "ruangan" => $this->ruangan,
            "ruangan_lain" => $this->ruangan_lain,
            "id_ruangan" => $this->id_ruangan,
            "vicon" => $this->vicon,
            "personil" => $this->personil,
            "peserta" => $this->peserta,
            "jumlahpeserta" => $this->jumlahpeserta,
            "sk" => $this->sk,
            "status" => $this->status,
            "keterangan" => $this->keterangan,
            "durasi" => $this->durasi,
            "jenis_link" => $this->jenis_link,
            "user" => $this->user,
            "created" => $this->created,
            "token" => $this->token,
            "status_absensi" => $this->status_absensi,
            "status_approval" => $this->status_approval,
            "is_reminded" => $this->is_reminded,
            'konsumsi' => new KonsumsiResource($this->whenLoaded('konsumsi')),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
