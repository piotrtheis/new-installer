<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;

class DirMod
{
    protected $command;
    protected $name;

    /**
     * Create a new installation helper instance.
     *
     * @param  NewCommand  $command
     * @param  string  $name
     * @return void
     */
    public function __construct(NewCommand $command, $name)
    {
        $this->name = $name;
        $this->command = $command;
    }

    /**
     * Run the installation helper.
     *
     * @return void
     */
    public function install()
    {
        $this->command->output->section('Zmiana uprawnień katalogów storage i bootstrap/cache');
        
        $process = new Process('sudo chmod 755 ../'. $this->name .' -R && find '. $this->command->path .' -type f -exec chmod 644 {} + && sudo chmod 777 ' . $this->command->path . '/storage -R && sudo chmod 777 ' . $this->command->path . '/bootstrap/cache  -R');

        $process->setTimeout(null)->run(function ($type, $line) {
            $this->command->output->write($line);
        });
    }
}
