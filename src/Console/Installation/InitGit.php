<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;

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
        $this->initGit();
        $this->gitAddRemote();
    }


    protected function initGit(){
        $confirm = $this->command->output->confirm('Inicjujemy git-a?', $default = true);

        if ($confirm) 
        {
            $process = new Process('git init && git add --all && git commit -m "first commit"', $this->command->path);

            $process->setTimeout(null)->run(function ($type, $line) use ($io) 
            {
                $this->command->output->write($line);
            });

            $this->command->output->block('Pierwszy commit gotowy', 'OK', 'fg=green', null, true);

            $this->gitAddRemote();
        } else 
        {
            $this->command->output->block('Nie to nie.', 'WARNING', 'fg=black;bg=cyan', ' ', true);
        }
    }



    protected function gitAddRemote()
    {
        $confirm = $this->command->output->confirm('Dodajemy link do zdalnego repo?', $default = true);

        if ($confirm) {
            $url = $this->command->output->ask('Podaj link do zdalnego repo', $default = null, $validator = null);

            // $process = new Process('git ls-remote ' . $url);

            // $process->setTimeout(null)->run(function ($type, $line){

            //     if (strstr($line, 'fatal') && $line != 'fatal: remote origin already exists') {
            //         $this->command->output->error('Git twierdzi że takie repo nieistnieje, co Ty na to?');

            //         $confirm = $this->command->output->confirm('Próbujemy jeszcze raz?', $default = true);

            //         if ($confirm) {
            //             $this->gitAddRemote();
            //         }
            //     }
            // });

            $process = new Process('git remote add origin ' . $url . ' &&  git push -u origin master', $this->command->path);

            $process->run(function ($type, $line) {
                $this->command->output->text($line);
            });
        } else {
            $this->command->output->block('Nie to nie.', 'WARNING', 'fg=black;bg=cyan', ' ', true);
        }

        $this->command->output->success('Mam nadzieję że się udało');
    }
}
