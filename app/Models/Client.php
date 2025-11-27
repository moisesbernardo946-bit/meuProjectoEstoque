<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'nif',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    /**
     * Empresa filha à qual este cliente pertence.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function costCenters()
    {
        return $this->hasMany(CostCenter::class);
    }

    public function financialTransactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }
}
