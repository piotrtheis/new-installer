<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;

class CloneCmsRepo
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
        $process = new Process('git clone git@bitbucket.org:etdcms/cms.git .', $this->command->path);

        $process->setTimeout(null)->run(function ($type, $line) {
            $this->command->output->write($line);
        });

        //remove .git folder
        $process = new Process('rm -rf .git', $this->command->path);

        $process->setTimeout(null)->run(function ($type, $line) use ($io) {
            $this->command->output->write($line);
        });
    }
}
