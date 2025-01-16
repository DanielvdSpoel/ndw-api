<?php

namespace App\Http\Resources;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Site */
class SiteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ndw_id' => $this->ndw_id,
            'label' => $this->label,
            'type' => $this->type,
            'version' => $this->version,
            'side' => $this->side,
            'lanes' => $this->lanes,
            'version_time' => $this->version_time,
            'computation_method' => $this->computation_method,
            'lat' => $this->lat,
            'long' => $this->long,
            'characteristics' => CharacteristicResource::collection($this->whenLoaded('characteristics')),
        ];
    }
}
