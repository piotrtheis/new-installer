<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;

class RunNpmInstall
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
        $this->command->output->section('Instalacja npm, npm forever gulp bower larvel-elixir');
        $this->command->output->writeln('<info>Instalacja  npm...</info>');

        $process = new Process('npm set progress=true && npm install', $this->command->path);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->setTimeout(null)->run(function ($type, $line) {
            $this->command->output->write($line);
        });

        $dependencies = [
            'gulp' => 'npm set progress=true && npm install gulp',
            'bower' => 'npm set progress=true && npm install bower',
            'forever' => 'npm set progress=true && npm install forever',
            'laravel-elixir' => 'npm set progress=true && npm install laravel-elixir',
        ];

        foreach ($dependencies as $library => $command) 
        {
            $process = new Process($library);

            $process->run(function ($type, $line) use ($command){
                if(str_contains($line, 'command not found')){
                    $process = new Process($command, $this->command->path);

                    if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
                        $process->setTty(true);
                    }

                    $process->run(function ($type, $line) {
                        $this->command->output->write($line);
                    });
                }
            });
        }
    }
}
