<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'next_spin_result',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
