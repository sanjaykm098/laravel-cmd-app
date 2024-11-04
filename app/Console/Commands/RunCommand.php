<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\select;

class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $getAllCommands = $this->getApplication()->all();

        $commands = [];

        foreach ($getAllCommands as $command) {
            if (strpos($command->getName(), 'app:') === 0) {
                $commands[$command->getName()] = $command->getDescription();
            }
        }

        $result = select('Select a command to run', $commands);
        if ($result === false) {
            $this->error('Command not found.');
            return;
        }

        $this->call($result);
    }
}
