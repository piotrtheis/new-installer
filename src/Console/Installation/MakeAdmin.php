<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Hashing\BcryptHasher as Hash;

class MakeAdmin
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
        if (! $this->command->output->confirm('Czy chcesz dodać administratora strony?', true))
        {
            exit;
        }

        try {            
            $this->addAdmin();
        } catch (\Illuminate\Database\QueryException $e) {
            $this->command->output->error('Coś poszło nie tak, ' . $e->getMessage());
            
            if (! $this->command->output->confirm('Czy chcesz spróbować jeszcze raz?', true))
            {
                exit;
            }

            $this->addAdmin();
        }
    }


    protected function addAdmin()
    {
        if(!$email = $this->command->output->ask('email', $default = null))
        {
            return;
        }

        //TODO ask hidden
        if(!$password = $this->command->output->ask('password', $default = null))
        {
            return;
        }


        $id = Capsule::table('users')->insertGetId([
            'email' => $email,
            'password' => (new Hash)->make($password)
        ]);

        //TODO roles
    }


}
