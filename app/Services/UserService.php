<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            throw new \Exception('User Not Found.', 404);
        }

        return $user;
    }

    public function getUserByEmail($email = null)
    {
        $user = User::with(['profile', 'role'])
            ->where('email', $email)
            ->first();

        return $user;
    }
}
