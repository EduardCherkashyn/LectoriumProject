<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-22
 * Time: 13:02
 */

namespace App\Controller\Api;

use App\Entity\Course;
use App\Entity\Student;
use App\Entity\UserBaseClass;
use App\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/login"), methods={"POST"}
     */
    public function loginAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $data = json_decode($content, true);
        $user = $this->getDoctrine()->getRepository(UserBaseClass::class)->findOneBy(['email'=>$data['email']]);
        if (!$user instanceof UserBaseClass || !$passwordEncoder->isPasswordValid($user, $data['password'])) {
            throw new JsonHttpException(404, 'Bad Request');
        }

        return $this->json($user);
    }

    /**
     * @Route("/api/user/password"), methods={"PUT"}
     */
    public function changePasswordAction(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $data = json_decode($content, true);
        $user = $this->getUser();
        $checkPass = $passwordEncoder->isPasswordValid($user, $data['oldPassword']);
        if (!$checkPass == true) {
            throw new JsonHttpException(400, 'Wrong current password!');
        }
        if (strlen($data['newPassword']) < 3 || strlen($data['newPassword']) > 10) {
            throw new JsonHttpException(400, 'Password length is not correct!It suppose to be not less than 3 and not more than 10 digits!');
        }
        $newPassword = $passwordEncoder->encodePassword($user, $data['newPassword']);
        $user->setPassword($newPassword);
        $errors = $validator->validate($user);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($user);
    }

    /**
     * @Route("/api/user/{id}"), methods={"GET"}
     */
    public function getOneAction(UserBaseClass $user)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');

        return $this->json($user);
    }

    /**
     * @Route("/api/courses/{id}/student"), methods={"GET"}
     */
    public function getAllStudentsOfOneCourseAction(Course $course)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        $students = $course->getStudents();

        return $this->json($students);
    }

    /**
     * @Route("/api/student"), methods={"DELETE"}
     */
    public function deleteStudentAction(Student $student)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $em->remove($student);
        $em->flush();

        return $this->json('Student is deleted',200);
    }
}
