<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-27
 * Time: 14:57
 */

namespace App\Controller\Api;

use App\Entity\Course;
use App\Entity\Topic;
use App\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TopicController extends AbstractController
{
    /**
     * @Route("/api/topic/{id}", methods={"GET"})
     */
    public function getOneAction(Topic $topic)
    {
        return $this->json($topic);
    }

    /**
     * @Route("/api/topic", methods={"GET"})
     */
    public function getAllAction()
    {
        $user = $this->getUser();
        /** @var Course $course */
        $course = $user->getCourse();
        $topics = $course->getPlan()->getTopics();

        return $this->json($topics);
    }

    /**
     * @Route("/api/topic", methods={"POST"})
     */
    public function createAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $user = $this->getUser();
        /** @var Course $course */
        $course = $user->getCourse();
        /** @var Topic $topic */
        $topic = $serializer->deserialize($content, Topic::class, 'json');
        $errors = $validator->validate($topic);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $em = $this->getDoctrine()->getManager();
        $plan = $course->getPlan();
        $topic->setPlan($plan);
        $em->persist($topic);
        $em->flush();

        return $this->json($topic);
    }

    /**
     * @Route("/api/topic/{id}", methods={"PUT"})
     */
    public function editAction(Request $request, ValidatorInterface $validator, Topic $topic)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $data = json_decode($content, true);
        $topic->setName($data['name'])
              ->setDescription($data['description'])
              ->setStatus($data['status']);
        $errors = $validator->validate($topic);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($topic);
        $em->flush();

        return $this->json($topic);
    }

    /**
     * @Route("/api/topic/{id}", methods={"DELETE"})
     */
    public function deleteAction(Topic $topic)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        $em = $this->getDoctrine()->getManager();
        $em->remove($topic);
        $em->flush();

        return $this->json('Topic is deleted',200);
    }
}
