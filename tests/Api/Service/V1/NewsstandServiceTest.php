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
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;

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

        $this->converter = $this->getMockBuilder('Converter' . uniqid())
            ->setMethods([ 'objectify', 'responsify' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('Common\Core\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'find', 'getConverter', 'getMetadata' ])
            ->getMock();

        $this->il = $this->getMockBuilder('Common\Core\Component\Loader\InstanceLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'getInstance' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->nh = $this->getMockBuilder('Common\Core\Component\Helper\NewsstandHelper')
            ->setConstructorArgs([ $this->il, '/wibble/flob' ])
            ->setMethods([ 'exists', 'generatePath', 'getRelativePath', 'move', 'remove' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Sercurity')
            ->setMethods([ 'hasPermission' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $this->em->expects($this->any())->method('getConverter')
            ->with('Content')->willReturn($this->converter);

        $this->il->expects($this->any())->method('getInstance')
            ->willReturn($this->instance);

        $this->service = $this->getMockBuilder('Api\Service\V1\NewsstandService')
            ->setConstructorArgs([ $this->container, 'Common\Model\Entity\Content' ])
            ->setMethods([ 'getItem', 'assignUser' ])
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

            case 'orm.manager':
                return $this->em;

            case 'core.helper.newsstand':
                return $this->nh;

            case 'core.security':
                return $this->security;

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
        $data = [ 'created' => new \DateTime(), 'title' => 'waldo' ];

        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service->expects($this->any())->method('assignUser')
            ->willReturn($data);

        $this->converter->expects($this->any())->method('objectify')
            ->willReturn($data);

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
        $data = [ 'created' => new \DateTime(), 'title' => 'waldo' ];

        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service->expects($this->any())->method('assignUser')
            ->willReturn($data);

        $this->converter->expects($this->any())->method('objectify')
            ->willReturn($data);

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
        $data = [ 'created' => new \DateTime(), 'title' => 'waldo' ];

        $item = new Content([
            'created' => '2010-01-01 00:00:00',
            'path'    => '/2010/01/01/plugh.mumble'
        ]);

        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $this->service->expects($this->any())->method('assignUser')
            ->willReturn($data);

        $this->converter->expects($this->any())->method('objectify')
            ->willReturn($data);

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
