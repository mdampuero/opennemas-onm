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
use Common\ORM\Core\UserGroup;
use Common\ORM\Database\Repository\UserGroupRepository;

class UserGroupRepositoryTest extends \PHPUnit_Framework_TestCase
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
                'id'   => 'integer',
                'name' => 'string',
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
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->repository =
            new UserGroupRepository($this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests refresh and getPrivileges.
     */
    public function testRefresh()
    {
        $this->conn->expects($this->at(0))->method('fetchAll')->willReturn([
            [ 'id' => 1, 'name' => 'glork' ],
            [ 'id' => 2, 'name' => 'thud' ]
        ]);
        $this->conn->expects($this->at(1))->method('fetchAll')->willReturn([
            [ 'user_group_id' => 1, 'privilege' => '1' ],
            [ 'user_group_id' => 2, 'privilege' => '2' ]
        ]);

        $method = new \ReflectionMethod($this->repository, 'refresh');
        $method->setAccessible(true);

        $method->invokeArgs($this->repository, [ [ [ 'id' => 1 ] , [ 'id' => 2 ] ] ]);
    }
}
