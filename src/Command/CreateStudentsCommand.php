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
use App\Services\FilterCoursesForUserService;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class CreateStudentsCommand extends Command
{
    protected static $defaultName = 'app:students-create';

    protected $manager;

    protected $encoder;

    protected $serializer;

    protected $courseService;

    public function __construct($name = null, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, FilterCoursesForUserService $coursesForUserService)
    {
        $this->courseService = $coursesForUserService;
        $this->manager = $manager;
        $this->encoder = $encoder;
        $this->serializer = new Serializer(
            [new GetSetMethodNormalizer(), new ArrayDenormalizer()],
            [new CsvEncoder()]
        );;
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
        if(!$this->manager->getRepository(Student::class)->findAll() == null) {
            $io->warning("Students already uploaded!");
        }
        else{
        $io->section('Creating students for the season!');
        $confirm = $io->confirm('Are you sure?');
        if ($confirm) {
            $files = file_get_contents(getcwd().'/public/Students/Students.csv');
            $data = $this->serializer->deserialize($files,Student::class.'[]','csv');
            /** @var Student $student */
            foreach ($data as $student) {
                $course = $this->courseService->filter($student);
                $uuid = Uuid::uuid4();
                $student->setPassword($this->encoder->encodePassword($student, $student->getPassword()))
                    ->setRoles(["ROLE_STUDENT"])
                    ->setApiToken($uuid->toString())
                    ->setAvatar('default.jpeg')
                    ->setCourse($course);
                $this->manager->persist($student);
            }
            $this->manager->flush();
            $io->success("Success!");
            }
        }
    }
}
