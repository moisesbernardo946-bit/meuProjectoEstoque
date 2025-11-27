<?php
// app/Models/PurchaseProgramItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProgramItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_program_id',
        'requisition_item_id',
        'product_id',
        'payment_method',
        'supplier_name',
        'budget_unit_value',
        'budget_total_value',
        'status',
        'notes',
    ];

    // Programação a que pertence
    public function program()
    {
        return $this->belongsTo(PurchaseProgram::class, 'purchase_program_id');
    }

    // Item da requisição original
    public function requisitionItem()
    {
        return $this->belongsTo(RequisitionItem::class);
    }

    // Produto (para facilitar joins, nome, unidade, etc.)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helpers de status
    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    public function isConcluido(): bool
    {
        return $this->status === 'concluido';
    }

    public function isFaltando(): bool
    {
        return $this->status === 'faltando';
    }
}
