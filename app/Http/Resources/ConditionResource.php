<?php

namespace App\Http\Resources;

use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Condition */
class ConditionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'value' => $this->value,
            'operator' => $this->operator,
            'characteristic' => new CharacteristicResource($this->whenLoaded('characteristic')),
        ];
    }
}
