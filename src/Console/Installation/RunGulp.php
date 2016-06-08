<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;

class RunGulp
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
        $this->command->output->writeln('<info>Instalacja  bower-a...</info>');

        $process = new Process('gulp && php artisan cms:update-scripts', $this->command->path);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }
        
        $process->setTimeout(null)->run(function ($type, $line) {
            $this->command->output->write($line);
        });
    }
}
