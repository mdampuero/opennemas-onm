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
use Common\ORM\Entity\UserGroup;
use Common\ORM\Database\Persister\UserGroupPersister;

class UserGroupPersisterTest extends \PHPUnit_Framework_TestCase
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
            'name' => 'UserGroup',
            'properties' => [
                'id'         => 'integer',
                'name'       => 'string',
                'privileges' => 'array'
            ],
            'mapping' => [
                'database' => [
                    'table' => 'user_group',
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

        $this->persister = new UserGroupPersister($this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests create for an user group with privileges.
     */
    public function testCreate()
    {
        $entity = new UserGroup([ 'name' => 'xyzzy', 'privileges' => [ 1, 2 ] ]);

        $this->conn->expects($this->once())->method('lastInsertId')->willReturn(1);
        $this->conn->expects($this->once())->method('insert')->with(
            'user_group',
            [ 'id' => null, 'name' => 'xyzzy' ],
            [ \PDO::PARAM_STR, \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(2))->method('executeQuery')->with(
            'replace into user_group_privileges values (?,?),(?,?)',
            [ 1, 1, 1, 2 ],
            [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->at(3))->method('executeQuery')->with(
            'delete from user_group_privileges where user_group_id = ? and privilege_id not in (?)',
            [ 1, [ 1, 2 ] ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $this->persister->create($entity);
        $this->assertEquals(1, $entity->id);
    }

    /**
     * Tests update for an user group with privileges.
     */
    public function testUpdate()
    {
        $entity = new UserGroup([
            'id'         => 1,
            'name'       => 'garply',
            'privileges' => [ 1 ],
        ]);

        $this->conn->expects($this->once())->method('update')->with(
            'user_group',
            [ 'name' => 'garply' ],
            [ 'id' => 1 ],
            [ \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(1))->method('executeQuery')->with(
            'replace into user_group_privileges values (?,?)',
            [ 1, 1 ],
            [ \PDO::PARAM_INT, \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->at(2))->method('executeQuery')->with(
            'delete from user_group_privileges where user_group_id = ? and privilege_id not in (?)',
            [ 1, [ 1 ] ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $this->cache->expects($this->once())->method('delete');
        $this->persister->update($entity);
    }

    /**
     * Tests save privileges with no privileges.
     */
    public function testSavePrivileges()
    {
        $method = new \ReflectionMethod($this->persister, 'savePrivileges');
        $method->setAccessible(true);

        $method->invokeArgs($this->persister, [ 1, [] ]);
    }
}
