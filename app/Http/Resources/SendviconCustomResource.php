<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SendviconCustomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'data' => new SendviconResource($this['vicon']),
            'status_nama' => $this['status_nama'],
            'status_ruangan' => $this['status_ruangan'],
            'available_ruangan' => $this['available_ruangan'],
        ];
    }
}
