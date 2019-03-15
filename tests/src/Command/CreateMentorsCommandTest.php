<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-15
 * Time: 19:11
 */

namespace App\Tests\src\Command;

use App\Entity\Mentor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateMentorsCommandTest extends WebTestCase
{
    /** @var EntityManagerInterface */
    protected $entityManager;


    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        $client = static::createClient(['environment' => 'test']);
        $this->entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        parent::__construct($name, $data, $dataName);
    }

    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('app:mentors-create');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);
        $commandTester->execute([
            'command'  => $command->getName(),
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('Success!', $output);
        $mentor= $this->entityManager->getRepository(Mentor::class)->findOneBy(['email' => 'edik@ukr.net']);
        $this->assertNotEmpty($mentor,'No records found!');
        $this->assertContains("ROLE_MENTOR",$mentor->getRoles());
    }
}
