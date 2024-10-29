<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class RegisterUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:register-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        clear();
        $userArray = [];
        $userArray['name'] = text(
            'Please enter your name',
            'John Doe',
            '',
            true,
            ['required']
        );
        $userArray['email'] = text(
            'Please enter your email',
            'john@example.com',
            '',
            true,
            ['required', 'email', 'unique:users,email']
        );
        $userArray['password'] = password(
            'Please enter your password',
            'password',
            true,
            ['required', 'min:8']
        );
        $userArray['password'] = bcrypt($userArray['password']);
        $user = User::create(
            $userArray
        );
        info('User created successfully');

        table(
            ['ID', 'Name', 'Email', 'Created At', 'Updated At'],
            [
                [
                    'ID' => $user->id,
                    'Name' => $user->name,
                    'Email' => $user->email,
                    'Created At' => $user->created_at,
                    'Updated At' => $user->updated_at,
                ]
            ]
        );
    }
}
