<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Actions\CreateAuthTokenAction;
use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Repositories\Interfaces\ProfileRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use DB;
use Exception;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller {
    public function redirectToProvider(string $provider) {
        if (!in_array($provider, config('auth.socialite.drivers'), true)) {
            abort(404, 'Social Provider is not supported');
        }

        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl()
        ]);
    }

    public function handleProviderCallback(string $provider, UserRepositoryInterface $userRepository, ProfileRepositoryInterface $profileRepository) {
        if (!in_array($provider, config('auth.socialite.drivers'), true)) {
            abort(404, 'Social Provider is not supported');
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        $user = DB::transaction(function () use ($userRepository, $profileRepository, $socialUser, $provider) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = $userRepository->create([
                    'email' => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                    'role_id' => RolesEnum::Viewer->value
                ]);

                $profileRepository->create([
                    'user_id' => $user->id,
                    'first_name' => $user->user['given_name'] ?? '',
                    'last_name' => $user->user['family_name'] ?? '',
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            $socialAccount = $user->socialAccounts()
                ->where('provider_name', $provider)
                ->first();

            if (!$socialAccount) {
                $user->socialAccounts()->create([
                    'provider_id' => $socialUser->getId(),
                    'provider_name' => $provider,
                ]);
            }

            return $userRepository->findById($user->id, ['profile', 'role']);
        });

        $token = (new CreateAuthTokenAction)->execute($user, "{$provider}-token");

        return $this->responseCreated([
            'user' => new UserResource($user),
            'access_token' => $token,
        ]);
    }
}
