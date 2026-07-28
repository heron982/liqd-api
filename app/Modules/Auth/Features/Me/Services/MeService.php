<?php

namespace App\Modules\Auth\Features\Me\Services;

use App\Models\User;
use App\Modules\Auth\Features\Me\Dtos\MeDto;
use App\Modules\Auth\Shared\Repositories\UserRepository;
use App\Modules\Shared\Helpers\CacheHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MeService
{
    private const CACHE_TTL_SECONDS = 60;

    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function execute(int $userId): MeDto
    {
        /** @var array{id: int, name: string, email: string} $profile */
        $profile = CacheHelper::remember(
            UserRepository::profileCacheKey($userId),
            self::CACHE_TTL_SECONDS,
            function () use ($userId) {
                $user = $this->userRepository->find($userId);

                if (! $user) {
                    throw (new ModelNotFoundException)->setModel(User::class, [$userId]);
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            },
        );

        return MeDto::from($profile);
    }
}