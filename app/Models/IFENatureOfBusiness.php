<?php

namespace App\Models;

use App\Traits\Paginatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class IFENatureOfBusiness extends Model
{
    use HasFactory, Paginatable, Sortable;

    protected $table = 'nature_of_business';

    protected $fillable = [
        'name',
        'value',
    ];
}
