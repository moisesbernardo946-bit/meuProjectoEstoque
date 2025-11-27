<?php

// app/Models/PurchaseProgramAttachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProgramAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_program_id',
        'path',
        'original_name',
        'mime_type',
    ];

    public function program()
    {
        return $this->belongsTo(PurchaseProgram::class, 'purchase_program_id');
    }

    // AGORA: apenas devolve o path local, sem chamar Storage::url()
    public function getUrlAttribute(): string
    {
        // Ex.: "/mnt/data/alguma_pasta/ficheiro.pdf"
        return $this->path;
    }
}
