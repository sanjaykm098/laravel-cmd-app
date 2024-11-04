<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\table;

class ViewAllUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View all users in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        clear();
        $header = ['ID', 'Name', 'Email', 'Created At', 'Updated At'];
        $users = User::all()->map(function ($user) {
            return [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Created At' => $user->created_at->diffforhumans(),
                'Updated At' => $user->updated_at->diffforhumans(),
            ];
        })->toArray();

        table($header, $users);
    }
}
