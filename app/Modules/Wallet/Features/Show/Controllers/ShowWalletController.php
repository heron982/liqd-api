<?php

namespace App\Modules\Wallet\Features\Show\Controllers;

use App\Modules\Wallet\Features\Show\Services\ShowWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowWalletController
{
    public function execute(Request $request, ShowWalletService $service): JsonResponse
    {
        return response()->json(
            $service->execute($request->user()->id)->toArray()
        );
    }
}
