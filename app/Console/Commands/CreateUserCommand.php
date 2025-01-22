<?php

namespace App\Console\Commands;

use App\Enums\RolesEnum;
use App\Repositories\Database\DatabaseProfileRepository;
use App\Repositories\Database\DatabaseUserRepository;
use App\Rules\Profile\ValidProfileNameRule;
use App\Traits\Commands\InteractsWithConsole;
use DB;
use Illuminate\Console\Command;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command {
    use InteractsWithConsole;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user.';

    /**
     * Execute the console command.
     */
    public function handle() {
        $data['first_name'] = $this->askUntilValid(
            'First name of the user',
            ['required', 'string', 'max:50', new ValidProfileNameRule]
        );

        $data['last_name'] = $this->askUntilValid(
            'Last name of the user',
            ['required', 'string', 'max:50', new ValidProfileNameRule]
        );

        $data['email'] = $this->askUntilValid(
            'Email of the user',
            ['required', 'string', 'email', 'max:255', 'unique:users,email']
        );

        $data['password'] = $this->askUntilValid(
            'Password of the user',
            ['required', 'string', Password::defaults()],
            true
        );

        $userRepository = new DatabaseUserRepository;
        $profileRepository = new DatabaseProfileRepository;

        $user = DB::transaction(function () use ($data, $userRepository, $profileRepository) {
            $user = $userRepository->create([
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => RolesEnum::ADMIN->value,
            ]);

            $profileRepository->create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
            ]);

            return $userRepository->findById($user->id, ['profile', 'role']);
        });

        $this->info('User '.$user->profile->fullName.' has been created successfully.');

        return 0;
    }
}
