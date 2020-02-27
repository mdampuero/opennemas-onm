<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Api\Service\V1;

use Api\Service\V1\AttachmentService;
use Common\Core\Component\Helper\AttachmentHelper;
use Common\ORM\Entity\Instance;

use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AttachmentServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'flob' ]);

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('Common\Core\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityRepository')
            ->setMethods([ 'find' ])
            ->getMock();

        $this->il = $this->getMockBuilder('Common\Core\Component\Loader\InstanceLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'getInstance' ])
            ->getMock();

        $this->ah = $this->getMockBuilder('Common\Core\Component\Helper\AttachmentHelper')
            ->setConstructorArgs([ $this->il, '/wibble/flob' ])
            ->setMethods([ 'generatePath', 'getRelativePath', 'move', 'remove', 'exists' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $this->il->expects($this->any())->method('getInstance')
            ->willReturn($this->instance);

        $this->service = $this->getMockBuilder('Api\Service\V1\AttachmentService')
            ->setConstructorArgs([ $this->container, '\Attachment' ])
            ->setMethods([ 'getItem' ])
            ->getMock();
    }

    /**
     * Returns a mocked service basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'entity_repository':
                return $this->em;

            case 'core.helper.attachment':
                return $this->ah;

            default:
                return null;
        }
    }

    /**
     * Tests createItem when no file provided
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItemWhenNoFile()
    {
        $this->service->createItem([ 'title' => 'waldo' ]);
    }

    /**
     * Tests createItem when an error while moving the file is thrown.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItemWhenErrorWithFile()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $externalAttachment = m::mock('overload:\Attachment');

        $this->ah->expects($this->once())->method('move');

        $externalAttachment->shouldReceive('create')->once()->andReturn(null);

        $this->service->createItem([ 'title' => 'waldo' ], $file);
    }


    /**
     * Tests createItem when file already exists.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItemWhenFileAlreadyExists()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $this->ah->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(true);

        $this->service->createItem([ 'title' => 'waldo' ], $file);
    }


    /**
     * Tests createItem when successful upload.
     */
    public function testCreateItemWhenSuccessfulUpload()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $externalAttachment = m::mock('overload:\Attachment');

        $this->ah->expects($this->once())->method('move');

        $externalAttachment->shouldReceive('create')->once()->andReturn($externalAttachment);

        $this->service->createItem([ 'title' => 'waldo' ], $file);
    }

    /**
     * Tests updateItem when an error while moving the file is thrown.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenErrorWithFile()
    {
        $item = new \Attachment();

        $item->created = '2010-01-01 00:00:00';
        $item->path    = '/2010/01/01/plugh.mumble';

        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $this->service->expects($this->once())->method('getItem')
            ->willReturn($item);

        $this->ah->expects($this->once())->method('generatePath')
            ->will($this->throwException(new \Exception()));

        $this->service->updateItem(1, [ 'title' => 'waldo' ], $file);
    }

    /**
     * Tests updateItem when file already exists.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenFileAlreadyExists()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $attachment = $this->getMockBuilder('\Attachment')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRelativePath' ])
            ->getMock();

        $this->ah->expects($this->once())->method('getRelativePath')
            ->willReturn('AttachmentHelper');

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(true);

        $this->service->expects($this->once())->method('getItem')
            ->with(1)
            ->willReturn($attachment);

        $attachment->expects($this->once())->method('getRelativePath')
            ->willReturn('Attachment');

        $this->service->updateItem(1, [ 'title' => 'waldo' ], $file);
    }


    /**
     * Tests updateItem when not empty relative path.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenNotEmptyRelativePath()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $attachment = $this->getMockBuilder('\Attachment')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRelativePath', 'update' ])
            ->getMock();

        $this->service->expects($this->once())->method('getItem')
            ->with(1)
            ->willReturn($attachment);

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(false);

        $attachment->expects($this->any())->method('getRelativePath')
            ->willReturn('Attachment');

        $this->ah->expects($this->once())
            ->method('remove');

        $this->ah->expects($this->once())
            ->method('move');

        $attachment->expects($this->once())->method('update');

        $this->service->updateItem(1, [ 'title' => 'waldo' ], $file);
    }


    /**
     * Tests updateItem when successful update.
     */
    public function testUpdateItemWhenSuccessfulUpdate()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $attachment = $this->getMockBuilder('\Attachment')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRelativePath', 'update' ])
            ->getMock();

        $this->service->expects($this->once())->method('getItem')
            ->with(1)
            ->willReturn($attachment);

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(false);

        $attachment->expects($this->any())->method('getRelativePath')
            ->willReturn('Attachment');

        $this->ah->expects($this->once())
            ->method('remove');

        $this->ah->expects($this->once())
            ->method('move');

        $attachment->expects($this->once())->method('update')
            ->willReturn('something');

        $this->service->updateItem(1, [ 'title' => 'waldo' ], $file);
    }

    /**
     * Tests updateItem when no file nor existing path provided.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenNoFileNorPath()
    {
        $this->service->updateItem(1, [ 'title' => 'waldo' ]);
    }
}
