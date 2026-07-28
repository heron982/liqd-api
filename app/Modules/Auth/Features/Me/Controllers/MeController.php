<?php

namespace App\Modules\Auth\Features\Me\Controllers;

use App\Modules\Auth\Features\Me\Services\MeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController
{
    public function execute(Request $request, MeService $service): JsonResponse
    {
        return response()->json(
            $service->execute($request->user()->id)->toArray()
        );
    }
}