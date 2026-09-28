<?php

namespace App\Models;

use App\Traits\Paginatable;
use App\Models\Cities;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    use HasFactory, Paginatable, Filterable;

    protected $fillable = [
        'name',
        'code',
    ];

    public function cities()
    {
        return $this->hasMany(Cities::class, 'state_id', 'id');
    }
}
