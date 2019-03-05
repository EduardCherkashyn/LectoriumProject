<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-05
 * Time: 18:19
 */

namespace App\Controller\Api;


use App\Entity\Topic;
use App\Entity\Video;
use App\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VideoController extends AbstractController
{
    /**
     * @Route("/api/video/{id}"), methods={"GET"}
     */
    public function getOneAction(Video $video)
    {
        return $this->json($video);
    }

    /**
     * @Route("/api/topic/{id}/video"), methods={"GET"}
     */
    public function getAllOfOneTopicAction(Topic $topic)
    {
        $videos = $topic->getVideos();

        return $this->json($videos);
    }

    /**
     * @Route("/api/topic/{id}/video"), methods={"POST"}
     */
    public function createAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, Topic $topic)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $video = $serializer->deserialize($content, Video::class, 'json');
        $errors = $validator->validate($video);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $em = $this->getDoctrine()->getManager();
        /** @var Video $video */
        $video->setTopic($topic);
        $em->persist($video);
        $em->flush();

        return $this->json($video);
    }

    /**
     * @Route("/api/video/{id}"), methods={"PUT"}
     */
    public function editAction(Request $request, Video $video, ValidatorInterface $validator)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $data = json_decode($content, true);
        $video->setDescription($data['description'])
              ->setLink($data['link']);
        $errors = $validator->validate($video);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($video);
        $em->flush();

        return $this->json($video);
    }


    /**
     * @Route("/api/video/{id}"), methods={"DELETE"}
     */
    public function deleteAction(Video $video)
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        $em = $this->getDoctrine()->getManager();
        $em->remove($video);
        $em->flush();

        return $this->json('Video is deleted', 200);
    }
}