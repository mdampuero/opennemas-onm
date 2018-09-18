<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\ORM\Command;

use Common\ORM\Command\ClearConfigCommand;
use Doctrine\DBAL\Schema\Schema as DoctrineSchema;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Tests the orm:config:clear command.
 */
class ClearConfigCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Cache')
            ->setMethods([ 'remove' ])
            ->getMock();

        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->cacheManager = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->container->expects($this->once())->method('get')
            ->with('cache.manager')->willReturn($this->cacheManager);

        $this->cacheManager->expects($this->any())->method('getConnection')
            ->willReturn($this->cache);
    }

    /**
     * Tests execute.
     */
    public function testExecute()
    {
        $this->cache->expects($this->once())->method('remove');

        $application = new Application();
        $application->add(new ClearConfigCommand());

        $command = $application->find('orm:config:clear');
        $command->setContainer($this->container);

        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName() ]);

        $output = $commandTester->getDisplay();

        $this->assertContains('[OK]', $output);
    }

    /**
     * Tests execute when an exception is thrown.
     */
    public function testExecuteWithError()
    {
        $this->cache->expects($this->once())->method('remove')
            ->will($this->throwException(new \Exception));

        $application = new Application();
        $application->add(new ClearConfigCommand());

        $command = $application->find('orm:config:clear');
        $command->setContainer($this->container);

        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName() ]);

        $output = $commandTester->getDisplay();

        $this->assertContains('[FAIL]', $output);
    }
}
