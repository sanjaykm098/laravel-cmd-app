<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\note;
use function Laravel\Prompts\search;

class UpdateUserBySerach extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user-by-serach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user by search';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        clear();
        $user = search(
            'Please search for a user by email or name',
            fn(string $value) => strlen($value) > 0
                ? User::whereLike('name', "%{$value}%")->pluck('name', 'id')->all()
                : [],
            'Select a user to update',
            10,
            ['required'],
            '',
            true,
        );
        if ($user) {
            $user = User::find($user);
            if (!$user) {
                error('User not found');
                return;
            }
        }
        $responses = form($user)
            ->text('What is your name?', required: true, name: 'name', default: $user->name)
            ->text('What is your email?', required: true, name: 'email', default: $user->email, validate: 'required|email')
            ->password(
                label: 'What is your password?',
                validate: ['password' => 'nullable|min:8'],
                name: 'password',
                hint: 'Leave empty if you do not want to update the password'
            )
            ->confirm('Do you want to update the user?', name: 'confirm')
            ->submit();
        if ($responses['confirm']) {
            $user->update([
                'name' => $responses['name'],
                'email' => $responses['email'],
                'password' => $responses['password'] ? bcrypt($responses['password']) : $user->password,
            ]);
            note('User updated successfully');
        } else {
            info('User not updated || User updated cancelled');
        }
    }
}
