<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Measurement extends Model
{
    protected $fillable = [
        'type',
        'value',
        'characteristic_id',
        'timestamp',
    ];

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
        ];
    }
}
