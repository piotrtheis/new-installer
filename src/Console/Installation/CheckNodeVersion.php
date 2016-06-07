<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;

class CheckNodeVersion
{
    protected $command;

    /**
     * Create a new installation helper instance.
     *
     * @param  NewCommand  $command
     * @return void
     */
    public function __construct(NewCommand $command)
    {
        $this->command = $command;
    }

    /**
     * Run the installation helper.
     *
     * @return void
     */
    public function install()
    {
        $process = new Process('nodejs -v');

        $process->run(function ($type, $line) { 
            if(str_contains($line, 'command not found'))
            {
                $this->command->output->warning('Brak nodejs!!!');

                if (! $this->command->output->confirm('Czy chcesz zainstalować nodejs teraz?', true))
                {
                    return;
                }

                $this->installNode();
            }
        });
    }


    protected function installNode(){
        $process = new Process('sudo apt-get install nodejs');

        $process->run(function ($type, $line) {
            $this->command->output->write($line);
        });
    }
}
