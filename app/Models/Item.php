<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'unit_id',
        'storage_location_id',
        'code',
        'name',
        'description',
        'brand',
        'image',
        'stock',
        'stock_minimum',
        'stock_maximum',
        'purchase_price',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'stock'          => 'integer',
        'stock_minimum'  => 'integer',
        'stock_maximum'  => 'integer',
        'purchase_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class);
    }
}