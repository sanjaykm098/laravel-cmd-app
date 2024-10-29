<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class VerifyToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify login token for user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = text('Please enter your login token', 'Please enter your login token', '', true);

        $result = PersonalAccessToken::findToken($token);

        if ($result) {
            // dd($result);
            table(
                ['ID', 'Name', 'Abilities', 'Last Used At', 'Created At'],
                [
                    [
                        'ID' => $result->id,
                        'Name' => $result->name,
                        'Abilities' => json_encode($result->abilities),
                        'Last Used At' => $result->last_used_at,
                        'Created At' => $result->created_at->diffforhumans(),
                    ],
                ]
            );
            info('Token is valid');
        } else {
            error('Token is invalid');
        }
    }
}
