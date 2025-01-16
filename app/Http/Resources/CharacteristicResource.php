<?php

namespace App\Http\Resources;

use App\Models\Characteristic;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Characteristic */
class CharacteristicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'index' => $this->index,
            'accuracy' => $this->accuracy,
            'period' => $this->period,
            'lane' => $this->lane,
            'type' => $this->type,
            'url' => route('sites.characteristics.show', [$this->site, $this]),
            'measurements' => MeasurementResource::collection($this->whenLoaded('measurements')),
            'conditions' => ConditionResource::collection($this->whenLoaded('conditionRelation')),
            'site' => new SiteResource($this->whenLoaded('site')),
        ];
    }
}
