<?php

namespace App\Modules\Trade\Features\Buy\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyRequest extends FormRequest
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
            'amount_brl' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
        ];
    }
}
