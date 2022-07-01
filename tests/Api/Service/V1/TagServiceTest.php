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

use Api\Service\V1\TagService;
use Common\Model\Entity\Tag;

/**
 * Defines test cases for TagService class.
 */
class TagServiceTest extends \PHPUnit\Framework\TestCase
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

        $this->fm = $this->getMockBuilder('FilterManager')
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getId', 'getIdKeys', 'getL10nKeys' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([
                'countBy', 'find', 'findBy', 'getIdsByContentType',
                'getNumberOfContents'
            ])->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);
        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);
        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->fm->expects($this->any())->method('filter')
            ->willReturn($this->fm);

        $this->fm->expects($this->any())->method('set')
            ->willReturn($this->fm);

        $this->service = new TagService($this->container, 'Common\Model\Entity\Tag');
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'data.manager.filter':
                return $this->fm;

            case 'orm.manager':
                return $this->em;
        }

        return null;
    }

    /**
     * Tests createItem.
     */
    public function testCreateItem()
    {
        $data = [ 'name' => 'Plugh' ];

        $this->converter->expects($this->any())->method('objectify')
            ->with(array_merge($data, [ 'slug'   => 'plugh', ]))
            ->willReturn(array_merge($data, [ 'slug'   => 'plugh' ]));

        $this->em->expects($this->once())->method('persist');

        $this->fm->expects($this->once())->method('get')
            ->with()->willReturn('plugh');

        $this->metadata->expects($this->once())->method('getId')
            ->willReturn([ 'id' => 1 ]);

        $item = $this->service->createItem($data);

        $this->assertEquals('Plugh', $item->name);
        $this->assertEquals('plugh', $item->slug);
    }

    /**
     * Tests getListByContentTypes when there are no tags associated to any
     * content in the list of types.
     */
    public function testGetListByContentTypesWhenNoTags()
    {
        $this->repository->expects($this->once())->method('getIdsByContentType')
            ->with([ 'article' ])->willReturn([]);

        $response = $this->service->getListByContentTypes([ 'article' ]);

        $this->assertEquals([], $response['items']);
        $this->assertEquals(0, $response['total']);
    }

    /**
     * Tests getListByContentTypes when there are tags associated to any
     * content in the list of types.
     */
    public function testGetListByContentTypesWhenTagsFound()
    {
        $tags = [
            new Tag([ 'id' => 1]),
            new Tag([ 'id' => 2]),
            new Tag([ 'id' => 3]),
        ];

        $this->repository->expects($this->once())->method('getIdsByContentType')
            ->with([ 'article' ])->willReturn([ 1, 2, 3 ]);

        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2, 3 ])->willReturn($tags);

        $response = $this->service->getListByContentTypes([ 'article' ]);

        $this->assertEquals($tags, $response['items']);
        $this->assertEquals(3, $response['total']);
    }

    /**
     * Tests getListBySlugs when no valid slugs provided.
     *
     * @expectedException \Api\Exception\GetListException
     */
    public function testGetListBySlugWhenNoSlugsProvided()
    {
        $this->service->getListBySlugs([]);
    }

    /**
     * Tests getListBySlugs when no valid slugs provided.
     */
    public function testGetListBySlugWhenSlugsProvided()
    {
        $tags = [ new Tag([ 'slug' => 'wobble' ]) ];

        $this->repository->expects($this->at(0))->method('findBy')
            ->with('slug in ["wobble"] and (locale is null or locale = "es_ES")')
            ->willReturn($tags);

        $this->repository->expects($this->at(1))->method('countBy')
            ->with('slug in ["wobble"] and (locale is null or locale = "es_ES")')
            ->willReturn(1);

        $this->repository->expects($this->at(2))->method('findBy')
            ->with('slug in ["wobble"] and locale is null')
            ->willReturn($tags);

        $this->repository->expects($this->at(3))->method('countBy')
            ->with('slug in ["wobble"] and locale is null')
            ->willReturn(1);

        $response = $this->service->getListBySlugs([ 'wobble' ], 'es_ES');

        $this->assertEquals($tags, $response['items']);
        $this->assertEquals(1, $response['total']);

        $response = $this->service->getListBySlugs([ 'wobble' ]);

        $this->assertEquals($tags, $response['items']);
        $this->assertEquals(1, $response['total']);
    }

    /**
     * Tests getListBySlugs when no valid string provided.
     *
     * @expectedException \Api\Exception\GetListException
     */
    public function testGetListByStringWhenNoStringProvided()
    {
        $this->fm->expects($this->at(0))->method('set')
            ->with('')->willReturn($this->fm);
        $this->fm->expects($this->at(1))->method('filter')
            ->with('tags')->willReturn($this->fm);
        $this->fm->expects($this->at(2))->method('get')
            ->willReturn('');
        $this->fm->expects($this->at(3))->method('set')
            ->with([ '' ])->willReturn($this->fm);
        $this->fm->expects($this->at(4))->method('filter')
            ->with('slug')->willReturn($this->fm);
        $this->fm->expects($this->at(5))->method('get')
            ->willReturn([]);

        $this->service->getListByString('');
    }

    /**
     * Tests getListBySlugs when valid string provided.
     */
    public function testGetListByStringWhenStringProvided()
    {
        $tags = [ new Tag([ 'slug' => 'wobble' ]) ];

        $this->fm->expects($this->at(0))->method('set')
            ->with('wobble norf')->willReturn($this->fm);
        $this->fm->expects($this->at(1))->method('filter')
            ->with('tags')->willReturn($this->fm);
        $this->fm->expects($this->at(2))->method('get')
            ->willReturn('wobble,norf');
        $this->fm->expects($this->at(3))->method('set')
            ->with([ 'wobble', 'norf' ])->willReturn($this->fm);
        $this->fm->expects($this->at(4))->method('filter')
            ->with('slug')->willReturn($this->fm);
        $this->fm->expects($this->at(5))->method('get')
            ->willReturn([ 'wobble', 'norf' ]);

        $this->repository->expects($this->once())->method('findBy')
            ->with('slug in ["wobble","norf"] and locale is null')
            ->willReturn($tags);

        $this->repository->expects($this->once())->method('countBy')
            ->with('slug in ["wobble","norf"] and locale is null')
            ->willReturn(1);

        $response = $this->service->getListByString('wobble norf');

        $this->assertEquals($tags, $response['items']);
        $this->assertEquals(1, $response['total']);
    }

    /**
     * Tests getListByIdsKeyMapped when ids provided.
     */
    public function testGetListByIdsKeyMappedWhenIdsProvided()
    {
        $tag = new Tag([ 'id' => 134 ]);

        $this->repository->expects($this->once())->method('find')
            ->with([ 134 ])->willReturn([ $tag ]);

        $this->converter->expects($this->once())->method('responsify')
            ->with([ 134 => $tag ])->willReturn([ 134 => $tag ]);

        $this->assertEquals(
            [ 'items' => [ 134 => $tag ], 'total' => 1 ],
            $this->service->getListByIdsKeyMapped([ 134 ])
        );
    }

    /**
     * Tests getListByIdsKeyMapped when ids provided.
     */
    public function testGetListByIdsKeyMappedWhenIdsProvidedForLocale()
    {
        $tags = [
            new Tag([ 'id' => 30044, 'locale' => 'es', 'name' => 'glorp' ]),
            new Tag([ 'id' => 2795,  'locale' => null, 'name' => 'xyzzy' ]),
            new Tag([ 'id' => 26394, 'locale' => 'gl', 'name' => 'glorp' ]),
        ];

        $this->converter->expects($this->once())->method('responsify')
            ->with([ 30044 => $tags[0], 2795 => $tags[1] ])
            ->willReturn([ 30044 => $tags[0], 2795 => $tags[1] ]);

        $this->repository->expects($this->once())->method('find')
            ->with([ 30044, 2795, 26934 ])->willReturn($tags);

        $this->assertEquals(
            [ 'items' => [ 30044 => $tags[0], 2795 => $tags[1] ], 'total' => 2 ],
            $this->service->getListByIdsKeyMapped([ 30044, 2795, 26934 ], 'es')
        );
    }

    /**
     * Tests getListByIdsKeyMapped when no ids provided.
     */
    public function testGetListByIdsKeyMappedWhenNoIdsProvided()
    {
        $this->assertEquals(
            [ 'items' => [], 'total' => 0 ],
            $this->service->getListByIdsKeyMapped([])
        );
    }

    /**
     * Tests getStats when empty values provided.
     */
    public function testGetStatsWhenEmptyValuesProvided()
    {
        $this->assertEquals([], $this->service->getStats(null));
        $this->assertEquals([], $this->service->getStats([]));
    }

    /**
     * Tests getStats when a tag provided.
     */
    public function testGetStatsWhenTagProvided()
    {
        $tag = new Tag([ 'id' => 134 ]);

        $this->repository->expects($this->once())->method('getNumberOfContents')
            ->with([ 134 ])->willReturn([ 134 => 345 ]);

        $this->assertEquals([ 134 => 345 ], $this->service->getStats($tag));
    }

    /**
     * Tests getStats when a list of tags provided.
     */
    public function testGetStatsWhenListOfTagsProvided()
    {
        $tags = [ new Tag([ 'id' => 134 ]), new Tag([ 'id' => 455 ]) ];

        $this->repository->expects($this->once())->method('getNumberOfContents')
            ->with([ 134, 455 ])->willReturn([ 134 => 345, 455 => 0 ]);

        $this->assertEquals([ 134 => 345, 455 => 0 ], $this->service->getStats($tags));
    }

    /**
     * Tests updateItem.
     */
    public function testUpdateItem()
    {
        $data = [ 'name' => 'Plugh' ];
        $item = new Tag($data);

        $data = [ 'name' => 'Wibble' ];

        $this->converter->expects($this->any())->method('objectify')
            ->with(array_merge($data, [ 'slug'   => 'wibble' ]))
            ->willReturn(array_merge($data, [ 'slug'   => 'wibble' ]));

        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);
        $this->em->expects($this->once())->method('persist');

        $this->fm->expects($this->once())->method('get')
            ->with()->willReturn('wibble');

        $this->service->updateItem(1, $data);

        $this->assertEquals('Wibble', $item->name);
        $this->assertEquals('wibble', $item->slug);
    }

    /**
     * Tests parse.
     */
    public function testParse()
    {
        $data = [
            'name'   => 'Wibble',
            'slug'   => 'wobble',
            'locale' => 'es_ES'
        ];

        $this->fm->expects($this->once())->method('set')
            ->with('wobble')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('filter')
            ->with('slug')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('get')
            ->willReturn('wobble');

        $method = new \ReflectionMethod($this->service, 'parse');
        $method->setAccessible(true);

        $this->assertEquals($data, $method->invokeArgs($this->service, [ $data ]));
    }
}
