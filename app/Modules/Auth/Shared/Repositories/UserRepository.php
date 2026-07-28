<?php

namespace App\Modules\Auth\Shared\Repositories;

use App\Models\User;
use App\Modules\Shared\Library\EloquentRepository\Adapters\EloquentRepositoryAdapter;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function __construct(
        private readonly EloquentRepositoryAdapter $adapter,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        /** @var User */
        return $this->adapter->create(User::class, $data);
    }

    public function find(int $id): ?User
    {
        /** @var User|null */
        return $this->adapter->find(User::class, $id);
    }

    public function findByEmail(string $email): ?User
    {
        /** @var User|null */
        return $this->adapter->query(User::class)
            ->where('email', $email)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        /** @var User */
        return $this->adapter->update($user, $data);
    }

    public function delete(User $user): bool
    {
        return $this->adapter->delete($user);
    }

    public function passwordMatches(User $user, string $plainPassword): bool
    {
        return Hash::check($plainPassword, $user->password);
    }

    public function createToken(User $user, string $name = 'mobile'): string
    {
        return $user->createToken($name)->plainTextToken;
    }
}
