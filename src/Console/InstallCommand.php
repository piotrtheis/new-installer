<?php

namespace CMS\Installer\Console;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{

    protected $io;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this->setName('new')
            ->setDescription('Instalacja cms-a')
            ->addArgument('name', InputArgument::OPTIONAL);
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message  = 'Testowy message';
        $elements = [1, 2, 3];

        $io = new SymfonyStyle($input, $output);

// // common output elements
        //         $io->title($message);
        //         $io->section($message);
        //         $io->text($message);
        //         $io->comment($message);

// // more advanced output elements
        //         $io->note($message);
        //         $io->caution($message);
        //         $io->listing($elements);

//         $question = 'Pytanie?';
        //         $choices = [1,2,3];

//         // $io->table($headers,$rows);

// // ask for user's input
        //         // $io->ask($question, $default = null,$validator = null);
        //         // $io->askHidden($question, $validator = null);
        //         $confirm = $io->confirm($question, $default = true);

//         if($confirm){
        //             echo 'Tak';
        //         } else {
        //             echo 'Nie';
        //         }

//         $io->choice($question,  $choices, $default = null);

// // display the result of the command or some important task
        //         $io->success( $message);
        //         $io->error( $message);
        //         $io->warning( $message);

        // $this->verifyApplicationDoesntExist(
        $directory = ($input->getArgument('name')) ? getcwd() . '/' . $input->getArgument('name') : getcwd();
        //     $output
        // );

        @mkdir('/home/piotr/www/xxx');

        $io->title('Rozpoczynam instalację CMS...');

        $this->cloneRepo($directory, $io)
            ->removeGit($directory, $io)
            ->gitInit($directory, $io)
        
        //->checkWindows($directory, $io)
          
        //    ->writeMessage($io)
        //   ->runComposer($directory, $io)
        //    ->initNode($directory, $io)
        //    ->initBower($directory, $io)
        //    ->runGulp($directory, $io)
        //    ->dirMod($directory, $io)
            ->phpConfigCheck($directory, $io);
        // ->setDatabase($directory, $io)
        // ->makeMigration($directory, $io)
        // ->insertAdmin($directory, $io);

