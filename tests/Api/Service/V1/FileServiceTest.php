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

use Api\Service\V1\FileService;
use Common\Core\Component\Helper\FileHelper;
use Common\ORM\Entity\Instance;

/**
 * Defines test cases for FileService class.
 */
class FileServiceTest extends \PHPUnit\Framework\TestCase
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

        $this->fh = $this->getMockBuilder('Common\Core\Component\Helper\FileHelper')
            ->setConstructorArgs([ $this->instance, '/wibble/flob' ])
            ->setMethods([ 'generatePath', 'generateRelativePath', 'move', 'remove' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $this->service = $this->getMockBuilder('Api\Service\V1\FileService')
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

            case 'core.helper.file':
                return $this->fh;

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

        $file->expects($this->any())->method('getClientOriginalName')
            ->willReturn('xyzzy.glorp');

        $this->fh->expects($this->once())->method('move')
            ->will($this->throwException(new \Exception()));

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

        $this->fh->expects($this->once())->method('generatePath')
            ->will($this->throwException(new \Exception()));

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
