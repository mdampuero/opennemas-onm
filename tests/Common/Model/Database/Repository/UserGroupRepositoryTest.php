<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Model\File\Repository;

use Opennemas\Orm\Core\Metadata;
use Common\Model\Database\Repository\UserGroupRepository;

class UserGroupRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Opennemas\Orm\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll', 'fetchArray' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'UserGroup',
            'class' => 'Common\Model\Entity\UserGroup',
            'properties' => [
                'pk_user_group' => 'integer',
                'name'          => 'string',
            ],
            'converters' => [
                'default' => [
                    'class' => 'Opennemas\Orm\Database\Data\Converter\BaseConverter'
                ]
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

        $this->cache = $this->getMockBuilder('Opennemas\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->repository =
            new UserGroupRepository('foo', $this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests countUsers when the list of provided ids is an array.
     */
    public function testCountUsersWhenArray()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT user_group_id AS "id", COUNT(1) AS "users" '
                . 'FROM user_user_group '
                . 'LEFT JOIN users ON user_id = id '
                . 'WHERE user_group_id IN (?) AND activated = 1 '
                . 'GROUP BY user_group_id',
                [ [ 1, 2 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([
                [ 'id' => 1, 'users' => 30256 ],
                [ 'id' => 2, 'users' => 3115 ],
            ]);

        $this->assertEquals([
            1 => 30256,
            2 => 3115,
        ], $this->repository->countUsers([ 1, 2 ]));
    }

    /**
     * Tests countUsers when the list of provided ids is empty.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCountUsersWhenEmptyIds()
    {
        $this->repository->countUsers([]);
    }

    /**
     * Tests countUsers when the list of provided ids is an integer.
     */
    public function testCountUsersWhenNotArray()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT user_group_id AS "id", COUNT(1) AS "users" '
                . 'FROM user_user_group '
                . 'LEFT JOIN users ON user_id = id '
                . 'WHERE user_group_id IN (?) AND activated = 1 '
                . 'GROUP BY user_group_id',
                [ [ 1 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([
                [ 'id' => 1, 'users' => 30256 ],
            ]);

        $this->assertEquals([
            1 => 30256,
        ], $this->repository->countUsers(1));
    }

    /**
     * Tests findUsers when the list of provided ids is an array.
     */
    public function testFindUsersWhenArray()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT id, name, email FROM users'
                . ' LEFT JOIN user_user_group ON user_id = id'
                . ' WHERE user_group_id IN (?) AND activated = 1',
                [ [ 1, 2 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([
                [ 'id' => 1, 'name' => 'gorp', 'email' => 'gorp@flob.org' ],
                [ 'id' => 2, 'name' => 'grault', 'email' => 'grault@flob.org' ],
            ]);

        $this->assertEquals([
            [ 'id' => 1, 'name' => 'gorp', 'email' => 'gorp@flob.org' ],
            [ 'id' => 2, 'name' => 'grault', 'email' => 'grault@flob.org' ],
        ], $this->repository->findUsers([ 1, 2 ]));
    }

    /**
     * Tests findUsers when the list of provided ids is empty.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testFindUsersWhenEmptyIds()
    {
        $this->repository->findUsers([]);
    }

    /**
     * Tests findUsers when the list of provided ids is an integer.
     */
    public function testFindUsersWhenNotArray()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT id, name, email FROM users'
                . ' LEFT JOIN user_user_group ON user_id = id'
                . ' WHERE user_group_id IN (?) AND activated = 1',
                [ [ 1 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([
                [ 'id' => 1, 'name' => 'gorp', 'email' => 'gorp@flob.org' ],
            ]);

        $this->assertEquals([
            [ 'id' => 1, 'name' => 'gorp', 'email' => 'gorp@flob.org' ],
        ], $this->repository->findUsers(1));
    }



    /**
     * Tests refresh and getPrivileges.
     */
    public function testRefresh()
    {
        $this->conn->expects($this->at(0))->method('fetchAll')->willReturn([
            [ 'pk_user_group' => 1, 'name' => 'glork' ],
            [ 'pk_user_group' => 2, 'name' => 'thud' ]
        ]);
        $this->conn->expects($this->at(1))->method('fetchAll')->willReturn([
            [ 'pk_fk_user_group' => 1, 'pk_fk_privilege' => 1 ],
            [ 'pk_fk_user_group' => 2, 'pk_fk_privilege' => 2 ]
        ]);

        $method = new \ReflectionMethod($this->repository, 'refresh');
        $method->setAccessible(true);

        $userGroups = $method->invokeArgs(
            $this->repository,
            [ [ [ 'pk_user_group' => 1 ] , [ 'pk_user_group' => 2 ] ] ]
        );

        $this->assertEquals(
            [ 'pk_user_group' => 1, 'name' => 'glork', 'privileges' => [ 1 ] ],
            $userGroups[1]->getData()
        );

        $this->assertEquals(
            [ 'pk_user_group' => 2, 'name' => 'thud', 'privileges' => [ 2 ] ],
            $userGroups[2]->getData()
        );
    }
}
