<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'item_id',
        'user_id',
        'quantity',
        'unit',
        'price',
        'note',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'price'            => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}