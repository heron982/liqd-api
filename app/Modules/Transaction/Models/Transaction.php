<?php

namespace App\Modules\Transaction\Models;

use App\Models\User;
use App\Modules\Transaction\Enums\TransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'type', 'amount_brl', 'amount_btc', 'btc_price'])]
class Transaction extends Model
{
    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'amount_brl' => 'decimal:2',
            'amount_btc' => 'decimal:8',
            'btc_price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
