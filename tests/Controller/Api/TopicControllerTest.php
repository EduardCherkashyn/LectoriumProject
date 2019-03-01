<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-27
 * Time: 13:26
 */

namespace App\Tests\Controller\Api;

use App\Tests\AbstractTest;

class TopicControllerTest extends AbstractTest
{
    /**
     * @test
     * @dataProvider userData
     */
    public function testCreateAction($data, $token, $expected): void
    {
        $this->client->request(
            'POST',
            '/api/topic',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => $token
            ],
            json_encode($data)
        );
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    public function userData()
    {
        return [
            'Valid data' => [["name" => "test name", "description" => "some text", "status" => 0], $this->token_mentor, 200],
            'Invalid token' => [["name" => "test name", "description" => "some text", "status" => 0], $this->token_student, 403],

            ];
    }
}
