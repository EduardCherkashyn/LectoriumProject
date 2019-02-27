<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-26
 * Time: 17:28
 */

namespace App\Tests\Controller\Api;

use App\Entity\UserBaseClass;
use App\Tests\AbstractTest;

class UserControllerTest extends AbstractTest
{
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
            'Valid data' => ['123456', 'admin@admin.com', 200],
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
        $student = $this->entityManager->getRepository(UserBaseClass::class)->findOneBy(['email' => 'student@ukr.net']);

        return [
            'Valid data' => ['Eduard', '111111',$student->getApiToken(), 200],
            'Invalid data' => ['Eduard', '11',$student->getApiToken(), 400],
            'UnAuthorized' => ['111', '111111','', 403],
            'Invalid Old password' => ['12', '111111',$student->getApiToken(), 400],
        ];
    }
}