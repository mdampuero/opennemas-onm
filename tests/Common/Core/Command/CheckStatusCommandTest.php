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

use Common\Core\Command\CheckStatusCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Tests the orm:config:clear command.
 */
class CheckStatusCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->checker = $this->getMockBuilder('Checker')
            ->setMethods([
                'checkCacheConnection',
                'checkDatabaseConnection',
                'checkNfs',
                'getCacheConfiguration',
                'getDatabaseConfiguration',
            ])
            ->getMock();

        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->container->expects($this->once())->method('get')
            ->with('core.status.checker')->willReturn($this->checker);

        $application = new Application();
        $application->add(new CheckStatusCommand());

        $this->command = $application->find('core:status:check');
        $this->command->setContainer($this->container);

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * Tests execute.
     */
    public function testExecute()
    {
        $this->checker->expects($this->once())->method('checkCacheConnection')->willReturn(true);
        $this->checker->expects($this->once())->method('checkDatabaseConnection')->willReturn(true);
        $this->checker->expects($this->once())->method('checkNfs')->willReturn(true);
        $this->checker->expects($this->once())->method('getCacheConfiguration')->willReturn([
            'name' => 'flob',
        ]);

        $this->checker->expects($this->once())->method('getDatabaseConfiguration')->willReturn([
            'name' => 'fred',
        ]);


        $this->commandTester->execute(
            [ 'command' => $this->command->getName() ],
            [ 'verbosity' => OutputInterface::VERBOSITY_VERBOSE ]
        );

        $output = $this->commandTester->getDisplay();

        $this->assertContains('Checking NFS status... DONE', $output);
        $this->assertContains('Checking database connection... DONE', $output);
        $this->assertContains('fred', $output);
        $this->assertContains('Checking cache connection... DONE', $output);
        $this->assertContains('flob', $output);
    }

    /**
     * Tests execute when something fails.
     */
    public function testExecuteWithErrors()
    {
        $this->checker->expects($this->once())->method('checkCacheConnection')->willReturn(false);
        $this->checker->expects($this->once())->method('checkDatabaseConnection')->willReturn(false);
        $this->checker->expects($this->once())->method('checkNfs')->willReturn(false);

        $this->commandTester->execute([ 'command' => $this->command->getName() ]);
        $output = $this->commandTester->getDisplay();

        $this->assertContains('Checking NFS status... FAIL', $output);
        $this->assertContains('Checking database connection... FAIL', $output);
        $this->assertContains('Checking cache connection... FAIL', $output);
    }
}
