<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;

class CheckOs
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
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
        {
            $this->command->output->error('Nowy CMS nie lubi Windowsa!!!');
            
            exit;
        }
    }
}
