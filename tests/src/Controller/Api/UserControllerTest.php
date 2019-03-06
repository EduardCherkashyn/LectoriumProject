<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-26
 * Time: 17:28
 */

namespace App\Tests\Controller\Api;

use App\Entity\Student;
use App\Tests\AbstractTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;

class UserControllerTest extends AbstractTest
{
    /** @var Client $client */
    protected $client;
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * @test
     * @dataProvider userData
     */
    public function testLoginAction($password, $email, $expected): void
    {
        $data = [
            'password'=> $password,
            'email'=> $email
        ];
        $this->client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    public function userData()
    {
        return [
            'Valid data' => ['Eduard', 'student@ukr.net', 200],
            'Invalid Data' => ['1111', 'ed@ukr.net', 404],
            'Empty Data' => ['', '', 404]
        ];
    }

    /**
     * @test
     * @dataProvider passwordData
     */
    public function testPasswordChangeAction($passwordOld, $passwordNew, $token, $expected): void
    {
        $data = [
            'oldPassword'=> $passwordOld,
            'newPassword'=> $passwordNew
        ];
        $this->client->request(
            'PUT',
            '/api/user/password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => $token
            ],
            json_encode($data)
        );
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    public function passwordData()
    {
        return [
            'Valid data' => ['Eduard', 'Eduard',$this->token_student, 200],
            'Invalid data' => ['Eduard', '11',$this->token_student, 400],
            'UnAuthorized' => ['111', '111111','', 403],
            'Invalid Old password' => ['12', '111111',$this->token_student, 400]
        ];
    }

    /**
     * @test
     * @dataProvider getoneData
     */
    public function testgetOneAction($id, $token, $expected): void
    {

        $this->client->request(
            'GET',
            '/api/user/'.$id,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => $token
            ]
        );
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    public function getOneData()
    {
        /** @var Student $student */
        $student = $this->entityManager->getRepository(Student::class)->findOneBy([]);

        return [
            'Valid data' => [$student->getId(), $this->token_mentor, 200],
            'Invalid data' => [111111111, $this->token_mentor, 404],
            'UnAuthorized' => [$student->getId(), $this->token_student, 403],
        ];
    }
}
