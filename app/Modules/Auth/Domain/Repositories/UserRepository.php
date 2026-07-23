<?php

namespace App\Modules\Auth\Domain\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return User::query()->create($data);
    }

    public function find(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->fill($data);
        $user->save();

        return $user->refresh();
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
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
