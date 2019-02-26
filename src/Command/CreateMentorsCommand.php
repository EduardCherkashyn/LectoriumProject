<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-26
 * Time: 14:36
 */

namespace App\Command;


use App\Entity\Course;
use App\Entity\Mentor;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateMentorsCommand extends Command
{
    protected static $defaultName = 'app:mentors-create';

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
            ->setDescription('Creates mentors')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create mentors for a new season')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->section('Creating mentors for the season!');
        $confirm = $io->confirm('Are you sure?');
        if($confirm) {
            $file = file_get_contents(getcwd().'/public/Mentors/Mentors.txt');
            $data = explode("\n", $file);
            $mentors = [];
            foreach ($data as $value) {
                $mentor = explode(':', $value);
                $mentors[] = $mentor;
            }
            $courses = $this->manager->getRepository(Course::class)->findAll();
            foreach ($courses as $course){
                $name = $course->getName();
                foreach ($mentors as $value) {
                    if ($name == $value[3]) {
                        $uuid = Uuid::uuid4();
                        $mentor = new Mentor();
                        $mentor->setEmail($value[0])
                            ->setPassword($this->encoder->encodePassword($mentor, $value[1]))
                            ->setName($value[2])
                            ->setCourse($course)
                            ->setRoles(["ROLE_MENTOR"])
                            ->setApiToken($uuid->toString());
                        $this->manager->persist($mentor);
                        unset($value);
                    }
                }
            }

            $this->manager->flush();
            $io->success("Success!");
        }

    }
}