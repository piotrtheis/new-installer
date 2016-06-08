<?php

namespace CMS\Installer\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ChoiceQuestion;

class NewCommand extends SymfonyCommand
{
    /**
     * The input interface.
     *
     * @var InputInterface
     */
    public $input;

    /**
     * The output interface.
     *
     * @var OutputInterface
     */
    public $output;

    /**
     * The path to the new Spark installation.
     *
     * @var string
     */
    public $path;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('new')
            ->setDescription('Instalacja cms-a')
            ->addArgument('name', InputArgument::REQUIRED, 'Nazwa katalogu');
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = new SymfonyStyle($input, $output);

        $this->path = getcwd().'/'.$input->getArgument('name');

        $installers = [
            Installation\CheckOs::class,
            Installation\MakeCmsDir::class,
            Installation\CloneCmsRepo::class,
            Installation\ComposerInstall::class,
            Installation\CheckNodeVersion::class,
            Installation\RunNpmInstall::class,
            Installation\RunBowerInstall::class,
            Installation\RunGulp::class,
            Installation\DatabaseConfiguration::class,
            Installation\DatabaseMigration::class,
            // Installation\DatabaseSeeding::class,
            Installation\DirMod::class,
            Installation\InitGit::class,
            Installation\MakeAdmin::class,
            Installation\MakeApacheVirtualHost::class,
            //TODO
            // Installation\ServerRequirements::class,
        ];

        foreach ($installers as $installer) {
            (new $installer($this, $input->getArgument('name'), $this->path))->install();
        }
    }
}
