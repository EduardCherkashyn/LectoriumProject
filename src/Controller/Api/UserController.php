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
use App\Services\AvatarService;
use App\Services\FilterCoursesForUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/api/student", methods={"POST"})
     */
    public function userCreateAction(Request $request,
                                     SerializerInterface $serializer,
                                     ValidatorInterface $validator,
                                     FilterCoursesForUserService $courseService)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        /** @var Student $student */
        $student = $serializer->deserialize($content, Student::class, 'json');
        $course = $courseService->filter($student);
        $student->setCourse($course);
        $errors = $validator->validate($student);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();

        return $this->json($student);
    }

    /**
     * @Route("/login", methods={"POST"})
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
     * @Route("/api/user/password", methods={"PUT"})
     */
    public function changePasswordAction(Request $request,
                                         ValidatorInterface $validator,
                                         UserPasswordEncoderInterface $passwordEncoder)
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
     * @Route("/api/user/{id}", methods={"GET"})
     */
    public function getOneAction(UserBaseClass $user)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');

        return $this->json($user);
    }

    /**
     * @Route("/api/courses/{id}/student", methods={"GET"})
     */
    public function getAllStudentsOfOneCourseAction(Course $course)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        $students = $course->getStudents();

        return $this->json($students);
    }

    /**
     * @Route("/api/student/{id}", methods={"DELETE"})
     */
    public function deleteStudentAction(Student $student)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $em->remove($student);
        $em->flush();

        return $this->json('Student is deleted',200);
    }

    /**
     * @Route("/api/avatar", methods={"POST"})
     */
    public function avatarUploadAction(Request $request, AvatarService $avatarService)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        /** @var UserBaseClass $user */
        $user = $this->getUser();
        $avatarService->upload($user, $content);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($user);
    }

    /**
     * @Route("/api/avatar", methods={"DELETE"})
     */
    public function avatarDeleteAction()
    {
        /** @var UserBaseClass $user */
        $user = $this->getUser();
        $user->setAvatar('default.jpeg');
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($user);
    }
}
