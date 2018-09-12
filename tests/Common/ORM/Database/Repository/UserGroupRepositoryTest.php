<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Common\ORM\File\Repository;

use Common\ORM\Core\Metadata;
use Common\ORM\Database\Repository\UserGroupRepository;

class UserGroupRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll', 'fetchArray' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'UserGroup',
            'properties' => [
                'pk_user_group' => 'integer',
                'name'          => 'string',
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
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->repository =
            new UserGroupRepository('foo', $this->conn, $this->metadata, $this->cache);
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
