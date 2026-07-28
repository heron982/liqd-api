<?php

namespace App\Modules\Trade\Features\Sell\Controllers;

use App\Modules\Trade\Shared\Dtos\TradeResponseDto;
use App\Modules\Trade\Features\Sell\Requests\SellRequest;
use App\Modules\Trade\Features\Sell\Services\SellService;
use Illuminate\Http\JsonResponse;

class SellController
{
    public function execute(SellRequest $request, SellService $service): JsonResponse
    {
        $result = $service->execute(
            $request->user()->id,
            $request->string('amount_btc')->toString(),
        );

        return response()->json(
            TradeResponseDto::from([
                'wallet' => [
                    'balance_brl' => $result->wallet->balanceBrl(),
                    'balance_btc' => $result->wallet->balanceBtc(),
                ],
                'transaction' => $result->transaction,
            ])->toArray()
        );
    }
}
