<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;

class DatabaseConfiguration
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
    public function install($next_try = false)
    {
        //only for first try
        if(!$next_try)
        {
            $this->command->output->note('Konfiguracja bazy');

            if (! $this->command->output->confirm('Czy chcesz dodać config bazy?', true))
            {
                exit;
            }
        }

        $config = [];

        if(!$host = $this->command->output->ask('host', $default = 'localhost'))
        {
            return;
        }

        if(!$user = $this->command->output->ask('user', $default = null))
        {
            return;
        }

        if(!$pass = $this->command->output->ask('pass', $default = null))
        {
            return;
        }

        if(!$db_name = $this->command->output->ask('db_name', $default = null))
        {
            return;
        }

        $config['DB_HOST'] = $host;
        $config['DB_USERNAME'] = $user;
        $config['DB_PASSWORD'] = $pass;
        $config['DB_DATABASE'] = $db_name;

        
        $this->testConnection($config);

        $this->saveConfig($config);

    }


    protected function testConnection(array $config)
    {
        try 
        {
            $dbh = new \PDO(sprintf('mysql:host=%s;dbname=%s', $config['DB_HOST'], $config['DB_DATABASE']), $config['DB_USERNAME'], $config['DB_PASSWORD']);
            

        } catch (\PDOException $e) {
            $this->command->output->error('Coś poszło nie tak, ' . $e->getMessage());
            
            if (! $this->command->output->confirm('Czy chcesz spróbować ponownie?', true))
            {
                exit;
            }

            $this->install(true);
        }
    }


    protected function saveConfig($config){

        foreach ($config as $key => $value) {
            $command = sprintf("sed -i '/%s=/c\%s=%s' .env", $key, $key, $value);

            $process = new Process($command, $this->command->path);

            $process->run(function ($type, $line) {
                $this->command->output->write($line);
            });
        } 

    }
}
