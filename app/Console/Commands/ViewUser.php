<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\multisearch;
use function Laravel\Prompts\search;
use function Laravel\Prompts\table;

class ViewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:view-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View a user in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        clear();
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

        $headers = ['ID', 'Name', 'Email', 'Created At', 'Updated At'];
        $data = [];
        foreach ($users as $userId) {
            $user = User::find($userId);
            $data[] = [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Created At' => $user->created_at,
                'Updated At' => $user->updated_at,
            ];
        }
        table($headers, $data);
    }
}
