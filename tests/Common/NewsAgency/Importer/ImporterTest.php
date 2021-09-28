<?php

namespace Tests\Common\NewsAgency\Component\Importer;

use Common\Model\Entity\Content;
use Common\NewsAgency\Component\Importer\Importer;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Tag;

class ImporterTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get', 'init' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'findBy', 'countBy' ])
            ->getMock();

        $this->service = $this->getMockBuilder('Api\Service\V1\OpinionService')
            ->disableOriginalConstructor()
            ->setMethods([ 'createItem' ])
            ->getMock();

        $this->ps = $this->getMockBuilder('Api\Service\V1\PhotoService')
            ->disableOriginalConstructor()
            ->setMethods([ 'createItem' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Api\Service\V1\TagService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getListByString' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->ds->expects($this->any())->method('init')
            ->willReturn($this->ds);

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->instance = new Instance([ 'internal_name' => 'flob' ]);
        $this->importer = new Importer($this->container, []);
        $this->config   = [
            'content_status'    => 1,
            'content_type_name' => 'article'
        ];
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.content':
                return $this->service;

            case 'api.service.photo':
                return $this->ps;

            case 'api.service.tag':
                return $this->ts;

            case 'core.instance':
                return $this->instance;

            case 'entity_repository':
                return $this->repository;

            case 'orm.manager':
                return $this->em;
        }

        return null;
    }

    /**
     * Tests autoImport when exception.
     */
    public function testAutoImportWhenException()
    {
        $resource = new ExternalResource();

        $result = [
            'ignored'  => 0,
            'imported' => 0,
            'invalid'  => 1
        ];

        $importer = $this->getMockBuilder(get_class($this->importer))
            ->setConstructorArgs([ $this->container, $this->config ])
            ->setMethods([ 'getResources', 'isImported', 'import' ])
            ->getMock();

        $importer->expects($this->once())->method('getResources')
            ->willReturn([ $resource ]);

        $importer->expects($this->once())->method('isImported')
            ->with($resource)
            ->willReturn(false);

        $importer->expects($this->once())->method('import')
            ->with($resource)
            ->will($this->throwException(new \Exception()));

        $this->assertEquals($result, $importer->autoImport());
    }

    /**
     * Tests autoImport when ignored.
     */
    public function testAutoImportWhenIgnored()
    {
        $resource = new ExternalResource();

        $result = [
            'ignored'  => 1,
            'imported' => 0,
            'invalid'  => 0
        ];

        $importer = $this->getMockBuilder(get_class($this->importer))
            ->setConstructorArgs([ $this->container, $this->config ])
            ->setMethods([ 'getResources', 'isImported', 'import' ])
            ->getMock();

        $importer->expects($this->once())->method('getResources')
            ->willReturn([ $resource ]);

        $importer->expects($this->once())->method('isImported')
            ->with($resource)
            ->willReturn(true);

        $this->assertEquals($result, $importer->autoImport());
    }

    /**
     * Tests autoImport when valid.
     */
    public function testAutoImportWhenValid()
    {
        $resource = new ExternalResource();

        $result = [
            'ignored'  => 0,
            'imported' => 1,
            'invalid'  => 0
        ];

        $importer = $this->getMockBuilder(get_class($this->importer))
            ->setConstructorArgs([ $this->container, $this->config ])
            ->setMethods([ 'getResources', 'isImported', 'import' ])
            ->getMock();

        $importer->expects($this->once())->method('getResources')
            ->willReturn([ $resource ]);

        $importer->expects($this->once())->method('isImported')
            ->with($resource)
            ->willReturn(false);

        $importer->expects($this->once())->method('import')
            ->with($resource);

        $this->assertEquals($result, $importer->autoImport());
    }

    /**
     * Tests configure
     */
    public function testConfigure()
    {
        $this->ds->expects($this->any())->method('get')
            ->with('comments_config')
            ->willReturn([ 'with_comments' => 0 ]);

        $this->assertEquals(
            $this->importer,
            $this->importer->configure($this->config)
        );
    }

    /**
     * Tests import when is already imported.
     */
    public function testImportWhenAlreadyImported()
    {
        $resource = new ExternalResource([ 'urn' => 'baz:x-foo' ]);
        $content  = new Content([ 'urn_source' => 'baz:x-foo' ]);

        $this->repository->expects($this->once())->method('countBy')
            ->with([ 'urn_source' => [ [ 'value' => [ $resource->urn ], 'operator' => 'IN' ] ] ], [])
            ->willReturn(1);

        $this->repository->expects($this->once())->method('findBy')
            ->with([ 'urn_source' => [ [ 'value' => [ $resource->urn ], 'operator' => 'IN' ] ] ], [])
            ->willReturn([ $content->urn_source => $content ]);

        $this->assertEquals($content, $this->importer->import($resource, []));
    }

    /**
     * Tests import when resource of type opinion.
     */
    public function testImportWhenOpinion()
    {
        $resource = new ExternalResource([ 'type' => 'opinion' ]);
        $content  = new Content([ 'content_type_name' => 'opinion']);

        $importer = $this->getMockBuilder(get_class($this->importer))
            ->setConstructorArgs([ $this->container, $this->config ])
            ->setMethods([ 'getData' ])
            ->getMock();

        $importer->expects($this->once())->method('getData')
            ->with($resource, [])
            ->willReturn([ 'content_type_name' => 'opinion' ]);

        $this->service->expects($this->once())->method('createItem')
            ->with([ 'content_type_name' => 'opinion' ])
            ->willReturn($content);

        $this->assertEquals($content, $importer->import($resource, []));
    }

    /**
     * Tests import when resource of type photo.
     */
    public function testImportWhenPhoto()
    {
        $resource = new ExternalResource([ 'type' => 'photo' ]);
        $content  = new Content([ 'content_type_name' => 'photo']);

        $importer = $this->getMockBuilder(get_class($this->importer))
            ->setConstructorArgs([ $this->container, $this->config ])
            ->setMethods([ 'getData' ])
            ->getMock();

        $importer->expects($this->once())->method('getData')
            ->with($resource, [])
            ->willReturn([ 'path' => '/foo/baz/glorp', 'params' => [] ]);

        $this->ps->expects($this->once())->method('createItem')
            ->with([ 'content_status' => 1 ], new \SplFileInfo('/foo/baz/glorp'), true)
            ->willReturn($content);

        $this->assertEquals($content, $importer->import($resource, []));
    }

    /**
     * Tests isImported.
     */
    public function testIsImported()
    {
        $resource = new ExternalResource([ 'id' => 10, 'urn' => 'urn:x-foo' ]);

        $this->repository->expects($this->once())->method('countBy')
            ->willReturn(1);

        $this->assertTrue($this->importer->isImported($resource));
    }

    /**
     * Tests setPropagation.
     */
    public function testSetPropagation()
    {
        $property = new \ReflectionProperty(
            get_class($this->importer),
            'propagation'
        );
        $property->setAccessible(true);

        $this->importer->setPropagation(false);

        $this->assertFalse($property->getValue($this->importer));
    }

    /**
     * Tests getAuthor with author
     */
    public function testGetAuthorWithAuthor()
    {
        $method = new \ReflectionMethod($this->importer, 'getAuthor');
        $method->setAccessible(true);

        $resource = new ExternalResource();

        $data = [ 'fk_author' => 1 ];

        $this->assertEquals(
            1,
            $method->invokeArgs($this->importer, [ $resource, $data ])
        );
    }

    /**
     * Tests getAuthor without author
     */
    public function testGetAuthorWithoutAuthor()
    {
        $method = new \ReflectionMethod($this->importer, 'getAuthor');
        $method->setAccessible(true);

        $resource = new ExternalResource();

        $this->assertEmpty(
            $method->invokeArgs($this->importer, [ $resource, [] ])
        );
    }

    /**
     * Tests getAuthor with auto import no map
     */
    public function testGetAuthorWithAutoImportNoMap()
    {
        $method = new \ReflectionMethod($this->importer, 'getAuthor');
        $method->setAccessible(true);

        $this->config['author'] = 1;

        $importer = $this->getMockBuilder(get_class($this->importer))
            ->setConstructorArgs([ $this->container, $this->config ])
            ->setMethods([ 'isAutoImportEnabled' ])
            ->getMock();

        $importer->expects($this->any())->method('isAutoImportEnabled')
            ->willReturn(true);

        $resource         = new ExternalResource();
        $resource->author = 'waldo | bar/foo';

        $this->assertEquals(
            1,
            $method->invokeArgs($importer, [ $resource, [] ])
        );
    }

    /**
     * Tests getAuthor with auto import using map
     */
    public function testGetAuthorWithAutoImportMap()
    {
        $method = new \ReflectionMethod($this->importer, 'getAuthor');
        $method->setAccessible(true);

        $this->config['auto_import'] = true;
        $this->config['authors_map'] = [
            [ 'slug' => 'waldo.*', 'id' => 1 ],
            [ 'slug' => 'qwert.*/foo', 'id' => 2 ],
            [ 'slug' => 'thud', 'id' => 3 ],
        ];

        $importer = $this->getMockBuilder(get_class($this->importer))
            ->setConstructorArgs([ $this->container, $this->config ])
            ->setMethods([ 'isAutoImportEnabled' ])
            ->getMock();

        $importer->expects($this->any())->method('isAutoImportEnabled')
            ->willReturn(true);

        $resource         = new ExternalResource();
        $resource->author = 'waldo | bar/foo';

        $this->assertEquals(
            1,
            $method->invokeArgs($importer, [ $resource, [] ])
        );

        $resource->author = 'qWert | bar/foo';
        $this->assertEquals(
            2,
            $method->invokeArgs($importer, [ $resource, [] ])
        );

        $resource->author = 'bar ThUD | bar/foo - baz';
        $this->assertEquals(
            3,
            $method->invokeArgs($importer, [ $resource, [] ])
        );
    }

    /**
     * Tests getCategory.
     */
    public function testGetCategory()
    {
        $resource = new ExternalResource();

        $method = new \ReflectionMethod(get_class($this->importer), 'getCategory');
        $method->setAccessible(true);

        $this->assertEquals(
            14,
            $method->invokeArgs($this->importer, [ $resource, [ 'fk_content_category' => '14' ] ])
        );

        $resource->category = 'baz';

        $params = [
            'category'       => 20,
            'auto_import'    => true,
            'categories_map' => [
                [ 'id' => 12, 'slug' => 'glorp' ],
                [ 'id' => 13, 'slug' => 'baz' ],
                [ 'id' => 14, 'slug' => 'foo' ]
            ]
        ];

        $config = new \ReflectionProperty(get_class($this->importer), 'config');
        $config->setAccessible(true);
        $config->setValue($this->importer, array_merge($config->getValue($this->importer), $params));

        $this->assertEquals(13, $method->invokeArgs($this->importer, [ $resource, [] ]));

        $resource->category = 'unknown';

        $this->assertEquals(20, $method->invokeArgs($this->importer, [ $resource, [] ]));

        $config->setValue($this->importer, []);

        $this->assertNull($method->invokeArgs($this->importer, [ $resource, [] ]));
    }


    /**
     * Tests getDataForPhoto.
     */
    public function testGetDataForPhoto()
    {
        $resource = new ExternalResource([ 'source' => 'block', 'file_name' => 'name.php' ]);

        $path = new \ReflectionProperty(get_class($this->importer), 'path');
        $path->setAccessible(true);
        $path->setValue($this->importer, '/foo/baz/glorp');

        $result = [ 'path' => '/foo/baz/glorp/block/name.php'];

        $method = new \ReflectionMethod(get_class($this->importer), 'getDataForPhoto');
        $method->setAccessible(true);

        $this->assertEquals($result, $method->invokeArgs($this->importer, [ $resource, [] ]));
    }

    /**
     * Tests getData.
     */
    public function testGetData()
    {
        $resource = new ExternalResource(
            [
                'summary' => 'Lorem ipsum dolor sit amet consectetur.',
                'title'   => 'Lorem, ipsum dolor.',
                'urn'     => 'foo:x-baz',
                'body'    => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis, totam.',
                'href'    => 'https://lorem.ipsum',
                'source' => 'block',
                'file_name' => 'name.php'
            ]
        );

        $data   = [ 'fk_author' => 2 ];
        $method = new \ReflectionMethod(get_class($this->importer), 'getData');
        $method->setAccessible(true);

        $config = new \ReflectionProperty(get_class($this->importer), 'config');
        $config->setAccessible(true);
        $config->setValue($this->importer, [
            'external'      => 'redirect',
            'external_link' => 'https://baz.glorp',
            'target'        => 'glorp',
        ]);

        $result = [
            'content_status'      => 1,
            'content_type_name'   => 'glorp',
            'with_comment'        => 1,
            'description'         => 'Lorem ipsum dolor sit amet consectetur.',
            'title'               => 'Lorem, ipsum dolor.',
            'urn_source'          => 'foo:x-baz',
            'body'                => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis, totam.',
            'href'                => 'https://lorem.ipsum',
            'params'              => [ 'bodyLink' => $config->getValue($this->importer)['external_link'] ],
            'fk_author'           => 2,
            'fk_publisher'        => 2,
            'fk_user_last_editor' => 2,
            'in_home'             => 0,
            'tags'                => [],
            'frontpage'           => 0
        ];

        $this->ts->expects($this->any())->method('getListByString')
            ->with('Lorem, ipsum dolor.')
            ->willReturn(['items' => []]);

        $this->assertEquals($result, $method->invokeArgs($this->importer, [ $resource, $data ]));

        $data['href'] = 'https://lorem.ipsum';
        $config->setValue($this->importer, [
            'external'      => 'original',
        ]);
        $resource->type = 'photo';

        $result['params']['bodyLink'] = 'https://lorem.ipsum';
        $result['content_type_name']  = 'photo';
        $result['path']               = '/flob/importers/block/name.php';
        $result['fk_content_type']    = 8;
        $result['description']        = $resource->body;

        $this->assertEquals($result, $method->invokeArgs($this->importer, [ $resource, $data ]));
    }

    /**
     * Tests getImported.
     */
    public function testGetImported()
    {
        $urn     = 'foo:x-baz';
        $content = new Content([ 'urn_source' => 'foo:x-baz' ]);
        $result  = [
            $content->urn_source => $content
        ];

        $this->repository->expects($this->any())->method('findBy')
            ->with([ 'urn_source' => [ ['value' => [ $urn ], 'operator' => 'IN' ] ] ], [])
            ->willReturn([ $content ]);

        $method = new \ReflectionMethod(
            get_class($this->importer),
            'getImported'
        );
        $method->setAccessible(true);

        $this->assertEquals($result, $method->invokeArgs($this->importer, [ [ $urn ] ]));
    }

    /**
     * Tests getRelated.
     */
    public function testGetRelated()
    {
        $actual = [
            [
                'caption'           => 'Facilis, aperiam!',
                'content_type_name' => 'photo',
                'position'          => 0,
                'source_id'         => 10,
                'target_id'         => 20,
                'type'              => 'photo'
            ]
        ];

        $content = new Content([
            'pk_content'        => 1,
            'content_type_name' => 'photo',
            'description'       => 'Lorem ipsum dolor sit amet.'
         ]);

         $relationships = [ 'featured_frontpage', 'featured_inner' ];

        $result = [
                [
                    'caption'           => 'Facilis, aperiam!',
                    'content_type_name' => 'photo',
                    'position'          => 0,
                    'source_id'         => 10,
                    'target_id'         => 20,
                    'type'              => 'photo'
                ],
                [
                    'caption'           => 'Lorem ipsum dolor sit amet.',
                    'content_type_name' => 'photo',
                    'position'          => 0,
                    'target_id'         => 1,
                    'type'              => 'featured_frontpage'
                ],
                [
                    'caption'           => 'Lorem ipsum dolor sit amet.',
                    'content_type_name' => 'photo',
                    'position'          => 0,
                    'target_id'         => 1,
                    'type'              => 'featured_inner'
                ]
            ];

        $method = new \ReflectionMethod(
            get_class($this->importer),
            'getRelated'
        );
        $method->setAccessible(true);

        $this->assertEquals($result, $method->invokeArgs($this->importer, [ $content, $relationships, $actual ]));
    }

    /**
     * Tests getTags.
     */
    public function testGetTags()
    {
        $resource = new ExternalResource([ 'tags' => [ 'glorp', 'foo', 'baz' ] ]);
        $tags     = [
            'items' => [
                new Tag([ 'id' => 1 ]),
                new Tag([ 'id' => 2 ]),
                new Tag([ 'id' => 3 ])
            ]
        ];

        $method = new \ReflectionMethod(
            get_class($this->importer),
            'getTags'
        );
        $method->setAccessible(true);

        $this->ts->expects($this->once())->method('getListByString')
            ->with([ 'glorp', 'foo', 'baz' ])
            ->willReturn($tags);

        $this->assertEquals([ 1, 2, 3 ], $method->invokeArgs($this->importer, [ $resource ]));
    }
}
