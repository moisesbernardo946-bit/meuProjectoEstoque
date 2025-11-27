<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExitProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'entity_product_id',
        'type',
        'quantity',
        'notes',
    ];

    public function entityProduct()
    {
        return $this->belongsTo(EntityProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
