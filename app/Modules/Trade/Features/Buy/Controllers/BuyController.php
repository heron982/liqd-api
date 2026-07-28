<?php

namespace App\Modules\Trade\Features\Buy\Controllers;

use App\Modules\Trade\Features\Buy\Requests\BuyRequest;
use App\Modules\Trade\Features\Buy\Services\BuyService;
use App\Modules\Trade\Shared\Dtos\TradeResponseDto;
use Illuminate\Http\JsonResponse;

class BuyController
{
    public function execute(BuyRequest $request, BuyService $service): JsonResponse
    {
        $result = $service->execute(
            $request->user()->id,
            $request->string('amount_brl')->toString(),
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
