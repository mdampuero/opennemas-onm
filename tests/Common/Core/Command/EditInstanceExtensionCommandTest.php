<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Command;

use Common\Core\Command\EditInstanceExtensionCommand;
use Common\Model\Entity\Instance;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Tests the orm:config:clear command.
 */
class EditInstanceExtensionCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->il = $this->getMockBuilder('Common\Core\Component\Loader\InstanceLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'loadInstanceByName', 'getInstance' ])
            ->getMock();

        $this->loader = $this->getMockBuilder('Common\Core\Component\Loader\CoreLoader')
            ->disableOriginalConstructor()
            ->setMethods(['configureInstance' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'persist' ])
            ->getMock();

        $this->input = $this->getMockBuilder('Input')
            ->setMethods([ 'getArgument', 'getOption' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $application = new Application();
        $application->add(new EditInstanceExtensionCommand());

        $this->command = $application->find('core:instance:extension');
        $this->command->setContainer($this->container);

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * Returns a mock basing on the service name.
     *
     * @param string $name The service name.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'orm.manager':
                return $this->em;

            case 'core.loader.instance':
                return $this->il;

            case 'core.loader':
                return $this->loader;

            default:
                return null;
        }

        return null;
    }

    /**
     * Tests execute without changes.
     */
    public function testExecuteWithNoChanges()
    {
        $instance = new Instance([ 'internal_name' => 'baz' ]);

        $this->il->expects($this->any())->method('loadInstanceByName')
            ->with('baz')->willReturn($this->il);
        $this->il->expects($this->once())->method('getInstance')
            ->willReturn($instance);


        $this->loader->expects($this->once())->method('configureInstance')
            ->with($instance)->willReturn($this->loader);

        $this->commandTester->execute([
            'instance' => 'baz',
            'uuid'     => 'thud',
            'command'  => $this->command->getName(),
            '--remove' => true,
        ]);
    }

    /**
     * Tests execute with changes.
     */
    public function testExecuteWithChanges()
    {
        $instance = new Instance([ 'internal_name' => 'baz' ]);

        $this->il->expects($this->any())->method('loadInstanceByName')
            ->with('baz')->willReturn($this->il);
        $this->il->expects($this->once())->method('getInstance')
            ->willReturn($instance);

        $this->loader->expects($this->once())->method('configureInstance')
            ->with($instance)->willReturn($this->loader);

        $this->em->expects($this->once())->method('persist')
            ->with($instance);

        $this->commandTester->execute([
            'instance' => 'baz',
            'uuid'     => 'thud',
            'command'  => $this->command->getName()
        ]);
    }

    /**
     * Tests execute with changes on activated modules.
     */
    public function testExecuteWithChangesActivated()
    {
        $instance = new Instance([
            'internal_name'     => 'baz',
            'activated_modules' => [ 'qwert', 'thud', 'waldo' ],
        ]);

        $this->il->expects($this->any())->method('loadInstanceByName')
            ->with('baz')->willReturn($this->il);
        $this->il->expects($this->once())->method('getInstance')
            ->willReturn($instance);

        $this->loader->expects($this->once())->method('configureInstance')
            ->with($instance)->willReturn($this->loader);

        $this->em->expects($this->once())->method('persist')
            ->with($instance);

        $this->commandTester->execute([
            'instance'   => 'baz',
            'uuid'       => 'thud',
            'command'    => $this->command->getName(),
            '--activate' => true,
            '--remove' => true,
        ]);
    }
}
