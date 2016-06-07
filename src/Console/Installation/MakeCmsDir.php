<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;

class MakeCmsDir
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
        if(!is_dir($this->command->path)){
            @mkdir($this->command->path);

            $this->command->output->note(sprintf('Katalog %s został utworzony', $this->name));
        } else {
            $this->command->output->error('Podany katalog jest już zajęty!!!');

            exit;
        }
    }
}
