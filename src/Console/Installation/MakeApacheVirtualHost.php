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
        $this->command->output->writeln('<info>Dodanie pliku vhosta do /etc/apache2/sites-available</info>');

        $file = '<VirtualHost *:80>
            ServerName '. $this->name .'.pl
            ServerAlias www.'. $this->name .'.pl

            DocumentRoot '. $this->command->path .'/public
            DirectoryIndex index.php

                <Directory '. $this->command->path .'/public/>
                        AllowOverride All
                        Require all granted
                </Directory>
        </VirtualHost>';

        $process = new Process(sprintf('echo "%s" | sudo tee --append /etc/apache2/sites-available/%s.conf', $file, $this->name));

        $process->setTimeout(null)->run(function ($type, $line) {
            $this->command->output->write($line);
        });





        $this->command->output->writeln('<info>a2ensite i restart</info>');

        $process = new Process('sudo /etc/init.d/apache2 restart');

        $process->setTimeout(null)->run(function ($type, $line) {
            $this->command->output->write($line);
        });
    }
}
