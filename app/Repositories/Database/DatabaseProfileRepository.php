<?php

namespace App\Repositories\Database;

use App\Models\Profile;
use App\Repositories\Interfaces\ProfileRepositoryInterface;

class DatabaseProfileRepository implements ProfileRepositoryInterface {
    public function create(array $data) {
        return Profile::create($data);
    }
}
