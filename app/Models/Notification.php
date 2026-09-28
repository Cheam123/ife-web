<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'telegram_send_sticker_id',
        'telegram_send_message_id',
        'read_at',
        'content_id',
        'content_type'
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function content()
    {
        return $this->morphTo();
    }
}
