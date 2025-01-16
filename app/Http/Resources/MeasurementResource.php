<?php

namespace App\Http\Resources;

use App\Models\Measurement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Measurement */
class MeasurementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'value' => $this->value,
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s'),
            'characteristic' => new CharacteristicResource($this->whenLoaded('characteristic')),
        ];
    }
}
