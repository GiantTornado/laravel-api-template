<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Exceptions\User\UserNotFoundException;
use App\Models\User;
use App\Repositories\Interfaces\ProfileRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserService {
    protected $userRepository;

    protected $profileRepository;

    public function __construct(UserRepositoryInterface $userRepository, ProfileRepositoryInterface $profileRepository) {
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
    }

    public function getUser($id) {
        $user = $this->userRepository->findById($id, ['profile', 'role']);
        if (!$user) {
            throw new UserNotFoundException;
        }

        return $user;
    }

    public function getUserByEmail($email = null) {
        $user = User::with(['profile', 'role'])
            ->where('email', $email)
            ->first();

        return $user;
    }

    public function createFromRequest($request) {
        return DB::transaction(function () use ($request) {
            $user = $this->userRepository->create([
                'email' => $request->email,
                'password' => $request->password,
                'role_id' => RolesEnum::Viewer->value,
            ]);

            $this->profileRepository->create([
                'user_id' => $user->id,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
            ]);

            return $this->userRepository->findById($user->id, ['profile', 'role']);
        });
    }
}
