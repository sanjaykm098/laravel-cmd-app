<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\multisearch;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\table;

class LoginToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:login-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate login token for user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = multisearch(
            'Search for the users you want to view',
            fn(string $value) => strlen($value) > 0
                ? User::whereLike('name', "%{$value}%")->pluck('name', 'id')->all()
                : []
        );

        foreach ($users as $userId) {
            $user = User::find($userId);
            if (!$user) {
                error('User not found');
                unset($users[$userId]);
                continue;
            }
        }
        if (count($users) == 0) {
            error('No users found');
            return;
        }

        $users = User::whereIn('id', $users)->get();

        $process = progress(
            label: 'Generating login token...',
            steps: count($users)
        );

        $process->start();

        foreach ($users as $user) {
            $user['token'] = $user->createToken('login-token')->plainTextToken;
            $process->advance();
        }

        $process->finish();

        $headers = ['ID', 'Name', 'Email', 'Token'];
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Token' => $user['token'],
            ];
        }
        table($headers, $data);
        outro('Login token generated successfully');
    }
}
