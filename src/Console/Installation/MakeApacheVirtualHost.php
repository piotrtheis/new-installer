<?php

namespace CMS\Installer\Console\Installation;

use Symfony\Component\Process\Process;
use CMS\Installer\Console\NewCommand;

class MakeApacheVirtualHost
{
    protected $name;
    protected $command;

    /**
     * Create a new installation helper instance.
     *
     * @param  NewCommand  $command
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
        $this->command->output->section('<info>Dodanie pliku vhosta do /etc/apache2/sites-available</info>');

        $file = '<VirtualHost *:80>
            ServerName '. $this->name .'.pl
            ServerAlias www.'. $this->name .'.pl

            DocumentRoot '. $this->command->path .'/public
            DirectoryIndex index.php

                <Directory '. $this->command->path .'/>
                        AllowOverride All
                        Require all granted
                </Directory>
        </VirtualHost>';

        $process = new Process(sprintf('echo "%s" | sudo tee --append /etc/apache2/sites-available/%s.conf', $file, $this->name));

        $process->setTimeout(null)->run(function ($type, $line) {
            $this->command->output->write($line);
        });





        $this->command->output->section('<info>a2ensite i restart</info>');

        $process = new Process('sudo a2ensite '. $this->name .'.conf && /etc/init.d/apache2 restart');

        $process->setTimeout(null)->run(function ($type, $line) {
            $this->command->output->write($line);
        });


        
        $command = sprintf('sudo sed -i \'1s/^/127.0.1.1       %s\n/\' /etc/hosts && /etc/init.d/networking restart', $this->name . '.pl');

        if (! $this->command->output->confirm('Czy chcesz dodać wpis do /etc/hosts['. $command .']?', true))
        {
            exit;
        }

        $process = new Process($command);

        $process->setTimeout(null)->run(function ($type, $line) {
            $this->command->output->write($line);
        });    
    }
}
