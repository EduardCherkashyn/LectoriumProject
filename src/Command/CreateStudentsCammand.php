<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-26
 * Time: 13:06
 */

namespace App\Command;

use App\Entity\Course;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateStudentsCammand extends Command
{
    protected static $defaultName = 'app:students-create';

    protected $manager;

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
            ->setDescription('Creates students')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create students for a new season')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->section('Creating students for the season!');
        $confirm = $io->confirm('Are you sure?');
        if ($confirm) {
            $file = file_get_contents(getcwd().'/public/Students/Students.txt');
            $data = explode("\n", $file);
            $students = [];
            foreach ($data as $value) {
                $student = explode(':', $value);
                $students[] = $student;
            }
            $courses = $this->manager->getRepository(Course::class)->findAll();
            foreach ($courses as $course) {
                $name = $course->getName();
                foreach ($students as $value) {
                    if ($name == $value[3]) {
                        $uuid = Uuid::uuid4();
                        $student = new Student();
                        $student->setEmail($value[0])
                        ->setPassword($this->encoder->encodePassword($student, $value[1]))
                        ->setName($value[2])
                        ->setCourse($course)
                        ->setRoles(["ROLE_STUDENT"])
                        ->setApiToken($uuid->toString());
                        $this->manager->persist($student);
                        unset($value);
                    }
                }
            }

            $this->manager->flush();
            $io->success("Success!");
        }
    }
}
