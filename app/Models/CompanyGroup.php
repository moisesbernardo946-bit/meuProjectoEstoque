<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nif',
        'code',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    /**
     * Empresas filhas deste grupo.
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
