<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-04
 * Time: 13:41
 */

namespace App\Tests\Command;

use App\Entity\UserBaseClass;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateAdminCommandTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:admin-create');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes','admintest@admin.com', 'admin', '123456']);
        $commandTester->execute([
            'command'  => $command->getName(),
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('Success!', $output);
        $admin = $this->entityManager->getRepository(UserBaseClass::class)->findOneBy(['email' => 'admintest@admin.com']);
        $this->assertNotEmpty($admin,'No records found!');
        $this->assertContains("ROLE_ADMIN",$admin->getRoles());
        $this->entityManager->remove($admin);
        $this->entityManager->flush();
    }
}
