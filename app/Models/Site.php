<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'ndw_id',
        'label',
        'version',
        'side',
        'lanes',
        'version_time',
        'computation_method',
        'lat',
        'long',
    ];

    protected function casts(): array
    {
        return [
            'version_time' => 'timestamp',
        ];
    }

    public function characteristics(): HasMany
    {
        return $this->hasMany(Characteristic::class);
    }
}
