<?php

namespace App\Modules\Auth\Features\Register\Controllers;

use App\Modules\Auth\Features\Register\Requests\RegisterRequest;
use App\Modules\Auth\Features\Register\Services\RegisterService;
use Illuminate\Http\JsonResponse;

class RegisterController
{
    public function execute(RegisterRequest $request, RegisterService $service): JsonResponse
    {
        $result = $service->execute(
            $request->string('name')->toString(),
            $request->string('email')->toString(),
            $request->string('password')->toString(),
        );

        return response()->json([
            'user' => [
                'id' => $result->user->id,
                'name' => $result->user->name,
                'email' => $result->user->email,
            ],
            'token' => $result->token,
        ], 201);
    }
}
