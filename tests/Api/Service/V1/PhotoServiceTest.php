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

        $this->dataSet = $this->getMockForAbstractClass('Opennemas\Orm\Core\DataSet');

        $this->dispatcher = $this->getMockBuilder('Common\Core\Component\EventDispatcher')
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getConverter', 'getDataSet', 'getMetadata', 'getRepository', 'persist',
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
            ->setMethods([ 'countBy', 'find', 'findBy', 'findBySql' ])
            ->getMock();

        $this->sh = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container\SettingHelper')
            ->disableOriginalConstructor()
            ->setMethods(['toBoolean'])
            ->getMock();

        $this->ih = $this->getMockBuilder('Common\Core\Component\Helper\ImageHelper')
            ->setConstructorArgs([ $this->il, '/wibble/flob', $this->processor ])
            ->setMethods([ 'generatePath', 'exists', 'move', 'remove', 'getInformation' ])
            ->getMock();

        $this->ip = $this->getMockBuilder('Common\Core\Component\Image\Processor')
            ->disableOriginalConstructor()
            ->setMethods(['open', 'apply', 'save', 'close', 'optimize'])
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

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->dataSet);

        $this->il->expects($this->any())->method('getInstance')
            ->willReturn($this->instance);

        $this->service = $this->getMockBuilder('Api\Service\V1\PhotoService')
            ->setConstructorArgs([ $this->container, '\Common\Model\Entity\Content' ])
            ->setMethods([ 'getItem', 'assignUser', 'updateItem' ])
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

            case 'core.helper.setting':
                return $this->sh;

            case 'core.image.processor':
                return $this->ip;

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
            ->setMethods([ 'getClientOriginalName', 'getPathname' ])
            ->getMock();

        $data = [ 'title' => 'plugh' ];

        $this->ih->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ih->expects($this->once())->method('exists')
            ->willReturn(false);

        $this->ih->expects($this->any())->method('getInformation')
            ->willReturn([]);

        $file->expects($this->once())->method('getPathname')
            ->willReturn('some/path');

        $this->converter->expects($this->any())->method('objectify')
            ->willReturn($data);

        $this->em->expects($this->once())->method('persist');

        $this->metadata->expects($this->once())->method('getId')
            ->willReturn([ 'id' => 1 ]);

        $this->ih->expects($this->once())->method('move');

        $this->dataSet->expects($this->once())->method('get')
            ->with('photo_settings', [])
            ->willReturn([
                'photo_settings' => [
                    'optimize_images' => 'true'
                ]
            ]);

        $this->sh->expects($this->once())->method('toBoolean')
            ->willReturn([
                'optimize_images' => true
            ]);
        $this->ip->expects($this->once())->method('open')
            ->willReturn($this->ip);

        $this->ip->expects($this->once())->method('apply')
            ->with('thumbnail', [1920, 1920, 'center', 'center'])
            ->willReturn($this->ip);

        $this->ip->expects($this->once())->method('optimize')
            ->with([
                'flatten'          => false,
                'quality'          => 65,
                'resolution-units' => 'ppi',
                'resolution-x'     => 72,
                'resolution-y'     => 72
            ])
            ->willReturn($this->ip);

        $this->ip->expects($this->once())->method('save')
            ->willReturn($this->ip);

        $this->ip->expects($this->once())->method('close')
            ->willReturn($this->ip);

        $this->service->expects($this->any())->method('assignUser')
            ->willReturn($data);

        $this->service->expects($this->once())->method('updateItem')
            ->willReturn([]);

        $this->service->createItem($data, $file);
    }

    /**
     * Tests deleteItem when no error.
     */
    public function testDeleteItem()
    {
        $item = new Entity([ 'path' => 'images/2010/01/01/plugh.mumble' ]);

        $this->repository->expects($this->once())->method('findBySql')
            ->willReturn([]);
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
            'pk_content' => 1,
            'name'       => 'wubble',
            'path'       => 'images/2010/01/01/plugh.wubble'
        ]);
        $itemB = new Entity([
            'pk_content' => 2,
            'name'       => 'xyzzy',
            'path'       => 'images/2010/01/01/plugh.xyzzy'
        ]);

        $this->repository->expects($this->once())->method('findBySql')
            ->willReturn([]);
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
                'action'  => 'Api\Service\V1\PhotoService::deleteList',
                'ids'     => [ 1, 2 ],
                'item'    => [ $itemA, $itemB ],
                'related' => []
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

        $this->repository->expects($this->once())->method('findBySql')
            ->willReturn([]);
        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])
            ->willReturn([ $itemA, $itemB ]);

        $this->em->expects($this->once())->method('remove')
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
