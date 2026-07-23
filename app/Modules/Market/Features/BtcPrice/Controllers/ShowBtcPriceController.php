<?php

namespace App\Modules\Market\Features\BtcPrice\Controllers;

use App\Modules\Market\Features\BtcPrice\Services\BtcPriceService;
use Illuminate\Http\JsonResponse;

class ShowBtcPriceController
{
    public function execute(BtcPriceService $service): JsonResponse
    {
        return response()->json(
            $service->execute()->toArray()
        );
    }
}
