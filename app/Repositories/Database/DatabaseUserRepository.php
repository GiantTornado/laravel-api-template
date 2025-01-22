<?php

namespace App\Repositories\Database;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class DatabaseUserRepository implements UserRepositoryInterface {
    public function findById(int $id, array $relations = []) {
        return User::with($relations)->find($id);
    }

    public function create(array $data) {
        return User::create($data);
    }
}
