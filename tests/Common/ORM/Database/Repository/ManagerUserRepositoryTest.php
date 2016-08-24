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
use Common\ORM\Database\Repository\ManagerUserRepository;

class ManagerUserRepositoryTest extends \PHPUnit_Framework_TestCase
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
            'name' => 'User',
            'properties' => [
                'id'   => 'integer',
                'name' => 'string',
            ],
            'mapping' => [
                'database' => [
                    'table' => 'user',
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
            new ManagerUserRepository('foo', $this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests refresh and getCategories.
     */
    public function testRefresh()
    {
        $this->conn->expects($this->at(0))->method('fetchAll')->willReturn([
            [ 'id' => 1, 'name' => 'glork' ],
            [ 'id' => 2, 'name' => 'thud' ]
        ]);
        $this->conn->expects($this->at(1))->method('fetchAll')->willReturn([
            [ 'id' => 1, 'internal_name' => 'quux', 'owner_id' => 1 ],
            [ 'id' => 2, 'internal_name' => 'fred', 'owner_id' => 2 ],
            [ 'id' => 3, 'internal_name' => 'fubar', 'owner_id' => 1 ],
        ]);

        $method = new \ReflectionMethod($this->repository, 'refresh');
        $method->setAccessible(true);

        $users = $method->invokeArgs($this->repository, [ [ [ 'id' => 1 ] , [ 'id' => 2 ] ] ]);

        $this->assertEquals(
            [ 'id' => 1, 'name' => 'glork', 'instances' => [ 'quux', 'fubar' ] ],
            $users[1]->getData()
        );

        $this->assertEquals(
            [ 'id' => 2, 'name' => 'thud', 'instances' => [ 'fred' ] ],
            $users[2]->getData()
        );
    }
}