        $io->success('CMS zainstalowany pomyślnie');
    }

    /**
     * Clone cms repo, use ssh
     *
     * @param  string $directory
     * @return InstallCommand
     */
    protected function cloneRepo($directory, $io)
    {
        $process = new Process('git clone git@bitbucket.org:etdcms/cms.git .', '/home/piotr/www/xxx');
        $process->setTimeout(10 * 60);

        $process->run(function ($type, $line) use ($io) {
            $io->text($line);
        });

        return $this;
    }

    /**
     * Remove .git dir
     *
     * @param  string $directory
     * @return InstallCommand
     */
    protected function removeGit($directory, $io)
    {
        $process = new Process('rm -rf .git', '/home/piotr/www/xxx');
        $process->setTimeout(10 * 60);

        $process->run(function ($type, $line) use ($io) {
            $io->write($line);
        });

        return $this;
    }

    /**
     * Init empty git repo.
     *
     * @param  [type] $directory [description]
     * @param  [type] $input     [description]
     * @param  [type] $output    [description]
     * @return InstallCommand
     */
    protected function gitInit($directory, $io)
    {
        $confirm = $io->confirm('Inicjujemy git-a?', $default = true);

        if ($confirm) {
            $process = new Process('git init && git add --all && git commit -m "first commit"', '/home/piotr/www/xxx');
            $process->setTimeout(10 * 60);

            $process->run(function ($type, $line) use ($io) {
                $io->write($line);
            });

            $io->block('Pierwszy commit gotowy', 'OK', 'fg=green', null, true);

            $this->gitAddRemote($directory, $io);
        } else {
            $io->block('Nie to nie.', 'WARNING', 'fg=black;bg=cyan', ' ', true);
        }

        return $this;
    }

    protected function gitAddRemote($directory, $io)
    {
        $confirm = $io->confirm('Dodajemy link do zdalnego repo?', $default = true);

        if ($confirm) {
            $url = $io->ask('Podaj link do zdalnego repo', $default = null, $validator = null);

            $process = new Process('git ls-remote ' . $url);
            $process->setTimeout(10 * 60);

            $process->run(function ($type, $line) use ($io, $directory) {

                if (strstr($line, 'fatal')) {
                    $io->error('Git twierdzi że takie repo nieistnieje, co Ty na to?');

                    $confirm = $io->confirm('Próbujemy jeszcze raz?', $default = true);

                    if ($confirm) {
                        $this->gitAddRemote($directory, $io);
                    }
                }
            });

            $process = new Process('git remote add origin ' . $url . ' &&  git push -u origin master', '/home/piotr/www/xxx');

            $process->run(function ($type, $line) use ($io) {
                $io->text($line);
            });
        } else {
            $io->block('Nie to nie.', 'WARNING', 'fg=black;bg=cyan', ' ', true);
        }

        $io->success('Mam nadzieję że się udało');

        return $this;
    }

    protected function runComposer($directory, $io)
    {
        $process = new Process('composer install && composer clear-cache && composer update', '/home/piotr/www/xxx');
        $process->setTimeout(10 * 60);

        $process->run(function ($type, $line) use ($io) {
            $io->writeln($line);
        });

        return $this;
    }

    /**
     * TODO windows
     * @param  [type] $directory [description]
     * @param  [type] $io        [description]
     * @return [type]            [description]
     */
    protected function initNode($directory, $io)
    {

        $process = new Process('npm install', '/home/piotr/www/xxx');
        $process->setTimeout(10 * 60);

        $process->run(function ($type, $line) use ($io) {
            $io->writeln($line);
        });

        return $this;
    }

    /**
     * TODO check -v, if unix install -g with sudo, if windows thrown an exception(windows suck)
     * @param  [type] $directory [description]
     * @param  [type] $io        [description]
     * @return [type]            [description]
     */
    protected function initBower($directory, $io)
    {
        $process = new Process('npm install bower && bower install', '/home/piotr/www/xxx');
        $process->setTimeout(10 * 60);

        $process->run(function ($type, $line) use ($io) {
            $io->writeln($line);
        });

        return $this;
    }

    /**
     * TODO check -v, if unix install -g with sudo, if windows thrown an exception(windows suck)
     * @param  [type] $directory [description]
     * @param  [type] $io        [description]
     * @return [type]            [description]
     */
    protected function runGulp($directory, $io)
    {
        $process = new Process('npm install es6-promise && npm install gulp && gulp', '/home/piotr/www/xxx');
        $process->setTimeout(10 * 60);

        $process->run(function ($type, $line) use ($io) {
            $io->writeln($line);
        });

        return $this;
    }

    protected function dirMod($directory, $io)
    {
        $process = new Process('chmod 775 /home/piotr/www/xxx/storage /home/piotr/www/xxx/bootstrap -R');
        $process->setTimeout(10 * 60);

        $process->run(function ($type, $line) use ($io) {
            $io->writeln($line);
        });
    }

    protected function phpConfigCheck($directory, $io)
    {
        if (ini_get('open_base_dir') == '') {
            

            //TODO windows, php -i|find/i"configuration file"
            $process = new Process("php -i | grep 'Configuration File'");
            $process->setTimeout(10 * 60);


            $process->run(function ($type, $line) use ($io) {

                $line = str_replace('Loaded Configuration File =>', '', $line);
                $line = str_replace('Configuration File (php.ini)', '', $line);
                

                $io->error('Oj, niedobrze. Pole open_base_dir nie powinno być puste. Tu masz ini.php ' . $line . "\n" .  'Napraw to i ponownie uruchom weryfikację ustawień.');
            });


            $confirm = $io->confirm('Sprawdzamy ponownie', $default = true);

                if($confirm){
                    $this->phpConfigCheck($directory, $io);
                } else {
                    throw new RuntimeException('Instalacja przerwana!!!');
                }


            
            die;
        }

        return $this;
    }

    protected function setDatabase($directory, $io)
    {

    }

    protected function makeMigration($directory, $io)
    {

    }

    protected function insertAdmin($directory, $io)
    {

    }

    protected function writeMessage($io)
    {
        $io->success('Za 5s zacznie się ostra instalacja, możesz iść zrobić sobię kawe, herbate albo iść zajarać bo trochę to potrwa.');

        sleep(5);

        return $this;
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param  string  $directory
     * @return void
     */
    protected function verifyApplicationDoesntExist($directory, OutputInterface $output)
    {
        if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
            throw new RuntimeException('Podany katalog istnieje!!!');
        }
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    private function findComposer()
    {
        if (file_exists(getcwd() . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar"';
        }

        return 'composer';
    }
}
