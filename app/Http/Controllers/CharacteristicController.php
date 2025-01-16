<?php

namespace App\Http\Controllers;

use App\Http\Resources\CharacteristicResource;
use App\Models\Characteristic;
use App\Models\Site;

class CharacteristicController extends Controller
{
    public function index(Site $site)
    {
        return CharacteristicResource::collection($site->characteristics);
    }

    public function show(Site $site, Characteristic $characteristic)
    {
        $characteristic->load(['site', 'conditionRelation', 'measurements']);
        return new CharacteristicResource($characteristic);
    }
}
