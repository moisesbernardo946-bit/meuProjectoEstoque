<?php

// app/Models/CostCenter.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $fillable = [
        'company_group_id',
        'company_id',
        'client_id',
        'code',
        'name',
        'type',
        'director_name',
        'is_active',
    ];

    // Relações
    public function group()
    {
        return $this->belongsTo(CompanyGroup::class, 'company_group_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Helpers

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function financialMovements()
    {
        return $this->hasMany(FinancialMovement::class);
    }
}
