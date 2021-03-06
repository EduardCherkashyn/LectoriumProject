<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-05
 * Time: 17:10
 */

namespace App\Controller\Api;

use App\Entity\HomeTask;
use App\Entity\Topic;
use App\Exception\JsonHttpException;
use App\Services\HomeworkService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeTaskController extends AbstractController
{
    /**
     * @Route("/api/hometask/{id}"), methods={"GET"}
     */
    public function getOneAction(HomeTask $homeTask)
    {
        return $this->json($homeTask);
    }

    /**
     * @Route("/api/hometask/topic/{id}"), methods={"GET"}
     */
    public function getAllOfOneTopicAction(Topic $topic)
    {
        $homeTasks = $topic->getHomeTasks();

        return $this->json($homeTasks);
    }

    /**
     * @Route("/api/hometask/topic/{id}"), methods={"POST"}
     */
    public function createAction(Request $request,
                                 SerializerInterface $serializer,
                                 ValidatorInterface $validator,
                                 Topic $topic,
                                 HomeworkService $homeworkService)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        /** @var HomeTask $homeTask */
        $homeTask = $serializer->deserialize($content, HomeTask::class, 'json');
        $errors = $validator->validate($homeTask);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $homeworkService->createHomeworkForEachStudent($topic->getPlan()->getCourse(), $homeTask);
        $em = $this->getDoctrine()->getManager();
        $homeTask->setTopic($topic);
        $em->persist($homeTask);
        $em->flush();

        return $this->json($homeTask);
    }

    /**
     * @Route("/api/hometask/{id}"), methods={"PUT"}
     */
    public function editAction(Request $request, HomeTask $homeTask, ValidatorInterface $validator)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $data = json_decode($content, true);
        $homeTask->setDescription($data['description']);
        $errors = $validator->validate($homeTask);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($homeTask);
        $em->flush();

        return $this->json($homeTask);
    }


    /**
     * @Route("/api/hometask/{id}"), methods={"DELETE"}
     */
    public function deleteAction(HomeTask $homeTask)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        $em = $this->getDoctrine()->getManager();
        $em->remove($homeTask);
        $em->flush();

        return $this->json('Hometask is deleted', 200);
    }
}
