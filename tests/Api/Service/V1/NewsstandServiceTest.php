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

use Api\Service\V1\NewsstandService;
use Common\Core\Component\Helper\NewsstandHelper;
use Common\ORM\Entity\Instance;

/**
 * Defines test cases for NewsstandService class.
 */
class NewsstandServiceTest extends \PHPUnit\Framework\TestCase
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

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->nh = $this->getMockBuilder('Common\Core\Component\Helper\NewsstandHelper')
            ->setConstructorArgs([ $this->instance, '/wibble/flob' ])
            ->setMethods([ 'exists', 'generatePath', 'getRelativePath', 'move', 'remove' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $this->service = $this->getMockBuilder('Api\Service\V1\NewsstandService')
            ->setConstructorArgs([ $this->container, '\Kiosko' ])
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
            case 'dbal_connection':
                return $this->conn;

            case 'core.dispatcher':
                return $this->dispatcher;

            case 'entity_repository':
                return $this->em;

            case 'core.helper.newsstand':
                return $this->nh;

            default:
                return null;
        }
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
            ->getMock();

        $this->nh->expects($this->once())->method('move')
            ->will($this->throwException(new \Exception()));

        $this->service->createItem([ 'title' => 'waldo' ], $file);
    }

    /**
     * Tests createItem when the provided file already exists.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItemWhenFileExists()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->getMock();

        $this->nh->expects($this->once())->method('exists')
            ->willReturn(true);

        $this->service->createItem([ 'title' => 'waldo' ], $file);
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
     * Tests updateItem when an error while moving the file is thrown.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenErrorWithFile()
    {
        $item = new \Kiosko();

        $item->created = '2010-01-01 00:00:00';
        $item->path    = '/2010/01/01/plugh.mumble';

        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $this->service->expects($this->once())->method('getItem')
            ->willReturn($item);

        $this->nh->expects($this->once())->method('generatePath')
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
