<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Characteristic extends Model
{
    protected $fillable = [
        'key',
        'index',
        'accuracy',
        'period',
        'lane',
        'type',
        'conditions',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function conditionRelation(): HasMany
    {
        return $this->hasMany(Condition::class,);

    }

    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }
}
