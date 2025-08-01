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

use Common\Model\Database\Persister\ContentPersister;
use Common\Model\Database\Persister\MenuPersister;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Content;
use Opennemas\Orm\Core\Metadata;

/**
 * Defines test cases for ContentPersister class.
 */
class ContentPersisterTest extends \PHPUnit\Framework\TestCase
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

        $this->converter = $this->getMockBuilder('Common\Model\Database\Data\Converter\ContentConverter')
            ->disableOriginalConstructor()
            ->setMethods([ 'databasifyContent', 'sObjectifyStrict'])
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
        $this->persister = new ContentPersister($this->conn, $this->metadata, $this->cache, $this->instance);

        $this->properties = [
                'avaliable' => 'boolean',
                'body' => 'string',
                'changed' => 'datetime',
                'content_status' => 'integer',
                'content_type_name' => 'enum',
                'created' => 'datetime',
                'description' => 'string',
                'endtime' => 'datetime',
                'favorite' => 'boolean',
                'fk_author' => 'integer',
                'fk_content_type' => 'integer',
                'fk_publisher' => 'integer',
                'fk_user_last_editor' => 'integer',
                'frontpage' => 'integer',
                'information' => 'array',
                'in_home' => 'boolean',
                'in_litter' => 'boolean',
                'items' => 'array',
                'params' => 'array',
                'pk_content' => 'integer',
                'position' => 'integer',
                'slug' => 'string',
                'starttime' => 'datetime',
                'title' => 'string',
                'urn_source' => 'string',
                'with_comment' => 'boolean',
                'categories' => 'array',
                'tags' => 'array',
                'related_contents' => 'array',
                'subscriptions' => 'array',
                'title_int' => 'string',
                'pretitle' => 'string',
        ];
        $this->mapping = [
                'database' => [
                    'table' => 'contents',
                    'metas' => [
                        'table' => 'contentmeta',
                        'ids' => [
                            'pk_content' => 'fk_content'
                        ],
                        'key' => 'meta_name'
                    ],
                    'relations' => [
                        'categories' => [
                            'table' => 'content_category',
                            'source_key' => 'pk_content',
                            'target_key' => 'content_id',
                            'return_fields' => 'category_id',
                            'columns' => [
                                'content_id' => [
                                    'type' => 'integer'
                                ],
                                'category_id' => [
                                    'type' => 'integer'
                                ]
                            ]
                        ],
                        'related_contents' => [
                            'table' => 'content_content',
                            'source_key' => 'pk_content',
                            'target_key' => 'source_id',
                            'columns' => [
                                'source_id' => [
                                    'type' => 'integer'
                                ],
                                'target_id' => [
                                    'type' => 'integer'
                                ],
                                'type' => [
                                    'type' => 'string'
                                ],
                                'content_type_name' => [
                                    'type' => 'string'
                                ],
                                'caption' => [
                                    'type' => 'string'
                                ],
                                'position' => [
                                    'type' => 'integer'
                                ],
                            ]
                        ],
                        'tags' => [
                            'table' => 'contents_tags',
                            'source_key' => 'pk_content',
                            'target_key' => 'content_id',
                            'return_fields' => 'tag_id',
                            'columns' => [
                                'content_id' => [
                                    'type' => 'integer'
                                ],
                                'tag_id' => [
                                    'type' => 'integer'
                                ]
                            ]
                        ]
                    ],
                    'columns' => [
                        'pk_content' => [
                            'type' => 'bigint',
                            'options' => [ 'default' => null, 'unsigned' => true, 'autoincrement' => true]
                        ],
                        'fk_content_type' => [
                            'type' => 'integer',
                            'options' => ['default' => null, 'unsigned' => true]
                        ],
                        'content_type_name' => [
                            'type' => 'string',
                            'options' => ['default' => null, 'length' => 20]
                        ],
                        'title' => [
                            'type' => 'string',
                            'options' => ['default' => null, 'notnull' => false, 'length' => 255]
                        ],
                        'description' => [
                            'type' => 'text',
                            'options' => ['default' => null, 'length' => 65532, 'notnull' => false]
                        ],
                        'body' => [
                            'type' => 'text',
                            'options' => ['default' => '', 'length' => 65532, 'notnull' => false]
                        ],
                        'starttime' => [
                            'type' => 'datetimetz',
                            'options' => ['default' => null, 'notnull' => false]
                        ],
                        'endtime' => [
                            'type' => 'datetimetz',
                            'options' => ['default' => null, 'notnull' => false]
                        ],
                        'created' => [
                            'type' => 'datetimetz',
                            'options' => ['default' => null, 'notnull' => false]
                        ],
                        'changed' => [
                            'type' => 'datetimetz',
                            'options' => ['default' => null, 'notnull' => false]
                        ],
                        'content_status' => [
                            'type' => 'integer',
                            'options' => ['default' => '0', 'notnull' => false, 'unsigned' => true]
                        ],
                        'fk_author' => [
                            'type' => 'integer',
                            'options' => [
                                'default' => null,
                                'notnull' => false,
                                'unsigned' => true,
                                'comment' => 'Clave foranea de user'
                            ]
                        ],
                        'fk_publisher' => [
                            'type' => 'integer',
                            'options' => [
                                'default' => null,
                                'notnull' => false,
                                'unsigned' => true,
                                'comment' => 'Clave foranea de user'
                            ]
                        ],
                        'fk_user_last_editor' => [
                            'type' => 'integer',
                            'options' => [
                                'default' => null,
                                'notnull' => false,
                                'unsigned' => true,
                                'comment' => 'Clave foranea de user'
                            ]
                        ],
                        'position' => [
                            'type' => 'integer',
                            'options' => ['default' => '100', 'notnull' => false, 'unsigned' => true]
                        ],
                        'frontpage' => [
                            'type' => 'boolean',
                            'options' => ['default' => '1', 'notnull' => false]
                        ],
                        'in_litter' => [
                            'type' => 'boolean',
                            'options' => [
                                'default' => '0',
                                'notnull' => false,
                                'unsigned' => true,
                                'comment' => '0publicado 1papelera'
                            ]
                        ],
                        'in_home' => [
                            'type' => 'boolean',
                            'options' => ['default' => '0', 'notnull' => false]
                        ],
                        'slug' => [
                            'type' => 'string',
                            'options' => ['default' => null, 'notnull' => false, 'length' => 255]
                        ],
                        'available' => [
                            'type' => 'boolean',
                            'options' => ['default' => '1', 'notnull' => false]
                        ],
                        'with_comment' => [
                            'type' => 'boolean',
                            'options' => ['default' => '1', 'notnull' => false]
                        ],
                        'params' => [
                            'type' => 'text',
                            'options' => ['default' => null, 'notnull' => false, 'length' => 65532]
                        ],
                        'favorite' => [
                            'type' => 'boolean',
                            'options' => ['default' => null, 'notnull' => false]
                        ],
                        'urn_source' => [
                            'type' => 'string',
                            'options' => ['default' => null, 'notnull' => false, 'lenghth' => 255]
                        ]
                    ]
                ]
        ];
    }

    /**
     * Tests remove.
     */
    public function testRemoveContentWhenSuccessfull()
    {
        $this->metadata->mapping = $this->mapping;
        $this->metadata->properties = $this->properties;
        $this->metadata->expects($this->any())->method('getId')
            ->willReturn([
                'pk_content' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with('delete from content_category where content_id = ?', [ 1 ], [ \PDO::PARAM_INT ]);

        $entity = new Content();
        $entity->categories = [1];
        $this->persister->remove($entity);
    }

    /**
     * Tests remove when an exception is thrown.
     *
     * @expectedException \Throwable
     */
    public function testRemoveWhenException()
    {
        $this->metadata->mapping = $this->mapping;
        $this->metadata->properties = $this->properties;
        $this->metadata->expects($this->any())->method('getId')
            ->willReturn([
                'pk_content' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with('delete from content_category where content_id = ?', [ 1 ], [ \PDO::PARAM_INT ])
            ->will($this->throwException(new \Exception()));

        $entity = new Content();
        $entity->categories = [1];
        $this->persister->remove($entity);
    }

    /**
     * Tests Create.
     * @expectedException \Throwable
     */
    public function testCreateContentWhenException()
    {
        $this->metadata->mapping = $this->mapping;
        $this->metadata->properties = $this->properties;
        $this->metadata->expects($this->any())->method('getId')
            ->willReturn([
                'pk_content' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with('delete from content_category where content_id = ?', [ 1 ], [ \PDO::PARAM_INT ]);

        $this->conn->expects($this->at(3))->method('executeQuery')
            ->with('replace into content_category(content_id, category_id) values (?,?)', [ 1, 1 ], [ 1, 1 ])
            ->will($this->throwException(new \Exception()));

        $entity = new Content();
        $entity->categories = [1];
        $this->persister->create($entity);
    }

    /**
     * Tests Create.
     */
    public function testCreateContentWhenSuccessfull()
    {
        $this->metadata->mapping = $this->mapping;
        $this->metadata->properties = $this->properties;
        $this->metadata->expects($this->any())->method('getId')
            ->willReturn([
                'pk_content' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with('delete from content_category where content_id = ?', [ 1 ], [ \PDO::PARAM_INT ]);

        $this->conn->expects($this->at(3))->method('executeQuery')
            ->with('replace into content_category(content_id, category_id) values (?,?)', [ 1, 1 ], [ 1, 1 ]);

        $entity = new Content();
        $entity->categories = [1];
        $this->persister->create($entity);
    }

    /**
     * Tests Create.
     * @expectedException \Throwable
     */
    public function testUpdateContentWhenException()
    {
        $this->metadata->mapping    = $this->mapping;
        $this->metadata->properties = $this->properties;
        $this->metadata->expects($this->any())->method('getId')
            ->willReturn([
                'pk_content' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with('delete from content_category where content_id = ?', [ 1 ], [ \PDO::PARAM_INT ]);

        $this->conn->expects($this->at(3))->method('executeQuery')
            ->with('replace into content_category(content_id, category_id) values (?,?)', [ 1, 1 ], [ 1, 1 ])
            ->will($this->throwException(new \Exception()));

        $entity = new Content();
        $entity->categories = [1];
        $this->persister->update($entity);
    }

    /**
     * Tests Create.
     */
    public function testUpdateContentWhenSuccessfull()
    {
        $this->metadata->mapping = $this->mapping;
        $this->metadata->properties = $this->properties;
        $this->metadata->expects($this->any())->method('getId')
            ->willReturn([
                'pk_content' => 1
            ]);

        $this->metadata->expects($this->once())->method('hasMetas')
            ->willReturn(false);

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with('delete from content_category where content_id = ?', [ 1 ], [ \PDO::PARAM_INT ]);

        $this->conn->expects($this->at(3))->method('executeQuery')
            ->with('replace into content_category(content_id, category_id) values (?,?)', [ 1, 1 ], [ 1, 1 ]);

        $entity = new Content();
        $entity->categories = [1];
        $this->persister->update($entity);
    }
}
