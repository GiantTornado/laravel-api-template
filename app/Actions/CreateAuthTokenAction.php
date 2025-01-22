<?php

namespace App\Actions;

class CreateAuthTokenAction {
    public function execute($user, $user_agent) {
        $device = substr($user_agent, 0, 255);

        return $user->createToken($device)->plainTextToken;
    }
}
