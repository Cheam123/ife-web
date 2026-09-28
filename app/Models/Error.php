<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    protected $table = 'error_table';

    protected $fillable = [
        'page_name',
        'error_message',
        'input'
    ];
}
