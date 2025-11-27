<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

