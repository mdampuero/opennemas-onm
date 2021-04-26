<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Command;

use Common\Migration\Command\CleanCommand;
use Common\Model\Entity\Instance;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Defines test cases for MigratCommand class.
 */
class CleanCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->conn = $this->getMockBuilder('DatabaseConnection')
            ->setMethods([ 'fetchAll', 'selectDatabase' ])
            ->getMock();

        $this->ir = $this->getMockBuilder('Common\Model\Database\Repository\InstanceRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'findBy' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConnection', 'getRepository' ])
            ->getMock();

        $this->em->expects($this->any())->method('getConnection')
            ->willReturn($this->conn);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->setMethods([ 'date', 'files', 'in', 'name' ])
            ->getMock();

        $this->fs = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->setMethods([
                'chgrp', 'dumpFile', 'exists', 'mkdir', 'remove', 'touch'
            ])->getMock();

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
                [ 'isVerbose' ]
            );

        $this->command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([
                'do',
                'getFinder',
                'getAttachments',
                'getParameters',
                'getPhotos',
                'getKiosko'
                ])
            ->getMock();

        $this->command->expects($this->any())->method('getFinder')
            ->willReturn($this->finder);

        $property = new \ReflectionProperty($this->command, 'fs');
        $property->setAccessible(true);
        $property->setValue($this->command, $this->fs);

        $this->command->setContainer($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;

            case 'orm.manager':
                return $this->em;
        }
    }

    /**
     * Tests do for no path.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testDoWhenNoPath()
    {
        $command = new CleanCommand();

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'     => $command->getName(),
            '--database'  => '1',
            '--instance'  => 'opennemas'
        ]);
    }

    /**
     * Tests do when skip
     */
    public function testDoWhenSkip()
    {
        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getFinder', 'getFileSystem', 'getParameters', 'getPhotos', 'getKiosko', 'getAttachments' ])
            ->getMock();

        $method = new \ReflectionMethod($command, 'execute');
        $method->setAccessible(true);

        $command->expects($this->any())->method('getParameters')
            ->with($this->input)
            ->willReturn([ 1, '/home/opennemas/current/public/media/opennemas/' ]);

        $command->expects($this->any())->method('getPhotos')
            ->with('1')
            ->willReturn([ 'images/2021/04/16/photo.jpg' ]);
        $command->expects($this->any())->method('getKiosko')
            ->with('1')
            ->willReturn([ 'kiosko/2018/10/01/20181001132608.jpg' ]);
        $command->expects($this->any())->method('getAttachments')
            ->with('1')
            ->willReturn([ 'files/file.pdf' ]);

        $command->expects($this->any())->method('getFinder')
            ->willReturn($this->finder);

        $command->expects($this->any())->method('getFileSystem')
            ->willReturn($this->fs);

        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPathName' ])
            ->getMock();

        $file->expects($this->any())->method('getPathName')
            ->willReturn('images/2021/04/16/photo.jpg');

        $this->finder->expects($this->any())->method('files')
            ->willReturn($this->finder);

        $this->finder->expects($this->any())->method('in')
            ->with('/home/opennemas/current/public/media/opennemas/')
            ->willReturn([ $file ]);

        $this->fs->expects($this->any())->method('remove')
            ->willThrowException(new \Exception());

        $this->assertEmpty(
            $method->invokeArgs($command, [ $this->input, $this->output ])
        );
    }

    /**
     * Tests do when skip & verbose
     */
    public function testDoWhenSkipVerbose()
    {
        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getFinder', 'getFileSystem', 'getParameters', 'getPhotos', 'getKiosko', 'getAttachments' ])
            ->getMock();

        $method = new \ReflectionMethod($command, 'execute');
        $method->setAccessible(true);

        $command->expects($this->any())->method('getParameters')
            ->with($this->input)
            ->willReturn([ 1, '/home/opennemas/current/public/media/opennemas/' ]);

        $command->expects($this->any())->method('getPhotos')
            ->with('1')
            ->willReturn([ 'images/2021/04/16/photo.jpg' ]);
        $command->expects($this->any())->method('getKiosko')
            ->with('1')
            ->willReturn([ 'kiosko/2018/10/01/20181001132608.jpg' ]);
        $command->expects($this->any())->method('getAttachments')
            ->with('1')
            ->willReturn([ 'files/file.pdf' ]);

        $command->expects($this->any())->method('getFinder')
            ->willReturn($this->finder);

        $command->expects($this->any())->method('getFileSystem')
            ->willReturn($this->fs);

        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPathName' ])
            ->getMock();

        $file->expects($this->any())->method('getPathName')
            ->willReturn('images/2021/04/16/photo.jpg');

        $this->finder->expects($this->any())->method('files')
            ->willReturn($this->finder);

        $this->finder->expects($this->any())->method('in')
            ->with('/home/opennemas/current/public/media/opennemas/')
            ->willReturn([ $file ]);

        $this->output->expects($this->any())->method('isVerbose')
            ->willReturn(true);

        $this->fs->expects($this->any())->method('remove')
            ->willThrowException(new \Exception());

        $this->assertEmpty(
            $method->invokeArgs($command, [ $this->input, $this->output ])
        );
    }

    /**
     * Tests do when done
     */
    public function testDoWhenDone()
    {
        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getFinder', 'getFileSystem', 'getParameters', 'getPhotos', 'getKiosko', 'getAttachments' ])
            ->getMock();

        $method = new \ReflectionMethod($command, 'execute');
        $method->setAccessible(true);

        $command->expects($this->any())->method('getParameters')
            ->with($this->input)
            ->willReturn([ 1, '/home/opennemas/current/public/media/opennemas/' ]);

        $command->expects($this->any())->method('getPhotos')
            ->with('1')
            ->willReturn([ 'images/2021/04/16/photo.jpg' ]);
        $command->expects($this->any())->method('getKiosko')
            ->with('1')
            ->willReturn([ 'kiosko/2018/10/01/20181001132608.jpg' ]);
        $command->expects($this->any())->method('getAttachments')
            ->with('1')
            ->willReturn([ 'files/file.pdf' ]);

        $command->expects($this->any())->method('getFinder')
            ->willReturn($this->finder);

        $command->expects($this->any())->method('getFileSystem')
            ->willReturn($this->fs);

        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPathName' ])
            ->getMock();

        $file->expects($this->any())->method('getPathName')
            ->willReturn('images/2021/04/17/photo.jpg');

        $this->finder->expects($this->any())->method('files')
            ->willReturn($this->finder);

        $this->finder->expects($this->any())->method('in')
            ->with('/home/opennemas/current/public/media/opennemas/')
            ->willReturn([ $file ]);

        $this->output->expects($this->any())->method('isVerbose')
            ->willReturn(true);

        $this->assertEmpty(
            $method->invokeArgs($command, [ $this->input, $this->output ])
        );
    }

    /**
     * Tests do when fail
     */
    public function testDoWhenFail()
    {
        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getFinder', 'getFileSystem', 'getParameters', 'getPhotos', 'getKiosko', 'getAttachments' ])
            ->getMock();

        $method = new \ReflectionMethod($command, 'execute');
        $method->setAccessible(true);

        $command->expects($this->any())->method('getParameters')
            ->with($this->input)
            ->willReturn([ 1, '/home/opennemas/current/public/media/opennemas/' ]);

        $command->expects($this->any())->method('getPhotos')
            ->with('1')
            ->willReturn([ 'images/2021/04/16/photo.jpg' ]);
        $command->expects($this->any())->method('getKiosko')
            ->with('1')
            ->willReturn([ 'kiosko/2018/10/01/20181001132608.jpg' ]);
        $command->expects($this->any())->method('getAttachments')
            ->with('1')
            ->willReturn([ 'files/file.pdf' ]);

        $command->expects($this->any())->method('getFinder')
            ->willReturn($this->finder);

        $command->expects($this->any())->method('getFileSystem')
            ->willReturn($this->fs);

        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPathName' ])
            ->getMock();

        $file->expects($this->any())->method('getPathName')
            ->willReturn('images/2021/04/17/photo.jpg');

        $this->finder->expects($this->any())->method('files')
            ->willReturn($this->finder);

        $this->finder->expects($this->any())->method('in')
            ->with('/home/opennemas/current/public/media/opennemas/')
            ->willReturn([ $file ]);

        $this->output->expects($this->any())->method('isVerbose')
            ->willReturn(true);

        $this->fs->expects($this->any())->method('remove')
            ->willThrowException(new \Exception());

        $this->assertEmpty(
            $method->invokeArgs($command, [ $this->input, $this->output ])
        );
    }

    /**
     * Tests getFinder.
     */
    public function testGetFinder()
    {
        $command = new CleanCommand();

        $method = new \ReflectionMethod($command, 'getFinder');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            '\Symfony\Component\Finder\Finder',
            $method->invokeArgs($command, [])
        );
    }

    /**
     * Tests getFileSystem.
     */
    public function testGetFileSystem()
    {
        $command = new CleanCommand();

        $method = new \ReflectionMethod($command, 'getFileSystem');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            '\Symfony\Component\Filesystem\Filesystem',
            $method->invokeArgs($command, [])
        );
    }

    /**
     * Tests getParameters when invalid arguments.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetParametersWhenInvalidArguments()
    {
        $database = 1;
        $instance = 'openenemas';

        $this->input->expects($this->at(0))->method('getOption')
            ->with('database')
            ->willReturn($database);
        $this->input->expects($this->at(1))->method('getOption')
            ->with('instance')
            ->willReturn($instance);
        $this->input->expects($this->at(2))->method('getOption')
            ->with('path')
            ->willReturn(null);

        $command = new CleanCommand();

        $method = new \ReflectionMethod($command, 'getParameters');
        $method->setAccessible(true);

        $method->invokeArgs($command, [ $this->input ]);
    }

    /**
     * Tests getParameters when all options.
     */
    public function testGetParametersWhenAllOptions()
    {
        $database = 1;
        $instance = 'openenemas';
        $path     = '/home/opennemas/current/public/media/openenmas/';

        $this->input->expects($this->at(0))->method('getOption')
            ->with('database')
            ->willReturn($database);
        $this->input->expects($this->at(1))->method('getOption')
            ->with('instance')
            ->willReturn($instance);
        $this->input->expects($this->at(2))->method('getOption')
            ->with('path')
            ->willReturn($path);

        $command = new CleanCommand();

        $method = new \ReflectionMethod($command, 'getParameters');
        $method->setAccessible(true);

        $method->invokeArgs($command, [ $this->input ]);
    }

    /**
     * Tests getParameters when no database option.
     */
    public function testGetParametersWhenNoDatabaseOption()
    {
        $instance     = new Instance([ 'settings' => [ 'BD_DATABASE' => 1 ] ]);
        $instanceName = 'openenemas';
        $path         = '/home/opennemas/current/public/media/openenmas/';

        $this->input->expects($this->at(1))->method('getOption')
            ->with('instance')
            ->willReturn($instanceName);
        $this->input->expects($this->at(2))->method('getOption')
            ->with('path')
            ->willReturn($path);

        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $command->expects($this->once())->method('getContainer')
            ->willReturn($this->container);

        $this->em->expects($this->once())->method('getRepository')
            ->with('Instance')
            ->willReturn($this->ir);

        $oql = sprintf('internal_name = "%s"', $instanceName);
        $this->ir->expects($this->once())->method('findBy')
            ->with($oql)
            ->willReturn([ $instance ]);

        $method = new \ReflectionMethod($command, 'getParameters');
        $method->setAccessible(true);

        $this->assertEquals([ 1, $path], $method->invokeArgs($command, [ $this->input ]));
    }

    /**
     * Tests getAttachments when no return data.
     */
    public function testGetAttachmentsWhenNoReturnData()
    {
        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $command->expects($this->once())->method('getContainer')
            ->willReturn($this->container);

        $this->conn->expects($this->once())->method('fetchAll')
            ->willReturn([]);

        $method = new \ReflectionMethod($command, 'getAttachments');
        $method->setAccessible(true);

        $this->assertEquals(
            [ ],
            $method->invokeArgs($command, [ 'opennemas' ])
        );
    }

    /**
     * Tests getAttachments when return data.
     */
    public function testGetAttachmentsWhenReturnData()
    {
        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $command->expects($this->once())->method('getContainer')
            ->willReturn($this->container);

        $this->conn->expects($this->once())->method('fetchAll')
            ->willReturn([ [ 'path' => '/file.pdf' ] ]);

        $method = new \ReflectionMethod($command, 'getAttachments');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 'files/file.pdf' ],
            $method->invokeArgs($command, [ 'opennemas' ])
        );
    }

    /**
     * Tests getPhotos when no return data.
     */
    public function testGetPhotosWhenNoReturnData()
    {
        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $command->expects($this->once())->method('getContainer')
            ->willReturn($this->container);

        $this->conn->expects($this->once())->method('fetchAll')
            ->willReturn([]);

        $method = new \ReflectionMethod($command, 'getPhotos');
        $method->setAccessible(true);

        $this->assertEquals(
            [ ],
            $method->invokeArgs($command, [ 'opennemas' ])
        );
    }

    /**
     * Tests getPhotos when return data.
     */
    public function testGetPhotosWhenReturnData()
    {
        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $command->expects($this->once())->method('getContainer')
            ->willReturn($this->container);

        $this->conn->expects($this->once())->method('fetchAll')
            ->willReturn([ [ 'meta_value' => 'images/2021/04/16/photo.jpg' ] ]);

        $method = new \ReflectionMethod($command, 'getPhotos');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 'images/2021/04/16/photo.jpg' ],
            $method->invokeArgs($command, [ 'opennemas' ])
        );
    }

    /**
     * Tests getKiosko when no return data.
     */
    public function testGetKioskoWhenNoReturnData()
    {
        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $command->expects($this->once())->method('getContainer')
            ->willReturn($this->container);

        $this->conn->expects($this->once())->method('fetchAll')
            ->willReturn([]);

        $method = new \ReflectionMethod($command, 'getKiosko');
        $method->setAccessible(true);

        $this->assertEquals(
            [ ],
            $method->invokeArgs($command, [ 'opennemas' ])
        );
    }

    /**
     * Tests getKiosko when return data.
     */
    public function testGetKioskoWhenReturnData()
    {
        $command = $this->getMockBuilder('Common\Migration\Command\CleanCommand')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $command->expects($this->once())->method('getContainer')
            ->willReturn($this->container);

        $this->conn->expects($this->once())->method('fetchAll')
            ->willReturn([ [ 'meta_value' => '2018/10/01/20181001132608.jpg' ] ]);

        $method = new \ReflectionMethod($command, 'getKiosko');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 'kiosko/2018/10/01/20181001132608.jpg' ],
            $method->invokeArgs($command, [ 'opennemas' ])
        );
    }
}
