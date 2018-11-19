<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Database\Persister;

use Common\ORM\Database\Persister\TagPersister;
use Common\ORM\Entity\Instance;
use Common\ORM\Entity\Tag;
use Common\ORM\Core\Metadata;

/**
 * Defines test cases for TagPersister class.
 */
class TagPersisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Common\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove', 'removeByPattern' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([
                'delete', 'executeQuery', 'fetchAll' ,'insert', 'lastInsertId',
                'update'
            ])->getMock();

        $this->instance = new Instance([ 'internal_name' => 'plugh' ]);

        $this->metadata = new Metadata([
            'name' => 'Tag',
            'properties' => [
                'id'   => 'integer',
                'name' => 'string',
                'slug' => 'string'
            ],
            'mapping' => [
                'database' => [
                    'table' => 'tags',
                    'columns' => [
                        'id' => [
                            'type'    => 'integer',
                            'options' => [ 'default' => null ]
                        ],
                        'name' => [
                            'type'    => 'string',
                            'options' => [ 'default' => null, 'length' => 60 ]
                        ]
                    ],
                    'index' => [
                        [
                            'primary' => true,
                            'columns' => [ 'id' ]
                        ]
                    ]
                ]
            ]
        ]);

        $this->persister = new TagPersister($this->conn, $this->metadata, $this->cache, $this->instance);
    }

    /**
     * Tests remove.
     */
    public function testRemove()
    {
        $tag = new Tag([ 'slug' => 'mumble' ]);

        $this->cache->expects($this->once())->method('removeByPattern')
            ->with('*plugh_article-123');

        $this->conn->expects($this->once())->method('fetchAll')
            ->willReturn([
                [ 'content_type_name' => 'article', 'content_id' => 123 ]
            ]);
        $this->conn->expects($this->once())->method('delete')
            ->with('tags');

        $this->persister->remove($tag);
    }
}
