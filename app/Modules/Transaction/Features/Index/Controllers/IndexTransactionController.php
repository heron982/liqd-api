<?php

namespace App\Modules\Transaction\Features\Index\Controllers;

use App\Modules\Transaction\Domain\Dtos\TransactionResponseDto;
use App\Modules\Transaction\Features\Index\Services\IndexTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexTransactionController
{
    public function execute(Request $request, IndexTransactionService $service): JsonResponse
    {
        $transactions = $service->execute($request->user()->id);

        return response()->json([
            'data' => TransactionResponseDto::collect($transactions)->toArray(),
        ]);
    }
}
