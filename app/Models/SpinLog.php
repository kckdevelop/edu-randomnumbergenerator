<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpinLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bet_amount',
        'result_amount',
        'status_manipulasi',
        'multiplier',
        'grid_pattern',
    ];

    protected function casts(): array
    {
        return [
            'grid_pattern' => 'array',
            'multiplier' => 'float',
            'bet_amount' => 'decimal:2',
            'result_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
