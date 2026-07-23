<?php

namespace App\Modules\Auth\Features\Me\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController
{
    public function execute(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
