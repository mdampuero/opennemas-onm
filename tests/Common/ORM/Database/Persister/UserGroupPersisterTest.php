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
use Common\ORM\Entity\Instance;
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

        $this->instance = new Instance([ 'internal_name' => 'glorp' ]);

        $this->metadata = new Metadata([
            'name' => 'UserGroup',
            'properties' => [
                'pk_user_group' => 'integer',
                'name'          => 'string',
                'privileges'    => 'array'
            ],
            'mapping' => [
                'database' => [
                    'table' => 'user_groups',
                    'columns' => [
                        'pk_user_group' => [
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
                            'columns' => [ 'pk_user_group' ]
                        ]
                    ]
                ]
            ]
        ]);

        $this->cache = $this->getMockBuilder('Common\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove', 'removeByPattern' ])
            ->getMock();

        $this->persister = new UserGroupPersister($this->conn, $this->metadata, $this->cache, $this->instance);
    }

    /**
     * Tests create for an user group with privileges.
     */
    public function testCreate()
    {
        $entity = new UserGroup([ 'name' => 'xyzzy', 'privileges' => [ 1, 2 ] ]);

        $this->conn->expects($this->once())->method('lastInsertId')->willReturn(1);
        $this->conn->expects($this->once())->method('insert')->with(
            'user_groups',
            [ 'pk_user_group' => null, 'name' => 'xyzzy' ],
            [ 'pk_user_group' => \PDO::PARAM_STR, 'name' => \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(2))->method('executeQuery')->with(
            'replace into user_groups_privileges values (?,?),(?,?)',
            [ 1, 1, 1, 2 ],
            [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->at(3))->method('executeQuery')->with(
            'delete from user_groups_privileges where pk_fk_user_group = ? and pk_fk_privilege not in (?)',
            [ 1, [ 1, 2 ] ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $this->persister->create($entity);
        $this->assertEquals(1, $entity->pk_user_group);
    }

    /**
     * Tests update for an user group with privileges.
     */
    public function testUpdate()
    {
        $entity = new UserGroup([
            'pk_user_group' => 1,
            'name'          => 'garply',
            'privileges'    => [ 1, 2 ],
        ]);

        $this->conn->expects($this->once())->method('update')->with(
            'user_groups',
            [ 'name' => 'garply' ],
            [ 'pk_user_group' => 1 ],
            [ 'name' => \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(1))->method('executeQuery')->with(
            'replace into user_groups_privileges values (?,?),(?,?)',
            [ 1, 1, 1, 2 ],
            [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->at(2))->method('executeQuery')->with(
            'delete from user_groups_privileges where pk_fk_user_group = ? and pk_fk_privilege not in (?)',
            [ 1, [ 1, 2 ] ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $this->cache->expects($this->exactly(2))->method('remove');
        $this->persister->update($entity);
    }

    /**
     * Tests remove for an user group with privileges.
     */
    public function testRemove()
    {
        $entity = new UserGroup([
            'pk_user_group' => 1,
            'name'          => 'garply',
            'privileges'    => [ 1 ],
        ]);

        $entity->refresh();

        $this->conn->expects($this->once())->method('delete')->with(
            'user_groups',
            [ 'pk_user_group' => 1 ]
        );

        $this->conn->expects($this->once())->method('executeQuery')->with(
            'delete from user_groups_privileges where pk_fk_user_group = ?',
            [ 1 ]
        );

        $this->cache->expects($this->exactly(2))->method('remove');
        $this->cache->expects($this->exactly(1))->method('removeByPattern')
            ->with('*glorp_user-*');

        $this->persister->remove($entity);
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
