<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\table;

class Login extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Login to the application & Generate token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = form()->text('Please enter your email', 'sanjay@example.com', '', true, ['required', 'email', 'exists:users,email'])
            ->password('Please enter your password', 'password', true, ['required'])->submit();

        $email = $data[0];
        $password = $data[1];

        $user = User::where('email', $email)->first();

        if (Hash::check($password, $user->password)) {
            info('Login Successful');
            $token = $user->createToken('auth_token')->plainTextToken;
            table(['Token'], [[$token]]);
        } else {
            error('Your Credentials are incorrect');
            return;
        }
    }
}
