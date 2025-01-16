<?php

namespace App\Http\Controllers;

use App\Http\Resources\SiteResource;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::query();
        return SiteResource::collection($sites->paginate());
    }

    public function show(Site $site)
    {
        $site->load(['characteristics', 'characteristics.conditionRelation']);
        return new SiteResource($site);
    }
}
