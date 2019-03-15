<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-15
 * Time: 23:20
 */

namespace App\Services;


use App\Entity\Course;
use App\Entity\UserBaseClass;
use Doctrine\ORM\EntityManagerInterface;

class FilterCoursesForUserService
{
    private $manager;

    private $courses;

    public function __construct(EntityManagerInterface $manager)
    {
       $this->manager = $manager;
       $this->courses = $manager->getRepository(Course::class)->findAll();
    }

    public function filter(UserBaseClass $user):Course
    {
        $plainCourse = $user->getPlainCourse();
        $course = array_filter($this->courses, function ($element) use ($plainCourse) {
            /** @var Course $element */
            if ($element->getName()!=null && $element->getName() == $plainCourse) {
                return TRUE;
            }
            return FALSE;
        });

        return array_shift($course);
    }
}
