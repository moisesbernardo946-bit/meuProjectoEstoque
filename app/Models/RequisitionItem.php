<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'product_id',
        'requested_quantity',
        'delivered_quantity',
        'notes',
        'item_status',
        'rejection_reason',
    ];

    /* 🔗 Relações */

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
