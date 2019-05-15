<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Cache\Command;

use Common\Core\Command\OptimizeImagesCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Tests the assets:image:optimize command.
 */
class OptimizeImagesCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->processor = $this->getMockBuilder('Common\Core\Component\Image\Processor')
            ->disableOriginalConstructor()
            ->setMethods([
                'apply', 'open', 'optimize', 'save'
            ])->getMock();

        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->input = $this->getMockBuilder('Input')
            ->setMethods([ 'getArgument', 'getOption' ])
            ->getMock();

        $this->command = $this->getMockBuilder('Common\Core\Command\OptimizeImagesCommand')
            ->setMethods([ 'getFiles' ])
            ->getMock();

        $this->command->setContainer($this->container);

        $this->command->input = $this->input;
    }

    /**
     * Returns a mock basing on the service name.
     *
     * @param string $name The service name.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.image.processor':
                return $this->processor;
        }

        return null;
    }

    /**
     * Tests execute for invalid resize option.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testExecuteWhenInvalidResizeOption()
    {
        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'source'      => 'glork',
            'command'     => $this->command->getName(),
            '--resize'    => 'quux',
        ]);
    }

    /**
     * Tests execute for invalid source argument.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testExecuteWhenInvalidSource()
    {
        $this->command->expects($this->once())->method('getFiles')
            ->will($this->throwException(new \InvalidArgumentException()));

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'source'      => 'glork',
            'command'     => $this->command->getName(),
            '--resize'    => '100x100',
        ]);
    }

    /**
     * Tests execute when no options provided.
     */
    public function testExecuteWhenNoOptions()
    {
        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRealPath' ])
            ->getMock();

        $file->expects($this->any())->method('getRealPath')
            ->willReturn('glorp/norf/wibble.png');

        $this->command->expects($this->once())->method('getFiles')
            ->willReturn([ $file ]);

        $this->processor->expects($this->once())->method('open')
            ->with('glorp/norf/wibble.png');
        $this->processor->expects($this->once())->method('save')
            ->with('glorp/norf/wibble.png');

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'source'  => 'glork',
            'command' => $this->command->getName(),
        ]);
    }

    /**
     * Tests execute when optimize and resize options provided.
     */
    public function testExecuteWhenOptionsProvided()
    {
        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRealPath' ])
            ->getMock();

        $file->expects($this->any())->method('getRealPath')
            ->willReturn('glorp/norf/wibble.png');

        $this->command->expects($this->once())->method('getFiles')
            ->willReturn([ $file ]);

        $this->processor->expects($this->once())->method('open')
            ->with('glorp/norf/wibble.png');
        $this->processor->expects($this->once())->method('optimize');
        $this->processor->expects($this->once())->method('apply')
            ->with('thumbnail', [ 250, 250 ]);
        $this->processor->expects($this->once())->method('save')
            ->with('glorp/norf/wibble.png');

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'source'     => 'glork',
            'command'    => $this->command->getName(),
            '--optimize' => true,
            '--resize'   => '250x250',
        ]);
    }

    /**
     * Tests end.
     */
    public function testEnd()
    {
        $method   = new \ReflectionMethod($this->command, 'end');
        $property = new \ReflectionProperty($this->command, 'ended');

        $method->setAccessible(true);
        $property->setAccessible(true);

        $this->assertEmpty($property->getValue($this->command));
        $method->invokeArgs($this->command, []);
        $this->assertNotEmpty($property->getValue($this->command));
    }

    /**
     * Tests getDuration.
     */
    public function testGetDuration()
    {
        $method = new \ReflectionMethod($this->command, 'getDuration');
        $method->setAccessible(true);

        $this->assertEquals('00:00:00', $method->invokeArgs($this->command, []));
    }

    /**
     * Tests getResizeParameters when invalid resolution provided
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetResizeParametersWhenInvalidResolution()
    {
        $method = new \ReflectionMethod($this->command, 'getResizeParameters');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, [ 'gorp' ]);
    }

    /**
     * Tests getResizeParameters when valid resolution provided
     */
    public function testGetResizeParametersWhenValidResolution()
    {
        $method = new \ReflectionMethod($this->command, 'getResizeParameters');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 9236, 27049 ],
            $method->invokeArgs($this->command, [ '9236x27049' ])
        );
    }

    /**
     * Tests start.
     */
    public function testStart()
    {
        $method   = new \ReflectionMethod($this->command, 'start');
        $property = new \ReflectionProperty($this->command, 'started');

        $method->setAccessible(true);
        $property->setAccessible(true);

        $this->assertEmpty($property->getValue($this->command));
        $method->invokeArgs($this->command, []);
        $this->assertNotEmpty($property->getValue($this->command));
    }
}
