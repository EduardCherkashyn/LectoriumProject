<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-05
 * Time: 18:44
 */

namespace App\Controller\Api;


use App\Entity\Homework;
use App\Entity\Student;
use App\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeWorkController extends AbstractController
{
    /**
     * @Route("/api/hometask/{id}"), methods={"GET"}
     */
    public function getOneAction(Homework $homework)
    {
        return $this->json($homework);
    }

    /**
     * @Route("/api/student/{id}/homework"), methods={"GET"}
     */
    public function getAllOfOneStudentAction(Student $student)
    {
        $homeWorks = $student->getHomeworks();

        return $this->json($homeWorks);
    }

    /**
     * @Route("/api/homework/{id}"), methods={"PUT"}
     */
    public function editAction(Request $request, Homework $homework, ValidatorInterface $validator)
    {
        /** @var Student $student */
        $student = $this->getUser();
        if(!$student->getHomeworks()->contains($homework)){
            throw new JsonHttpException(403, 'You are not the owner of a current object!');
        }
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $data = json_decode($content, true);
        $homework->setGitHubRepository($data['repository']);
        $errors = $validator->validate($homework);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($homework);
        $em->flush();

        return $this->json($homework);
    }

    /**
     * @Route("/api/homework-check/{id}"), methods={"PUT"}
     */
    public function checkAction(Request $request, Homework $homework, ValidatorInterface $validator)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $data = json_decode($content, true);
        $homework->setStatus($data['repository']);
        $errors = $validator->validate($homework);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($homework);
        $em->flush();

        return $this->json($homework);
    }

}