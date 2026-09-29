<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    public function scopeKey($query, $key)
    {
        return $query->where('key', strtoupper($key))->firstOrFail()->value;
    }
}
