<?php

namespace Test\Common\Core\Command;

use Common\Core\Command\WebCrawlingCommand;
use Common\Model\Entity\Instance;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Tests the crawling:execute command.
 */
class WebCrawlingCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->command = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([
                'configureSpider',
                'estimateInstancesByMaxTime',
                'filterInstancesByParameters',
                'filterInstancesByTime',
                'getInstances',
                'getNotEmptyParameter',
                'getParameters',
                'randomizeInstance'
                ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->input = $this
            ->getMockForAbstractClass(
                'Symfony\Component\Console\Input\Input',
                [],
                '',
                true,
                true,
                true,
                [ 'getOption' ]
            );

        $this->output = $this
            ->getMockForAbstractClass(
                'Symfony\Component\Console\Output\Output',
                [],
                '',
                true,
                true,
                true,
                [ 'isDebug' ]
            );

        $this->ir = $this->getMockBuilder('Common\Model\Database\Repository\InstanceRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'findBy' ])
            ->getMock();

        $this->spider = $this->getMockBuilder('VDB\Spider\Spider')
            ->disableOriginalConstructor()
            ->setMethods([ 'crawl' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->command->expects($this->any())->method('configureSpider')
            ->willReturn($this->spider);

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->ir);

        $this->command->setContainer($this->container);
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
        }

        return null;
    }

    /**
     * Tests configureSpider when not in debug mode.
     */
    public function testConfigureSpiderNotDebug()
    {
        $instance = new Instance([
            'activated_modules' => [ 'es.openhost.module.frontendSsl' ],
            'domains'           => [ 'testinstance.dom' ],
            ]);

        $parameters = [
            'port'  => 8080,
            'depth' => 2,
            'limit' => 1000,
            'time'  => 1000
        ];

        $command = new WebCrawlingCommand();

        $method = new \ReflectionMethod($command, 'configureSpider');
        $method->setAccessible(true);

        $output = new \ReflectionProperty($command, 'output');
        $output->setAccessible(true);
        $output->setValue($command, $this->output);

        $this->output->expects($this->at(0))->method('isDebug')
            ->willReturn(false);

        $this->output->expects($this->at(1))->method('isDebug')
            ->willReturn(true);

        $this->assertIsObject($method->invokeArgs($command, [ $parameters, $instance ]));
        $this->assertIsObject($method->invokeArgs($command, [ $parameters, $instance ]));
    }

    /**
     * Tests the execution of the test when all the parameters are completed.
     */
    public function testExecuteWhenArgumentsComplete()
    {
        $instance    = new Instance(['internal_name' => 'testinstance']);
        $application = new Application();
        $application->add($this->command);

        $this->command->expects($this->once())->method('getParameters')
            ->willReturn(
                [
                    'depth'     => 3,
                    'limit'     => 1000,
                    'instances' => 'testinstance',
                    'port'      => 8080,
                    'time'      => 3000,
                    'random'    => false
                ]
            );

        $this->command->expects($this->once())->method('filterInstancesByParameters')
            ->willReturn([ $instance ]);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute(
            [
                'command'     => $this->command->getName(),
                '--depth'     => 3,
                '--limit'     => 1000,
                '--instances' => 'testinstance',
                '--port'      => 8080,
                '--time'      => 3000,
                '--random'    => false
            ]
        );
    }

    /**
     * Tests the command when mode is verbose.
     */
    public function testExecuteWhenVerbose()
    {
        $instance    = new Instance(['internal_name' => 'testinstance']);
        $application = new Application();
        $application->add($this->command);

        $this->command->expects($this->once())->method('filterInstancesByParameters')
            ->willReturn([ $instance ]);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute(
            [
                'command'     => $this->command->getName(),
                '--depth'     => 3,
                '--limit'     => 1000,
                '--instances' => 'testinstance',
                '--port'      => 8080,
                '--time'      => 3000,
                '--random'    => false
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE
            ]
        );
    }

    /**
     * Tests the command when mode is veryVerbose.
     */
    public function testExecuteWhenVeryVerbose()
    {
        $instance    = new Instance(['internal_name' => 'testinstance']);
        $application = new Application();
        $application->add($this->command);

        $this->command->expects($this->once())->method('filterInstancesByParameters')
            ->willReturn([ $instance ]);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute(
            [
                'command'     => $this->command->getName(),
                '--depth'     => 3,
                '--limit'     => 1000,
                '--instances' => 'testinstance',
                '--port'      => 8080,
                '--time'      => 3000,
                '--random'    => false
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE
            ]
        );
    }

    /**
     * Tests the method estimateInstancesByMaxTime.
     */
    public function testEstimateInstancesByMaxTime()
    {
        $command = new WebCrawlingCommand();
        $method  = new \ReflectionMethod($command, 'estimateInstancesByMaxTime');
        $method->setAccessible(true);

        $this->assertEquals(
            floor(2 * 3600 / ((1000 / 1000) * 1000)),
            $method->invokeArgs(
                $command,
                [ 2, 1000, 1000 ]
            )
        );
    }

    /**
     * Tests method filterInstancesByParameter when random is true.
     */
    public function testFilterInstancesByParametersWhenRandom()
    {
        $instance = new Instance(['internal_name' => 'testinstance']);
        $command  = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([ 'randomizeInstance' ])
            ->getMock();

        $method = new \ReflectionMethod($command, 'filterInstancesByParameters');
        $method->setAccessible(true);

        $command->expects($this->at(0))->method('randomizeInstance')
            ->willReturn($instance);

        $this->assertEquals(
            [ $instance ],
            $method->invokeArgs(
                $command,
                [ [ 'random' => true, 'instances' => [ $instance ] ] ]
            )
        );
    }

    /**
     * Tests method filterInstancesByParameter when random is false and maxtime is not 0.
     */
    public function testFilterInstancesByParametersWhenNoRandomAndMaxTime()
    {
        $instance = new Instance(['internal_name' => 'testinstance']);
        $command  = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([ 'filterInstancesByTime' ])
            ->getMock();

        $method = new \ReflectionMethod($command, 'filterInstancesByParameters');
        $method->setAccessible(true);

        $command->expects($this->at(0))->method('filterInstancesByTime')
            ->willReturn([ $instance ]);

        $this->assertEquals(
            [ $instance ],
            $method->invokeArgs(
                $command,
                [ [ 'random' => false, 'instances' => [ $instance ], 'maxtime' => 2 ] ]
            )
        );
    }

    /**
     * Tests method filterInstancesByParameter when no parameters.
     */
    public function testFilterInstancesByParametersWhenNoParameters()
    {
        $instance = new Instance(['internal_name' => 'testinstance']);
        $command  = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([ 'filterInstancesByTime' ])
            ->getMock();

        $method = new \ReflectionMethod($command, 'filterInstancesByParameters');
        $method->setAccessible(true);

        $this->assertEquals(
            [ $instance ],
            $method->invokeArgs(
                $command,
                [ [ 'random' => false, 'instances' => [ $instance ], 'maxtime' => 0 ] ]
            )
        );
    }

    /**
     * Tests method filterInstancesByTime when more time than instances.
     */
    public function testFilterInstancesByTimeWhenMoreTimeThanInstances()
    {
        $parameters = [
            'maxtime' => 2,
            'time'    => 1000,
            'limit'   => 1000
        ];

        $firstInstance  = new Instance([ 'internal_name' => 'firstinstance' ]);
        $secondInstance = new Instance([ 'internal_name' => 'secondinstance' ]);

        $parameters['instances'] = [ $firstInstance, $secondInstance ];

        $command = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([ 'estimateInstancesByMaxTime' ])
            ->getMock();

        $command->expects($this->once())->method('estimateInstancesByMaxTime')
            ->with($parameters['maxtime'], $parameters['time'], $parameters['limit'])
            ->willReturn(7);

        $method = new \ReflectionMethod($command, 'filterInstancesByTime');
        $method->setAccessible(true);

        $this->assertEquals($parameters['instances'], $method->invokeArgs($command, [ $parameters ]));
    }

    /**
     * Tests method filterInstancesByTime when more instances than time.
     */
    public function testFilterInstancesByTimeWhenMoreInstancesThanTime()
    {
        $parameters = [
            'maxtime' => 1,
            'time'    => 500,
            'limit'   => 3000
        ];

        $time = floor($parameters['maxtime'] * 3600 / (($parameters['time'] / 1000) * $parameters['limit']));

        $firstInstance  = new Instance([ 'internal_name' => 'firstinstance', 'domains'  => [ 'firstinstance.com' ] ]);
        $secondInstance = new Instance([ 'internal_name' => 'secondinstance', 'domains' => [ 'secondinstance.com' ] ]);
        $thirdInstance  = new Instance([ 'internal_name' => 'thirdinstance', 'domains'  => [ 'thirdinstance.com' ] ]);

        $parameters['instances'] = [ $firstInstance, $secondInstance, $thirdInstance ];

        $command = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([ 'estimateInstancesByMaxTime', 'randomizeInstance' ])
            ->getMock();

        $command->expects($this->once())->method('estimateInstancesByMaxTime')
            ->with(
                $parameters['maxtime'],
                $parameters['time'],
                $parameters['limit']
            )
            ->willReturn($time);

        $command->expects($this->at(1))->method('randomizeInstance')
            ->with($parameters['instances'])
            ->willReturn($secondInstance);

        $command->expects($this->at(2))->method('randomizeInstance')
            ->with($parameters['instances'])
            ->willReturn($secondInstance);

        $command->expects($this->at(3))->method('randomizeInstance')
            ->with($parameters['instances'])
            ->willReturn($thirdInstance);

        $method = new \ReflectionMethod($command, 'filterInstancesByTime');
        $method->setAccessible(true);

        $this->assertEquals([ $secondInstance, $thirdInstance ], $method->invokeArgs($command, [ $parameters ]));
    }

    /**
     * Tests method getInstances when empty names.
     */
    public function testGetInstancesWhenEmptyNames()
    {
        $instance = new Instance(['internal_name' => 'testinstance']);
        $command  = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $method = new \ReflectionMethod($command, 'getInstances');
        $method->setAccessible(true);

        $command->expects($this->once())->method('getContainer')
            ->willReturn($this->container);

        $this->ir->expects($this->once())->method('findBy')
            ->with('activated = 1')
            ->willReturn([ $instance ]);

        $this->assertEquals([ $instance ], $method->invokeArgs($command, []));
    }

    /**
     * Tests method getInstances when not empty names.
     */
    public function testGetInstancesWhenNotEmptyNames()
    {
        $instance = new Instance(['internal_name' => 'testinstance']);
        $command  = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $oql  = 'activated = 1';
        $oql .= sprintf(' and internal_name in ["%s"]', implode('","', [ $instance->internal_name ]));

        $method = new \ReflectionMethod($command, 'getInstances');
        $method->setAccessible(true);

        $command->expects($this->once())->method('getContainer')
            ->willReturn($this->container);

        $this->ir->expects($this->once())->method('findBy')
            ->with($oql)
            ->willReturn([ $instance ]);

        $this->assertEquals([ $instance ], $method->invokeArgs($command, [ [ $instance->internal_name ] ]));
    }

    /**
     * Tests method getNotEmptyParameter.
     */
    public function testGetNotEmptyParameter()
    {
        $depth = 3;
        $time  = 1000;

        $command = new WebCrawlingCommand();
        $method  = new \ReflectionMethod($command, 'getNotEmptyParameter');
        $method->setAccessible(true);

        $this->input->expects($this->at(0))->method('getOption')
            ->with('depth')
            ->willReturn($depth);


        $this->input->expects($this->at(1))->method('getOption')
            ->with('depth')
            ->willReturn($depth);

        $this->input->expects($this->at(2))->method('getOption')
            ->with('time')
            ->willReturn($time);

        $this->input->expects($this->at(3))->method('getOption')
            ->with('time')
            ->willReturn($time);

        $this->input->expects($this->at(4))->method('getOption')
            ->with('limit')
            ->willReturn(null);

        $this->assertEquals($depth, $method->invokeArgs($command, [ $this->input, 'depth' ]));
        $this->assertEquals($time, $method->invokeArgs($command, [ $this->input, 'time' ]));
        $this->assertEquals(3000, $method->invokeArgs($command, [ $this->input, 'limit' ]));
    }

    /**
     * Tests method getParameters when not empty instances.
     */
    public function testGetParametersWhenNotEmptyInstances()
    {
        $firstTestInstance  = new Instance([ 'internal_name' => 'firsttestinstance' ]);
        $secondTestInstance = new Instance([ 'internal_name' => 'secondtestinstance' ]);

        $command = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([ 'getNotEmptyParameter', 'getInstances' ])
            ->getMock();

        $command->expects($this->at(0))->method('getNotEmptyParameter')
            ->with($this->input, 'depth')
            ->willReturn(3);
        $command->expects($this->at(1))->method('getNotEmptyParameter')
            ->with($this->input, 'limit')
            ->willReturn(1000);
        $command->expects($this->at(2))->method('getNotEmptyParameter')
            ->with($this->input, 'maxtime')
            ->willReturn(2);
        $command->expects($this->at(3))->method('getNotEmptyParameter')
            ->with($this->input, 'instances')
            ->willReturn('firsttestinstance,secondtestinstance');
        $command->expects($this->at(4))->method('getNotEmptyParameter')
            ->with($this->input, 'port')
            ->willReturn(8080);
        $command->expects($this->at(5))->method('getNotEmptyParameter')
            ->with($this->input, 'time')
            ->willReturn(1000);
        $command->expects($this->at(6))->method('getNotEmptyParameter')
            ->with($this->input, 'random')
            ->willReturn(false);

        $command->expects($this->once())->method('getInstances')
            ->with(preg_split('/\s*,\s*/', 'firsttestinstance,secondtestinstance'))
            ->willReturn([ $firstTestInstance, $secondTestInstance ]);

        $method = new \ReflectionMethod($command, 'getParameters');
        $method->setAccessible(true);

        $this->assertEquals(
            [
                'depth'     => 3,
                'limit'     => 1000,
                'maxtime'   => 2,
                'instances' => [ $firstTestInstance, $secondTestInstance ],
                'port'      => 8080,
                'time'      => 1000,
                'random'    => false
            ],
            $method->invokeArgs($command, [ $this->input ])
        );
    }

    /**
     * Tests method getParameters when empty instances.
     */
    public function testGetParametersWhenEmptyInstances()
    {
        $command = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([ 'getNotEmptyParameter', 'getInstances' ])
            ->getMock();

        $command->expects($this->at(0))->method('getNotEmptyParameter')
            ->with($this->input, 'depth');
        $command->expects($this->at(1))->method('getNotEmptyParameter')
            ->with($this->input, 'limit');
        $command->expects($this->at(2))->method('getNotEmptyParameter')
            ->with($this->input, 'maxtime');
        $command->expects($this->at(3))->method('getNotEmptyParameter')
            ->with($this->input, 'instances')
            ->willReturn(null);
        $command->expects($this->at(4))->method('getNotEmptyParameter')
            ->with($this->input, 'port');
        $command->expects($this->at(5))->method('getNotEmptyParameter')
            ->with($this->input, 'time');
        $command->expects($this->at(6))->method('getNotEmptyParameter')
            ->with($this->input, 'random');

        $command->expects($this->once())->method('getInstances')
            ->with(null);

        $method = new \ReflectionMethod($command, 'getParameters');
        $method->setAccessible(true);

        $method->invokeArgs($command, [ $this->input ]);
    }

    /**
     * Tests method randomizeInstance.
     */
    public function testRandomizeInstance()
    {
        $instance = new Instance(['internal_name' => 'testinstance']);
        $command  = $this->getMockBuilder('Common\Core\Command\WebCrawlingCommand')
            ->setMethods([ 'filterInstancesByTime' ])
            ->getMock();

        $method = new \ReflectionMethod($command, 'randomizeInstance');
        $method->setAccessible(true);

        $this->assertEquals($instance, $method->invokeArgs($command, [ [ $instance ] ]));
    }
}
