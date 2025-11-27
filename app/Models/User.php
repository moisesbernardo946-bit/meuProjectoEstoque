<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // se estiveres a usar API/Sanctum

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    // use HasApiTokens; // descomenta se usar Sanctum

    /**
     * Os atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * Atributos que devem ser escondidos em arrays (por exemplo, JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos que devem ser convertidos automaticamente.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10+ faz hash automático
    ];

    /**
     * Empresa filha à qual o usuário pertence.
     * Ex.: diretor da Home Decor, comprador da Indústria, etc.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Atalhos úteis para checar o tipo de usuário.
     */
    public function isDirector(): bool
    {
        return $this->role === 'diretor';
    }

    public function isFinanceiro(): bool
    {
        return $this->role === 'financeiro';
    }

    public function isAlmoxarife(): bool
    {
        return $this->role === 'almoxarife';
    }

    public function isComprador(): bool
    {
        return $this->role === 'comprador';
    }
}
