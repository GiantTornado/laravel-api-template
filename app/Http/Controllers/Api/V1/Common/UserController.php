<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;

class UserController extends Controller {
    public function store(StoreUserRequest $storeUserRequest, UserService $userService) {
        $user = $userService->createFromRequest($storeUserRequest);

        return $this->responseCreated(new UserResource($user));
    }
}
