<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'code', 
        'description',
        'category_id', 
        'unit_id', 
        'measure', 
        'zone_id', 
        'qr_code_path'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
