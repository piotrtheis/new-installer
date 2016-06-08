<?php

namespace CMS\Installer\Console\Installation;

use CMS\Installer\Console\NewCommand;
use Symfony\Component\Process\Process;

class InitGit
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
        if($this->initGit()){
            $this->remoteRepo();
        }
    }

    protected function initGit()
    {
        $confirm = $this->command->output->confirm('Inicjujemy git-a?', $default = true);

        if ($confirm) {
            $process = new Process('git init && git add --all && git commit -m "first commit"', $this->command->path);

            $process->setTimeout(null)->run(function ($type, $line) {
                $this->command->output->write($line);
            });

            $this->command->output->block('Pierwszy commit gotowy', 'OK', 'fg=green', null, true);

            return true;
        } else {
            $this->command->output->block('Nie to nie.', 'WARNING', 'fg=black;bg=cyan', ' ', true);
        }

        return false;
    }

    protected function remoteRepo()
    {
        if (!$this->checkRemoteRepo()) {
            $confirm = $this->command->output->confirm('Dodajemy link do zdalnego repo?', $default = true);

            if ($confirm) {
                $this->addRemote();
            }
        } else {
            $confirm = $this->command->output->confirm('Link do zdalnego repo jest już dodany, czy chcesz go zmienić?', $default = true);

            if ($confirm) {
                $this->updateRemote();
            }
        }

        $this->command->output->success('Mam nadzieję że się udało');
    }

    protected function addRemote()
    {
        $url = $this->command->output->ask('Podaj link do zdalnego repo', $default = null, $validator = null);

        $process = new Process('git remote add origin ' . $url . ' &&  git push -u origin master', $this->command->path);

        $process->run(function ($type, $line) {
            $this->command->output->text($line);
        });
    }

    protected function updateRemote()
    {
        $url = $this->command->output->ask('Podaj link do zdalnego repo', $default = null, $validator = null);

        $process = new Process('git remote set-url origin ' . $url . ' &&  git push -u origin master', $this->command->path);

        $process->run(function ($type, $line) {
            $this->command->output->text($line);
        });
    }

    protected function checkRemoteRepo()
    {
        $process = new Process('git config --get remote.origin.url');

        $process->setTimeout(null)->run(function ($type, $line) {

            return (bool)$line != '';
        });

        return false;
    }
}
