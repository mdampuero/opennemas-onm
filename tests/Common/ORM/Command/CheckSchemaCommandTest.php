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

use Common\ORM\Command\CheckSchemaCommand;
use Doctrine\DBAL\Schema\Schema as DoctrineSchema;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Tests the orm:schema:check command.
 */
class CheckSchemaCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->connection = $this->getMockBuilder('Connection')
            ->setMethods([ 'getDatabasePlatform', 'selectDatabase' ])
            ->getMock();

        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->dumper = $this->getMockBuilder('Dumper')
            ->setMethods([ 'dump', 'discover' ])
            ->getMock();

        $this->entityManager = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getConnection', 'getDumper' ])
            ->getMock();


        $this->connection->expects($this->once())->method('getDatabasePlatform')
            ->willReturn('foo');

        $this->container->expects($this->once())->method('get')
            ->with('orm.manager')->willReturn($this->entityManager);

        $this->entityManager->expects($this->any())->method('getConnection')
            ->willReturn($this->connection);
        $this->entityManager->expects($this->any())->method('getDumper')
            ->willReturn($this->dumper);

        $current = $this->getMockBuilder('DoctrineSchema')
            ->setMethods([ 'getMigrateToSql' ])
            ->getMock();

        $current->expects($this->any())->method('getMigrateToSql')
            ->willReturn([ 'ADD TABLE wobble' ]);

        $this->dumper->expects($this->once())->method('dump')
            ->willReturn(new DoctrineSchema());
        $this->dumper->expects($this->once())->method('discover')
            ->willReturn($current);
    }

    /**
     * Tests execute.
     */
    public function testExecute()
    {
        $application = new Application();
        $application->add(new CheckSchemaCommand());

        $command = $application->find('orm:schema:check');
        $command->setContainer($this->container);

        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName(), 'database' => 'foo' ]);

        $output = $commandTester->getDisplay();
        $output = preg_replace(['/\e\[[0-9]*(;[0-9]*)?m/', '/\n/' ], '', $output);

        $this->assertContains('USE `foo`', $output);
        $this->assertContains('ADD TABLE wobble', $output);
    }
}
