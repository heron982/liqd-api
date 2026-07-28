<?php

namespace App\Modules\Wallet\Shared\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'balance_brl', 'balance_btc'])]
class Wallet extends Model
{
    protected function casts(): array
    {
        return [
            'balance_brl' => 'decimal:2',
            'balance_btc' => 'decimal:8',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
