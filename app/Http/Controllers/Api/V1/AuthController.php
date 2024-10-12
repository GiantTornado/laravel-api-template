<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreAuthRequest;
use App\Services\AuthService;
use App\Services\UserService;
use App\Actions\CreateAuthTokenAction;
use App\Http\Resources\User\UserResource;

class AuthController extends Controller
{
    public function show(UserService $userService)
    {
        $user = $userService->getUser(auth()->id());

        return $this->responseOk(new UserResource($user));
    }

    public function store(StoreAuthRequest $storeAuthRequest, AuthService $authService)
    {
        try {
            $user = $authService->getUserByCredentialsOrFail(
                $storeAuthRequest->email,
                $storeAuthRequest->password
            );

            $token = (new CreateAuthTokenAction)->execute($user, $storeAuthRequest->userAgent());

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }

        return $this->responseCreated([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    public function destroy()
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->responseDeleted();
    }
}
