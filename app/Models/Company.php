<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_group_id',
        'name',
        'code',
        'nif',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    /**
     * Empresa mãe (grupo) à qual esta empresa pertence.
     */
    public function group()
    {
        return $this->belongsTo(CompanyGroup::class, 'company_group_id');
    }

    /**
     * Clientes desta empresa.
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Usuários vinculados a esta empresa (diretor, comprador, etc).
     * (Pressupõe uma coluna company_id na tabela users).
     */
    public function users()
    {
        return $this->hasMany(User::class);
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
