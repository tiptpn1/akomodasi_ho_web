<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KonsumsiResource extends JsonResource
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
            "id_sendvicon" => $this->id_sendvicon,
            "m_pagi" => $this->m_pagi,
            "m_siang" => $this->m_siang,
            "m_malam" => $this->m_malam,
            "biaya_m_pagi" => $this->biaya_m_pagi,
            "biaya_m_siang" => $this->biaya_m_siang,
            "biaya_m_malam" => $this->biaya_m_malam,
            "s_pagi" => $this->s_pagi,
            "s_siang" => $this->s_siang,
            "s_sore" => $this->s_sore,
            "biaya_s_pagi" => $this->biaya_s_pagi,
            "biaya_s_siang" => $this->biaya_s_siang,
            "biaya_s_sore" => $this->biaya_s_sore,
            "biaya_lain" => $this->biaya_lain,
            "keterangan" => $this->keterangan,
            "status" => $this->status,
            "konsumsi_kirim" => $this->konsumsi_kirim,
            "status_batal_m_pagi" => $this->status_batal_m_pagi,
            "status_batal_m_siang" => $this->status_batal_m_siang,
            "status_batal_m_malam" => $this->status_batal_m_malam,
            "status_batal_s_pagi" => $this->status_batal_s_pagi,
            "status_batal_s_siang" => $this->status_batal_s_siang,
            "status_batal_s_sore" => $this->status_batal_s_sore,
        ];
    }
}
