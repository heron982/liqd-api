<?php

namespace App\Modules\Trade\Features\Sell\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'amount_btc' => ['required', 'numeric', 'gt:0', 'decimal:0,8'],
        ];
    }
}
