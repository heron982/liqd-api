<?php

namespace App\Modules\Auth\Features\Register\Services;

use App\Modules\Auth\Features\Register\Dtos\RegisterResultDto;
use App\Modules\Auth\Domain\Repositories\UserRepository;
use App\Modules\Wallet\Domain\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;

class RegisterService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly WalletRepository $walletRepository,
    ) {}

    public function execute(string $name, string $email, string $password): RegisterResultDto
    {
        return DB::transaction(function () use ($name, $email, $password) {
            $user = $this->userRepository->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            $this->walletRepository->create([
                'user_id' => $user->id,
                'balance_brl' => '10000.00',
                'balance_btc' => '0.00000000',
            ]);

            $token = $this->userRepository->createToken($user);

            return RegisterResultDto::from([
                'user' => $user,
                'token' => $token,
            ]);
        });
    }
}
