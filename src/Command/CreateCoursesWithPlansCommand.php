<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-26
 * Time: 13:07
 */

namespace App\Command;

use App\Entity\Course;
use App\Entity\Plan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateCoursesWithPlansCommand extends Command
{
    protected static $defaultName = 'app:courses-create';

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
            ->setDescription('Creates courses')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create courses for a new season')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        if(!$this->manager->getRepository(Course::class)->findAll() == null)
        {
          $io->warning("Courses already uploaded!");

          return;
        }
        $file = file_get_contents(getcwd().'/public/Courses/Courses.txt');
        $data = explode("\n", $file);
        $io->section('Creating courses for the season!');
        $confirm = $io->confirm('Are you sure?');
        if ($confirm) {
            foreach ($data as $value) {
                $course = new Course();
                $plan = new Plan();
                $course->setName($value);
                $course->setYear(new \DateTime())
                       ->setPlan($plan);
                $this->manager->persist($course);
            }
            $this->manager->flush();
            $io->success("Success!");
        }
    }
}
