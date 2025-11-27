<?php

// app/Models/FinancialMovement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialMovement extends Model
{
    protected $fillable = [
        'cost_center_id',
        'type',
        'movement_date',
        'amount',
        'description',
        'reference',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    // Scopes úteis
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFromYearMonth($query, int $year, ?int $month = null)
    {
        $query->whereYear('movement_date', $year);

        if ($month) {
            $query->whereMonth('movement_date', $month);
        }

        return $query;
    }
}
