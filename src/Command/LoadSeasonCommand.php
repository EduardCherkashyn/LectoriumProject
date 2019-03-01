<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-26
 * Time: 14:48
 */

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadSeasonCommand extends Command
{
    protected static $defaultName = 'app:season-create';

    protected $manager;


    public function __construct($name = null, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates season with all data')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a new season')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->section('Creating the new  season!');
        $confirm = $io->confirm('Are you sure?');
        if ($confirm) {
            $command = $this->getApplication()->find('app:admin-create');
            $arguments = [];
            $greetInput = new ArrayInput($arguments);
            $command->run($greetInput, $output);

            $command = $this->getApplication()->find('app:courses-create');
            $arguments = [];
            $greetInput = new ArrayInput($arguments);
            $command->run($greetInput, $output);

            $command = $this->getApplication()->find('app:mentors-create');
            $arguments = [];
            $greetInput = new ArrayInput($arguments);
            $command->run($greetInput, $output);

            $command = $this->getApplication()->find('app:students-create');
            $arguments = [];
            $greetInput = new ArrayInput($arguments);
            $command->run($greetInput, $output);

            $io->success("Season is created successfully!!!");
        }
    }
}
