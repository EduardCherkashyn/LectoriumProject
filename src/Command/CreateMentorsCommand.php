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

class CreateMentorsCommand extends Command
{
    protected static $defaultName = 'app:mentors-create';

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
            ->setDescription('Creates mentors')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create mentors for a new season')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        if(!$this->manager->getRepository(Mentor::class)->findAll() == null) {
            $io->warning("Mentors already uploaded!");
        }
        else{
        $io->section('Creating mentors for the season!');
        $confirm = $io->confirm('Are you sure?');
        if ($confirm) {
            $files = file_get_contents(getcwd().'/public/Mentors/Mentors.csv');
            $data = $this->serializer->deserialize($files,Mentor::class.'[]','csv');
                /** @var Mentor $mentor */
            foreach ($data as $mentor) {
                $course = $this->courseService->filter($mentor);
                $uuid = Uuid::uuid4();
                $mentor->setPassword($this->encoder->encodePassword($mentor, $mentor->getPassword()))
                       ->setRoles(["ROLE_MENTOR"])
                       ->setApiToken($uuid->toString())
                       ->setAvatar('default.jpeg')
                       ->setCourse($course);
                $this->manager->persist($mentor);
                }
                $this->manager->flush();
                $io->success("Success!");

            }
        }
    }
}
