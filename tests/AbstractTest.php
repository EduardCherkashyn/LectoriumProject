<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-17
 * Time: 18:24
 */

namespace App\Tests;

use App\Entity\UserBaseClass;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractTest extends WebTestCase
{
    /** @var Client $client */
    protected $client;
    /** @var EntityManagerInterface */
    protected $entityManager;

    protected $token_student;

    protected $token_mentor;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        /** @var @var UserBaseClass $student */
        $student = $this->entityManager->getRepository(UserBaseClass::class)->findOneBy(['email'=>'student@ukr.net']);
        $this->token_student = $student->getApiToken();
        $mentor =$this->entityManager->getRepository(UserBaseClass::class)->findOneBy(['email'=>'edik@ukr.net']);
        $this->token_mentor = $mentor->getApiToken();

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    protected function tearDown()
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->getConnection()->rollback();
        }
    }
}
