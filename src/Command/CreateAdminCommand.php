<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-22
 * Time: 13:11
 */

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{
// the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:admin-create';

    protected  $manager;

    protected $encoder;


    public function __construct($name = null, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates an admin')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create an admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->section('Creating admin user!');

        $confirm = $io->confirm('Are you sure?');
        if($confirm) {
            $question = new Question('Please enter your email:');
            $email = $io->askQuestion($question);
            $question1 = new Question('Please enter your full Name:');
            $name = $io->askQuestion($question1);
            $question2 = new Question('Please enter your password:');
            $password = $io->askQuestion($question2);
            $uuid = Uuid::uuid4();
            $admin = new Admin();
            $admin->setName($name)
                ->setEmail($email)
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($this->encoder->encodePassword($admin, $password))
                ->setApiToken($uuid->toString($uuid));
            $this->manager->persist($admin);
            $this->manager->flush();
            $io->success("Success!");
        }

    }
}