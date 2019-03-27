<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-15
 * Time: 19:07
 */

namespace App\Tests\src\Command;


use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateStudentsCommandTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('app:students-create');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);
        $commandTester->execute([
            'command'  => $command->getName(),
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('Success!', $output);
        $student = $this->entityManager->getRepository(Student::class)->findOneBy(['email' => 'student@ukr.net']);
        $this->assertNotEmpty($student,'No records found!');
        $this->assertContains("ROLE_STUDENT",$student->getRoles());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
