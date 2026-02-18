<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'petugas_ti' => $this->petugas_ti,
            'laptop_vicon' => $this->laptop_vicon,
            'kendala_ti' => $this->kendala_ti,
            'kendala_umum' => $this->kendala_umum,
            'kendala_ex' => $this->kendala_ex,
            'sendvicon_id' => $this->sendvicon_id
        ];
    }
}
