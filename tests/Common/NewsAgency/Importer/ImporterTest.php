<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Parser\EuropaPress;

use Common\NewsAgency\Component\Importer\Importer;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Model\Entity\Instance;
use Content;
use stdClass;

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
            case 'orm.manager':
                return $this->em;

            case 'core.instance':
                return $this->instance;
        }

        return null;
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

        $importer = $this->getMockBuilder('Common\NewsAgency\Component\Importer\Importer')
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

        $importer = $this->getMockBuilder('Common\NewsAgency\Component\Importer\Importer')
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
}
