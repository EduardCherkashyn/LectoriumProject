<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-05
 * Time: 18:30
 */

namespace App\Services;


use App\Entity\Course;
use App\Entity\HomeTask;
use App\Entity\Homework;
use Doctrine\ORM\EntityManagerInterface;

class HomeworkService
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function createHomeworkForEachStudent(Course $course, HomeTask $homeTask):void
    {
        $students = $course->getStudents();
        foreach($students as $student){
            $homework = new Homework();
            $homework->setStudent($student)
                     ->setHomeTask($homeTask)
                     ->setStatus(0);
            $this->manager->persist($homework);
        }
        $this->manager->flush();
    }
}