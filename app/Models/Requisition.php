<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'company_id',
        'requester_name',
        'code', // código interno tipo REQ-0001
        'priority',
        'status',
        'purpose',
        'notes',
    ];

    /* 🔗 Relações */

    // Usuário que registrou a requisição
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Cliente para quem é a requisição
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Empresa de onde sai o material
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Itens da requisição
    public function items()
    {
        return $this->hasMany(RequisitionItem::class);
    }

    public function purchasePrograms()
    {
        return $this->hasMany(PurchaseProgram::class);
    }

}
