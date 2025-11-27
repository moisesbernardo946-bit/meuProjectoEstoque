<?php
// app/Models/PurchaseProgram.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'requisition_id',
        'buyer_id',
        'code',
        'status',
        'buyer_name',
        'buyer_phone',
        'buyer_email',
        'buyer_company_id',
        'notes',
        'total_budget_value',
    ];

    // Relação com empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Requisição de origem
    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    // Comprador (usuário)
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // Empresa filha do comprador (se usar)
    public function buyerCompany()
    {
        return $this->belongsTo(Company::class, 'buyer_company_id');
    }

    // Itens da programação
    public function items()
    {
        return $this->hasMany(PurchaseProgramItem::class);
    }

    // Anexos (documentos)
    public function attachments()
    {
        return $this->hasMany(PurchaseProgramAttachment::class);
    }

    // Helpers de status (se quiser usar em views)
    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    public function isAprovado(): bool
    {
        return $this->status === 'aprovado';
    }

    public function isConcluido(): bool
    {
        return $this->status === 'concluido';
    }

        public function financialRecords()
    {
        return $this->hasMany(FinancialRecord::class, 'purchase_program_id');
    }
}
