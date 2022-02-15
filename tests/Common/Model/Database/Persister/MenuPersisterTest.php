<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Model\Database\Persister;

use Common\Model\Database\Persister\MenuPersister;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Menu;
use Opennemas\Orm\Core\Metadata;

/**
 * Defines test cases for MenuPersister class.
 */
class MenuPersisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    // public function setUp()
    // {
        // $this->cache = $this->getMockBuilder('Opennemas\Cache\Redis\Redis')
        //     ->disableOriginalConstructor()
        //     ->setMethods([ 'remove' ])
        //     ->getMock();
        // $this->converter = $this->getMockBuilder('Common\Model\Database\Data\Converter\MenuConverter')
        //     ->disableOriginalConstructor()
        //     ->setMethods([ 'databasifyMenuItems', 'sObjectifyStrict'])
        //     ->getMock();
        // $this->metadata = $this->getMockBuilder('Opennemas\Orm\Core\Metadata')
        //     ->disableOriginalConstructor()
        //     ->setMethods([ 'remove', 'getId', 'getPrefixedId', 'getConverter', 'hasMetas', 'getTable' ])
        //     ->getMock();

        // $this->conn = $this->getMockBuilder('Opennemas\Orm\Core\Connection')
        //     ->disableOriginalConstructor()
        //     ->setMethods([
        //         'delete', 'executeQuery', 'fetchAll' ,'insert', 'lastInsertId',
        //         'update', 'beginTransaction', 'rollback', 'commit'
        //     ])->getMock();


        // $this->instance = new Instance([ 'internal_name' => 'plugh' ]);

        // $this->metadata = new Metadata([
        //     'name' => 'Menu',
        //     'class' => 'Common\Model\Entity\Menu',
        //     'properties' => [
        //         'pk_menu' => 'integer',
        //         'name'=> 'string',
        //         'position'=> 'string',
        //         'menu_items'=> 'array'
        //     ],
        //     'converters' => [
        //         'default' => [
        //             'class'=> 'Common\Model\Database\Data\Converter\MenuConverter',
        //             'arguments' => [ '@orm.metadata.menu' ]
        //         ]
        //     ],
        //     'repositories' => [
        //         'instance' => [
        //             'class' => 'Opennemas\Orm\Database\Repository\BaseRepository',
        //             'arguments' => [ '@orm.connection.instance', '@orm.metadata.menu', '@cache.connection.instance' ]
        //         ]
        //     ],
        //     'persisters' => [
        //         'instance' => [
        //             'class' => 'Common\Model\Database\Persister\MenuPersister',
        //             'arguments' => [ '@orm.connection.instance', '@orm.metadata.menu', '@cache.connection.instance' ]
        //         ]
        //     ],
        //     'mapping' => [
        //         'database' => [
        //             'table' => 'menus',
        //             'metas' => [
        //                 'table' => 'menumetas',
        //                 'ids' => [
        //                     'pk_menu' => 'fk_menu'
        //                 ],
        //                 'key' => 'meta_name'
        //             ],
        //             'relations' => [
        //                 'menu_items' => [
        //                     'table' => 'menu_items',
        //                     'source_key' => 'pk_menu',
        //                     'target_key' => 'pk_menu',
        //                     'columns' => [
        //                         'pk_item' => [
        //                             'type' => 'integer'
        //                         ],
        //                         'pk_menu' => [
        //                             'type' => 'integer'
        //                         ],
        //                         'title' => [
        //                             'type' => 'string'
        //                         ],
        //                         'link_name' => [
        //                             'type' => 'string'
        //                         ],
        //                         'type' => [
        //                             'type' => 'string'
        //                         ],
        //                         'position' => [
        //                             'type' => 'integer'
        //                         ],

        //                         'pk_father' => [
        //                             'type' => 'integer'
        //                         ]
        //                     ]
        //                 ]
        //             ],
        //             'columns' => [
        //                 'pk_menu' => [
        //                     'type' => 'integer',
        //                     'options' => [ 'default' => null, 'unsigned' => true, 'autoincrement' => true]
        //                 ],

        //                 'name' => [
        //                     'type' => 'string',
        //                     'options' => ['length' => 255, 'default' => null, 'notnull' => true]
        //                 ],

        //                 'position' => [
        //                     'type' => 'string',
        //                     'options' => ['default' => null, 'notnull' => false, 'length' => 50]
        //                 ]

        //             ]
        //         ]

        //     ]

        // ]);
    //     $this->menuWithItems = new Menu([
    //         'pk_menu' => 1,
    //         'name' => 'Houyi',
    //         'position' => 'Fafnir',
    //         'menu_items' => [
    //             [
    //                 'title' => 'Medusa',
    //                 'link_name' => 'Bacchus',
    //                 'pk_item' => 1,
    //                 'position' => 1,
    //                 'pk_father' => 0,
    //                 'pk_menu' => 1,
    //                 'type' => 'Arachne'
    //             ],
    //             [
    //                 'title' => 'Apollo',
    //                 'link_name' => 'Heimdalr',
    //                 'pk_item' => 2,
    //                 'position' => 2,
    //                 'pk_father' => 0,
    //                 'pk_menu' => 1,
    //                 'type' => 'Odin'
    //             ],
    //         ]
    //     ]);
    //     $this->simpleMenu = new Menu ([
    //         'pk_menu' => 1,
    //         'name' => 'Houyi',
    //         'position' => 'Fafnir',
    //         'menu_items' => []
    //     ]);
    // }

    /**
     * Tests remove.
     */
    // public function testRemove()
    // {
    //     $this->metadata->expects($this->any())->method('getConverter')
    //         ->willReturn([
    //             'class' => 'Common\Model\Database\Data\Converter\MenuConverter'
    //         ]);

    //     $this->metadata->expects($this->any())->method('hasMetas')
    //         ->willReturn(false);

    //     $this->metadata->expects($this->any())->method('getTable')
    //         ->willReturn([]);

    //     $this->cache->expects($this->any())->method('remove')
    //         ->willReturn([]);

    //     $this->conn->expects($this->any())->method('beginTransaction')
    //         ->willReturn([]);

    //     $this->conn->expects($this->any())->method('delete')
    //         ->willReturn([]);

    //     $this->metadata->expects($this->any())->method('getId')
    //         ->with($this->simpleMenu)
    //         ->willReturn([
    //             'pk_menu' => 1
    //         ]);

    //     $this->conn->expects($this->any())->method('executeQuery')
    //         ->willReturn([]);

    //     $persister = new MenuPersister($this->conn, $this->metadata, $this->cache, $this->instance);
    //     $persister->remove($this->simpleMenu);
    // }

    /**
     * Tests update.
     */
    // public function testUpdate()
    // {
    //     $this->metadata->expects($this->any())->method('getConverter')
    //         ->willReturn([
    //             'class' => 'Common\Model\Database\Data\Converter\MenuConverter'
    //         ]);

    //     $this->metadata->expects($this->any())->method('hasMetas')
    //         ->willReturn(false);

    //     $this->metadata->expects($this->any())->method('getTable')
    //         ->willReturn([]);

    //     $this->cache->expects($this->any())->method('remove')
    //         ->willReturn([]);

    //     $this->conn->expects($this->any())->method('beginTransaction')
    //         ->willReturn([]);

    //     $this->conn->expects($this->any())->method('delete')
    //         ->willReturn([]);

    //     $this->metadata->expects($this->any())->method('getId')
    //         ->with($this->menuWithItems)
    //         ->willReturn([
    //             'pk_menu' => 1
    //         ]);

    //     $this->conn->expects($this->any())->method('executeQuery')
    //         ->willReturn([]);

    //     $persister = new MenuPersister($this->conn, $this->metadata, $this->cache, $this->instance);
    //     $persister->update($this->menuWithItems);
    // }
}
