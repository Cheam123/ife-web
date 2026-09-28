<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    public function scopeKey($query, $key)
    {
        return $query->where('key', strtoupper($key))->firstOrFail()->value;
    }
}
