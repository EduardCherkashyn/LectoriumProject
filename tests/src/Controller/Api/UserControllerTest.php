<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-26
 * Time: 17:28
 */

namespace App\Tests\Controller\Api;

use App\Entity\Admin;
use App\Entity\Course;
use App\Entity\Student;
use App\Tests\AbstractTest;

class UserControllerTest extends AbstractTest
{
    /**
     * @test
     * @dataProvider userData
     */
    public function testLogin($password, $email, $expected): void
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
        /** @var Student $student */
        $student = $this->entityManager->getRepository(Student::class)->findOneBy([]);

        return [
            'Valid data' => [$student->getName(), $student->getEmail(), 200],
            'Invalid Data' => ['1111', 'ed@ukr.net', 404],
            'Empty Data' => ['', '', 404]
        ];
    }

    /**
     * @test
     * @dataProvider passwordData
     */
    public function testPasswordChange($passwordOld, $passwordNew, $token, $expected): void
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
        /** @var Student $student */
        $student = $this->entityManager->getRepository(Student::class)->findOneBy([]);

        return [
            'Valid data' => ['Eduard', 'Eduard',$student->getApiToken(), 200],
            'Invalid data' => ['Eduard', '11',$student->getApiToken(), 400],
            'UnAuthorized' => ['111', '111111','', 403],
            'Invalid Old password' => ['12', '111111',$student->getApiToken(), 400]
        ];
    }

    /**
     * @test
     * @dataProvider getoneData
     */
    public function testGetOne($id, $token, $expected): void
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

    /**
     * @test
     * @dataProvider getAllByCourseData
     */
    public function testGetAllStudentsOfOneCourse($id, $token, $expected): void
    {

        $this->client->request(
            'GET',
            '/api/courses/'.$id.'/student',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => $token
            ]
        );
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    public function getAllByCourseData()
    {
        /** @var Course $course */
        $course = $this->entityManager->getRepository(Course::class)->findOneBy([]);

        return [
            'Valid data' => [$course->getId(), $this->token_mentor, 200],
            'Invalid data' => [111111111, $this->token_mentor, 404],
            'UnAuthorized' => [$course->getId(), $this->token_student, 403],
        ];
    }


    /**
     * @test
     * @dataProvider deleteStudentData
     */
    public function testDeleteStudent($id, $token, $expected): void
    {

        $this->client->request(
            'DELETE',
            '/api/student/'.$id,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => $token
            ]
        );
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    public function deleteStudentData()
    {
        /** @var Student $student */
        $student = $this->entityManager->getRepository(Student::class)->findOneBy([]);
        /** @var Admin $admin */
        $admin = $this->entityManager->getRepository(Admin::class)->findOneBy([]);

        return [
            'Invalid data' => [111111111, $admin->getApiToken(), 404],
            'UnAuthorized' => [$student->getId(), $this->token_mentor, 403],
            'Valid data' => [$student->getId(), $admin->getApiToken(), 200]
        ];
    }

}
