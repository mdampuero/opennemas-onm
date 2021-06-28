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

use Api\Service\V1\ContentService;
use Common\Model\Entity\Content;
use Opennemas\Orm\Core\Entity;

/**
 * Defines test cases for CategoryService class.
 */
class ContentServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->converter = $this->getMockBuilder('Converter' . uniqid())
            ->setMethods([ 'objectify', 'responsify' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('EventDispatcher')
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getConverter', 'getMetadata', 'getRepository', 'persist'
            ])->getMock();

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getId', 'getIdKeys', 'getL10nKeys' ])
            ->getMock();

        $this->fm = $this->getMockBuilder('Opennemas\Data\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods(['filter', 'get', 'set'])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger')
            ->setMethods([ 'error' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([
                'countContents', 'moveContents', 'removeContents'
            ])->getMock();

        $this->security = $this->getMockBuilder('Sercurity')
            ->setMethods([ 'hasPermission' ])
            ->getMock();

        $this->user = new Entity([
            'email'    => 'flob@garply.com',
            'id'       => 1,
            'name'     => 'flob',
            'password' => 'quux',
            'type'     => 1
        ]);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);
        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);
        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->service = $this->getMockBuilder('Api\Service\V1\ContentService')
            ->setMethods([ 'getItem', 'getItemBy', 'getListByIds', 'assignUser' ])
            ->setConstructorArgs([ $this->container, 'Common\Model\Entity\Content' ])
            ->getMock();
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'error.log':
                return $this->logger;

            case 'orm.manager':
                return $this->em;

            case 'core.security':
                return $this->security;

            case 'core.user':
                return $this->user;

            case 'data.manager.filter':
                return $this->fm;
        }

        return null;
    }

    /**
     * Tests createItem when no error.
     */
    public function testCreateItem()
    {
        $data = [ 'name' => 'flob', 'changed' => 'foo' ];

        $this->service->expects($this->any())->method('assignUser')
            ->willReturn([ 'name' => 'flob', 'changed' => 'foo', 'bar' => 1 ]);

        $this->converter->expects($this->any())->method('objectify')
            ->with($this->arrayHasKey('changed'))
            ->will($this->returnArgument(0));

        $this->em->expects($this->once())->method('persist');
        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('content.createItem');
        $this->metadata->expects($this->once())->method('getId')
            ->willReturn([ 'id' => 1 ]);

        $item = $this->service->createItem($data);

        $this->assertEquals('flob', $item->name);
        $this->assertNotEmpty($item->changed);
    }

    /**
     * Tests emptyItem when the item was not found.
     */
    public function testGetItemBySlug()
    {
        $content = new Content([
            'pk_content' => 1,
        ]);

        $this->service->expects($this->once())->method('getItemBy')
            ->with('slug regexp "(.+\"|^)content_slug(\".+|$)"')
            ->willReturn($content);

        $this->assertEquals($this->service->getItemBySlug('content_slug'), $content);
    }

    /**
     * Tests emptyItem when the item was not found.
     */
    public function testGetItemBySlugAndContentType()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_content_type' => 'opinion'
        ]);

        $this->service->expects($this->once())->method('getItemBy')
            ->with('slug regexp "(.+\"|^)content_slug(\".+|$)" and fk_content_type=2')
            ->willReturn($content);

        $this->assertEquals($this->service->getItemBySlugAndContentType('content_slug', 2), $content);
    }

    /**
     * Tests patchItem when no error.
     */
    public function testPatchItem()
    {
        $item = new Content([ 'name' => 'foobar' ]);
        $data = [ 'name' => 'mumble', 'changed' => 'foo' ];

        $this->service->expects($this->any())->method('assignUser')
            ->willReturn([ 'name' => 'mumble', 'changed' => 'foo', 'bar' => 1 ]);

        $this->converter->expects($this->any())->method('objectify')
            ->with($this->arrayHasKey('changed'))
            ->will($this->returnArgument(0));

        $this->service->expects($this->once())->method('getItem')
            ->with(1)->willReturn($item);

        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('content.patchItem', [
                'id'     => 1,
                'item'   => $item,
                'action' => 'Api\Service\V1\OrmService::patchItem'
            ]);

        $this->service->patchItem(1, $data);

        $this->assertEquals('mumble', $item->name);
        $this->assertNotEmpty($item->changed);
    }

    /**
     * Tests patchList when no error.
     */
    public function testPatchList()
    {
        $item = new Content([ 'name' => 'foobar' ]);
        $data = [ 'name' => 'mumble', 'changed' => 'foo' ];

        $this->service->expects($this->any())->method('assignUser')
            ->willReturn([ 'name' => 'mumble', 'changed' => 'foo', 'bar' => 1 ]);

        $this->converter->expects($this->any())->method('objectify')
            ->with($this->arrayHasKey('changed'))
            ->will($this->returnArgument(0));

        $this->service->expects($this->once())->method('getListByIds')
            ->with([ 1 ])->willReturn([ 'items' => [ $item ], 'total' => 1 ]);

        $this->em->expects($this->once())->method('persist')
            ->with($item);
        $this->metadata->expects($this->once())->method('getId')
            ->willReturn([ 'id' => 1 ]);

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('content.patchList', [
                'action' => 'Api\Service\V1\OrmService::patchList',
                'ids'    => [ 1 ],
                'item'   => [ $item ]
            ]);

        $this->service->patchList([ 1 ], $data);

        $this->assertEquals('mumble', $item->name);
        $this->assertNotEmpty($item->changed);
    }

    /**
     * Tests updateItem when no error.
     */
    public function testUpdateItem()
    {
        $item = new Content([ 'name' => 'foobar' ]);
        $data = [ 'name' => 'flob', 'changed' => 'foo' ];

        $this->service->expects($this->any())->method('assignUser')
            ->willReturn([ 'name' => 'mumble', 'changed' => 'foo', 'bar' => 1 ]);

        $this->converter->expects($this->any())->method('objectify')
            ->with($this->arrayHasKey('changed'))
            ->will($this->returnArgument(0));

        $this->service->expects($this->once())->method('getItem')
            ->with(1)->willReturn($item);

        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('content.updateItem', [
                'id'     => 1,
                'item'   => $item,
                'action' => 'Api\Service\V1\OrmService::updateItem'
            ]);

        $this->service->updateItem(1, $data);

        $this->assertEquals('mumble', $item->name);
        $this->assertNotEmpty($item->changed);
    }

    /**
     * Tests assignUser.
     */
    public function testAssignUser()
    {
        $this->service = new ContentService($this->container, 'Common\Model\Entity\Content');

        $method = new \ReflectionMethod($this->service, 'assignUser');
        $method->setAccessible(true);

        $data = [ 'name' => 'mumble' ];

        $this->security->expects($this->any())->method('hasPermission')
            ->willReturn(false);

        $this->assertEquals(
            [ 'name' => 'mumble', 'foo' => 1, 'baz' => 1 ],
            $method->invokeArgs($this->service, [ $data, [ 'foo', 'baz' ] ])
        );
    }

    /**
     * Tests assignUser with Master.
     */
    public function testAssignUserWithMaster()
    {
        $this->service = new ContentService($this->container, 'Common\Model\Entity\Content');

        $method = new \ReflectionMethod($this->service, 'assignUser');
        $method->setAccessible(true);

        $data = [ 'name' => 'mumble' ];

        $this->security->expects($this->any())->method('hasPermission')
            ->willReturn(true);

        $this->assertEquals(
            $data,
            $method->invokeArgs($this->service, [ $data, [ 'foo', 'baz' ] ])
        );
    }

    /**
     * Tests localizeItem.
     */
    public function testLocalizeItem()
    {
        $item = new \Content();

        $item->related_contents = [ [ 'caption' => [ 'es_ES' => 'glorp', 'en_US' => 'baz' ] ] ];

        $result                   = $item;
        $result->related_contents = [ 'caption' => 'glorp' ];

        $method = new \ReflectionMethod($this->service, 'localizeItem');
        $method->setAccessible(true);

        $this->fm->expects($this->any())->method('set')
            ->with($item->related_contents)
            ->willReturn($this->fm);

        $this->fm->expects($this->any())->method('filter')
            ->with('localize', [ 'keys' => [ 'caption' ] ])
            ->willReturn($this->fm);

        $this->fm->expects($this->any())->method('get')
            ->willReturn($result->related_contents);

        $this->assertEquals($result, $method->invokeArgs($this->service, [ $item ]));
    }
}
