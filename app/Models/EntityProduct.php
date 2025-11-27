<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityProduct extends Model
{
    protected $fillable = [
        'product_id',
        'entity_type',
        'entity_id',
        'company_id',
        'quantity',
        'requested_quantity',
        'min_stock',
        'max_stock',
    ];

    // entity_type => 'company' ou 'client'
    public function entity()
    {
        return $this->morphTo(null, 'entity_type', 'entity_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
