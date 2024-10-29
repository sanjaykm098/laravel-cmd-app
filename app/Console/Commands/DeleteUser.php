<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\alert;
use function Laravel\Prompts\clear;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multisearch;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;

class DeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        clear();
        $users = multisearch(
            'Search for the users you want to delete',
            fn(string $value) => strlen($value) > 0
                ? User::where('name', 'like', "%{$value}%")->pluck('name', 'id')->all()
                : [],
            'Select users to delete',
            10,
            true,
        );

        $confirm = confirm('Are you sure you want to delete the selected users (' . count($users) . ')?', false, 'Yes I am sure', 'No I am not sure', false, ['required']);

        if (!$confirm) {
            alert('Operation cancelled');
            return;
        }
        progress(
            label: 'Deleting users...',
            steps: count($users),
            callback: function () use ($users) {
                foreach ($users as $user) {
                    $user = User::find($user);
                    sleep(1);
                    if ($user) {
                        $user->delete();
                    }
                }
            }
        );

        alert('Users deleted successfully');
    }
}
