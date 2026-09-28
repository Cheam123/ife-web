<?php

namespace App\Models;

use App\Traits\Paginatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class IFEStatus extends Model
{
    use HasFactory, Paginatable, Sortable;

    protected $table = 'ife_status';

    protected $fillable = [
        'name',
        'value',
    ];
    
}
