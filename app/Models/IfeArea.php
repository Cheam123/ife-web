<?php

namespace App\Models;


use App\Traits\Paginatable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class IfeArea extends Model
{
    use HasFactory, Paginatable, Sortable;

    protected $table = 'ife_area';

    protected $fillable = [
        'area',
        'description',
    ];
}
