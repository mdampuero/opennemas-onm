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
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Opennemas\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $this->converter = $this->getMockBuilder('Common\Model\Database\Data\Converter\MenuConverter')
            ->disableOriginalConstructor()
            ->setMethods([ 'databasifyMenuItems', 'sObjectifyStrict'])
            ->getMock();

        $this->metadata = $this->getMockBuilder('Opennemas\Orm\Core\Metadata')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove', 'getId', 'getPrefixedId', 'getConverter', 'hasMetas', 'getTable' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('Opennemas\Orm\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([
                'delete', 'executeQuery', 'fetchAll' ,'insert', 'lastInsertId',
                'update', 'beginTransaction', 'rollback', 'commit'
                ])->getMock();

        $this->metadata->expects($this->any())->method('getConverter')
            ->willReturn([
                'class' => 'Common\Model\Database\Data\Converter\MenuConverter'
            ]);

        $this->instance  = new Instance([ 'internal_name' => 'plugh' ]);
        $this->persister = new MenuPersister($this->conn, $this->metadata, $this->cache, $this->instance);

        $this->properties = [
                'pk_menu' => 'integer',
                'name' => 'string',
                'position' => 'string',
                'menu_items' => 'array'
        ];
        $this->mapping    = [
                'database' => [
                    'table' => 'menus',
                    'metas' => [
                        'table' => 'menumetas',
                        'ids' => [
                            'pk_menu' => 'fk_menu'
                        ],
                        'key' => 'meta_name'
                    ],
                    'relations' => [
                        'menu_items' => [
                            'table' => 'menu_items',
                            'source_key' => 'pk_menu',
                            'target_key' => 'pk_menu',
                            'columns' => [
                                'pk_item' => [
                                    'type' => 'integer'
                                ],
                                'pk_menu' => [
                                    'type' => 'integer'
                                ],
                                'title' => [
                                    'type' => 'string'
                                ],
                                'link_name' => [
                                    'type' => 'string'
                                ],
                                'type' => [
                                    'type' => 'string'
                                ],
                                'position' => [
                                    'type' => 'integer'
                                ],
                                'pk_father' => [
                                    'type' => 'integer'
                                ],
                                'locale' => [
                                    'type' => 'string'
                                ],
                                'referenceId' => [
                                    'type' => 'integer'
                                ]
                            ]
                        ]
                    ],
                    'columns' => [
                        'pk_menu' => [
                            'type' => 'integer',
                            'options' => [ 'default' => null, 'unsigned' => true, 'autoincrement' => true]
                        ],

                        'name' => [
                            'type' => 'string',
                            'options' => ['length' => 255, 'default' => null, 'notnull' => true]
                        ],

                        'position' => [
                            'type' => 'string',
                            'options' => ['default' => null, 'notnull' => false, 'length' => 50]
                        ]
                    ]
                ]
        ];
        $this->menuWithItems = new Menu([
            'pk_menu' => 1,
            'name' => 'Houyi',
            'position' => 'Fafnir',
            'menu_items' => [
                [
                    'title' => 'Medusa',
                    'link_name' => 'Bacchus',
                    'pk_item' => 1,
                    'position' => 1,
                    'pk_father' => 0,
                    'pk_menu' => 1,
                    'type' => 'Arachne',
                    'locale' => null,
                    'referenceId' => 0
                ],
                [
                    'title' => 'Apollo',
                    'link_name' => 'Heimdalr',
                    'pk_item' => 2,
                    'position' => 2,
                    'pk_father' => 0,
                    'pk_menu' => 1,
                    'type' => 'Odin',
                    'locale' => null,
                    'referenceId' => 0
                ],
            ]
        ]);

        $this->simpleMenu = new Menu([
            'pk_menu' => 1,
            'name' => 'Houyi',
            'position' => 'Fafnir',
            'menu_items' => []
        ]);
    }

    /**
     * Tests remove.
     */
    public function testRemoveWhenSuccessfull()
    {
        $this->metadata->expects($this->any())->method('getId')
            ->with($this->simpleMenu)
            ->willReturn([
                'pk_menu' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);



        $this->persister->remove($this->simpleMenu);
    }

    /**
     * Tests Create.
     * @expectedException \Throwable
     */
    public function testCreateMenuWhenException()
    {
        $this->metadata->mapping    = $this->mapping;
        $this->metadata->properties = $this->properties;
        $this->metadata->expects($this->any())->method('getId')
            ->with($this->menuWithItems)
            ->willReturn([
                'pk_menu' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with('delete from menu_items where pk_menu = ?', [ 1 ], [ \PDO::PARAM_INT ]);

        $this->conn->expects($this->at(3))->method('executeQuery')
            ->with(
                'insert into menu_items'
                . '(pk_item, pk_menu, title, link_name, type, position, pk_father, locale, referenceId) ' .
                'values (?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?)',
                [
                    1, 1, 'Medusa', 'Bacchus', 'Arachne', 1, 0, null, 0,
                    2, 1, 'Apollo', 'Heimdalr', 'Odin', 2, 0, null, 0],
                [
                    1, 1, 2, 2, 2, 1, 1, 2, 1,
                    1, 1, 2, 2, 2, 1, 1, 2, 1
                ]
            )
            ->will($this->throwException(new \Exception()));

        $this->persister->create($this->menuWithItems);
    }

    /**
     * Tests Create.
     */
    public function testCreateMenuWhenSuccessfull()
    {
        $this->metadata->mapping    = $this->mapping;
        $this->metadata->properties = $this->properties;
        $this->metadata->expects($this->any())->method('getId')
            ->with($this->menuWithItems)
            ->willReturn([
                'pk_menu' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with('delete from menu_items where pk_menu = ?', [ 1 ], [ \PDO::PARAM_INT ]);

        $this->conn->expects($this->at(3))->method('executeQuery')
            ->with(
                'insert into menu_items'
                . '(pk_item, pk_menu, title, link_name, type, position, pk_father, locale, referenceId) ' .
                'values (?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?)',
                [
                    1, 1, 'Medusa', 'Bacchus', 'Arachne', 1, 0, null, 0,
                    2, 1, 'Apollo', 'Heimdalr', 'Odin', 2, 0, null, 0
                ],
                [
                    1, 1, 2, 2, 2, 1, 1, 2, 1,
                    1, 1, 2, 2, 2, 1, 1, 2, 1
                ]
            );

        $this->persister->create($this->menuWithItems);
    }

     /**
     * Tests Create.
     * @expectedException \Throwable
     */
    public function testUpdateMenuWhenException()
    {
        $this->metadata->mapping = $this->mapping;
        $this->metadata->properties = $this->properties;
        $this->metadata->expects($this->any())->method('getId')
            ->with($this->menuWithItems)
            ->willReturn([
                'pk_menu' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with('delete from menu_items where pk_menu = ?', [ 1 ], [ \PDO::PARAM_INT ]);

        $this->conn->expects($this->at(3))->method('executeQuery')
            ->with(
                'insert into menu_items'
                . '(pk_item, pk_menu, title, link_name, type, position, pk_father, locale, referenceId) ' .
                'values (?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?)',
                [
                    1, 1, 'Medusa', 'Bacchus', 'Arachne', 1, 0, null, 0,
                    2, 1, 'Apollo', 'Heimdalr', 'Odin', 2, 0, null, 0 ],
                [
                    1, 1, 2, 2, 2, 1, 1, 2, 1,
                    1, 1, 2, 2, 2, 1, 1, 2, 1
                ]
            )
            ->will($this->throwException(new \Exception()));

        $this->persister->update($this->menuWithItems);
    }

    /**
     * Tests Create.
     */
    public function testUpdateMenuWhenSuccessfull()
    {
        $this->metadata->mapping    = $this->mapping;
        $this->metadata->properties = $this->properties;
        $this->metadata->expects($this->any())->method('getId')
            ->with($this->menuWithItems)
            ->willReturn([
                'pk_menu' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with('delete from menu_items where pk_menu = ?', [ 1 ], [ \PDO::PARAM_INT ]);

        $this->conn->expects($this->at(3))->method('executeQuery')
            ->with(
                'insert into menu_items'
                . '(pk_item, pk_menu, title, link_name, type, position, pk_father, locale, referenceId) ' .
                'values (?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?)',
                [
                    1, 1, 'Medusa', 'Bacchus', 'Arachne', 1, 0, null, 0,
                    2, 1, 'Apollo', 'Heimdalr', 'Odin', 2, 0, null, 0
                ],
                [
                    1, 1, 2, 2, 2, 1, 1, 2, 1,
                    1, 1, 2, 2, 2, 1, 1, 2, 1
                ]
            );

        $this->persister->update($this->menuWithItems);
    }
}
