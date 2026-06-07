<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'item_id',
        'type',
        'message',
        'status',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}