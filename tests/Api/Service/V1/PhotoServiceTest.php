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

use Common\Model\Entity\Instance;
use Opennemas\Orm\Core\Entity;

use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PhotoServiceTest extends \PHPUnit\Framework\TestCase
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

        $this->dispatcher = $this->getMockBuilder('Common\Core\Component\EventDispatcher')
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getConverter' ,'getMetadata', 'getRepository', 'persist',
                'remove'
            ])->getMock();

        $this->il = $this->getMockBuilder('Common\Core\Component\Loader\InstanceLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'getInstance' ])
            ->getMock();

        $this->processor = $this->getMockBuilder('Common\Core\Component\Image\Processor')
            ->setConstructorArgs([ '/wibble/flob' ])
            ->setMethods([ ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'countBy', 'find', 'findBy' ])
            ->getMock();

        $this->ih = $this->getMockBuilder('Common\Core\Component\Helper\ImageHelper')
            ->setConstructorArgs([ $this->il, '/wibble/flob', $this->processor ])
            ->setMethods([ 'generatePath', 'exists', 'move', 'remove', 'getInformation' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getId', 'getIdKeys', 'getL10nKeys' ])
            ->getMock();

        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);

        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->il->expects($this->any())->method('getInstance')
            ->willReturn($this->instance);

        $this->service = $this->getMockBuilder('Api\Service\V1\PhotoService')
            ->setConstructorArgs([ $this->container, '\Common\Model\Entity\Content' ])
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

            case 'core.helper.image':
                return $this->ih;

            case 'orm.manager':
                return $this->em;

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
        $this->service->createItem();
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

        $data = [ 'title' => 'plugh' ];

        $externalPhoto = m::mock('overload:\Photo');

        $this->ih->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ih->expects($this->once())->method('exists')
            ->willReturn(false);

        $this->converter->expects($this->any())->method('objectify')
            ->willReturn($data);

        $externalPhoto->shouldReceive('create')->once()->andReturn(null);

        $this->service->createItem($data, $file);
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

        $data = [ 'title' => 'plugh' ];

        $this->ih->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ih->expects($this->once())->method('exists')
            ->willReturn(true);

        $this->converter->expects($this->any())->method('objectify')
            ->willReturn($data);

        $this->service->createItem($data, $file);
    }

    /**
     * Tests createItem when correct file
     */
    public function testCreateItemWhenSuccessfulUpload()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $data = [ 'title' => 'plugh' ];

        $externalPhoto = m::mock('overload:\Photo');

        $this->ih->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ih->expects($this->once())->method('exists')
            ->willReturn(false);

        $this->ih->expects($this->once())->method('getInformation')
            ->willReturn([]);

        $this->converter->expects($this->any())->method('objectify')
            ->willReturn($data);

        $this->em->expects($this->once())->method('persist');

        $this->metadata->expects($this->once())->method('getId')
            ->willReturn([ 'id' => 1 ]);

        $this->ih->expects($this->once())->method('move');

        $externalPhoto->shouldReceive('create')->once()->andReturn(1);

        $this->service->createItem($data, $file);
    }

    /**
     * Tests deleteItem when no error.
     */
    public function testDeleteItem()
    {
        $item = new Entity([ 'path' => 'images/2010/01/01/plugh.mumble' ]);

        $this->service->expects($this->exactly(2))->method('getItem')
            ->with(23)
            ->willReturn($item);

        $this->ih->expects($this->once())->method('remove')
            ->with('images/2010/01/01/plugh.mumble');

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteItem when no item found.
     *
     * @expectedException \Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenNoEntity()
    {
        $this->service->expects($this->once())->method('getItem')
            ->with(23)
            ->will($this->throwException(new \Exception()));

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteList when no error.
     */
    public function testDeleteList()
    {
        $itemA = new Entity([
            'name' => 'wubble',
            'path' => 'images/2010/01/01/plugh.wubble'
        ]);
        $itemB = new Entity([
            'name' => 'xyzzy',
            'path' => 'images/2010/01/01/plugh.xyzzy'
        ]);

        $this->metadata->expects($this->at(0))->method('getL10nKeys')
            ->willReturn([]);
        $this->metadata->expects($this->at(2))->method('getId')
            ->with($itemA)->willReturn([ 'id' => 1 ]);
        $this->metadata->expects($this->at(3))->method('getId')
            ->with($itemB)->willReturn([ 'id' => 2 ]);

        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])
            ->willReturn([ $itemA, $itemB ]);
        $this->em->expects($this->exactly(2))->method('remove');

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('content.getListByIds', [
                'ids'   => [ 1, 2 ],
                'items' => [ $itemA, $itemB ]
            ]);

        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('content.deleteList', [
                'ids'   => [ 1, 2 ],
                'items' => [ $itemA, $itemB ]
            ]);

        $this->assertEquals(2, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when invalid list of ids provided.
     *
     * @expectedException \Api\Exception\DeleteListException
     */
    public function testDeleteListWhenInvalidIds()
    {
        $this->service->deleteList('xyzzy');
    }

    /**
     * Tests deleteList when one error happens while removing.
     *
     * @expectedException \Api\Exception\DeleteListException
     */
    public function testDeleteListWhenOneErrorWhileRemoving()
    {
        $itemA = new Entity([
            'name' => 'wubble',
            'path' => 'images/2010/01/01/plugh.wubble'
        ]);
        $itemB = new Entity([
            'name' => 'xyzzy',
            'path' => 'images/2010/01/01/plugh.xyzzy'
        ]);

        $this->metadata->expects($this->at(0))->method('getL10nKeys')
            ->willReturn([]);
        $this->metadata->expects($this->at(2))->method('getId')
            ->with($itemA)->willReturn([ 'id' => 1 ]);

        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])
            ->willReturn([ $itemA, $itemB ]);

        $this->em->expects($this->at(3))->method('remove')->willReturn('foobar');
        $this->em->expects($this->at(5))->method('remove')
            ->will($this->throwException(new \Exception()));

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('content.getListByIds', [
                'ids'   => [ 1, 2 ],
                'items' => [ $itemA, $itemB ]
            ]);

        $this->assertEquals(1, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when an error happens while searching.
     *
     * @expectedException \Api\Exception\DeleteListException
     */
    public function testDeleteListWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])
            ->will($this->throwException(new \Exception()));

        $this->service->deleteList([ 1, 2 ]);
    }
}
