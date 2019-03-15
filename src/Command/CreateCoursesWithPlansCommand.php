<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-07
 * Time: 10:59
 */

namespace App\Command;

use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class CreateCoursesWithPlansCommand extends Command
{
    protected static $defaultName = 'app:courses-create';

    protected $manager;

    protected $serializer;

    public function __construct($name = null, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
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
            ->setDescription('Creates courses')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create courses for a new season')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        if(!$this->manager->getRepository(Course::class)->findAll() == null) {
            $io->warning("Courses already uploaded!");
        }else{
            $files = file_get_contents(getcwd().'/public/Courses/Courses.csv');
            $io->section('Creating courses for the season!');
            $confirm = $io->confirm('Are you sure?');
            if ($confirm) {
                $data = $this->serializer->deserialize($files,Course::class.'[]','csv');
                foreach ($data as $course) {
                    $this->manager->persist($course);
                }
                $this->manager->flush();
                $io->success("Success!");
            }
        }
    }

}