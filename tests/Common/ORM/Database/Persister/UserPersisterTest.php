<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Common\ORM\Database\Persister;

use Common\ORM\Core\Metadata;
use Common\ORM\Entity\User;
use Common\ORM\Database\Persister\UserPersister;

class UserPersisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'delete', 'executeQuery', 'insert', 'lastInsertId', 'update' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'User',
            'properties' => [
                'id'         => 'integer',
                'name'       => 'string',
                'categories' => 'array'
            ],
            'mapping' => [
                'database' => [
                    'table' => 'users',
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

        $this->cache = $this->getMockBuilder('Common\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'delete' ])
            ->getMock();

        $this->persister = new UserPersister($this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests create for an user group with categories.
     */
    public function testCreate()
    {
        $entity = new User([ 'name' => 'xyzzy', 'categories' => [ 1, 2 ] ]);

        $this->conn->expects($this->once())->method('lastInsertId')->willReturn(1);
        $this->conn->expects($this->once())->method('insert')->with(
            'users',
            [ 'id' => null, 'name' => 'xyzzy' ],
            [ 'id' => \PDO::PARAM_STR, 'name' =>\PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(2))->method('executeQuery')->with(
            'replace into users_content_categories values (?,?),(?,?)',
            [ 1, 1, 1, 2 ],
            [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->at(3))->method('executeQuery')->with(
            'delete from users_content_categories where pk_fk_user = ? and pk_fk_content_category not in (?)',
            [ 1, [ 1, 2 ] ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $this->persister->create($entity);
        $this->assertEquals(1, $entity->id);
    }

    /**
     * Tests update for an user group with categories.
     */
    public function testUpdate()
    {
        $entity = new User([
            'id'         => 1,
            'name'       => 'garply',
            'categories' => [ 1 ],
        ]);

        $this->conn->expects($this->once())->method('update')->with(
            'users',
            [ 'name' => 'garply' ],
            [ 'id' => 1 ],
            [ 'name' => \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(1))->method('executeQuery')->with(
            'replace into users_content_categories values (?,?)',
            [ 1, 1 ],
            [ \PDO::PARAM_INT, \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->at(2))->method('executeQuery')->with(
            'delete from users_content_categories where pk_fk_user = ? and pk_fk_content_category not in (?)',
            [ 1, [ 1 ] ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $this->cache->expects($this->exactly(2))->method('delete');
        $this->persister->update($entity);
    }

    /**
     * Tests save categories with no categories.
     */
    public function testSaveCategories()
    {
        $method = new \ReflectionMethod($this->persister, 'saveCategories');
        $method->setAccessible(true);

        $method->invokeArgs($this->persister, [ 1, [] ]);
    }
}
