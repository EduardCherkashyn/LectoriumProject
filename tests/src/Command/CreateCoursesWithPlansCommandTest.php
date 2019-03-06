<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-03-04
 * Time: 17:43
 */

namespace App\Tests\src\Command;

use App\Entity\Course;
use App\Entity\Plan;
use App\Tests\AbstractTest;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateCoursesWithPlansCommandTest extends AbstractTest
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:courses-create');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('Success!', $output);
        $courses = $this->entityManager->getRepository(Course::class)->findAll();
        $plans = $this->entityManager->getRepository(Plan::class)->findAll();
        $this->assertNotEmpty($courses);
        $this->assertNotEmpty($plans);
        $this->assertEquals(count($courses),count($plans));
    }
}