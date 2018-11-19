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
use Common\ORM\Database\Repository\UserRepository;

class UserRepositoryTest extends \PHPUnit\Framework\TestCase
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
            new UserRepository('foo', $this->conn, $this->metadata, $this->cache);
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
            [ 'pk_fk_user' => 1, 'pk_fk_content_category' => '1' ],
            [ 'pk_fk_user' => 2, 'pk_fk_content_category' => '2' ]
        ]);

        $method = new \ReflectionMethod($this->repository, 'refresh');
        $method->setAccessible(true);

        $users = $method->invokeArgs($this->repository, [ [ [ 'id' => 1 ] , [ 'id' => 2 ] ] ]);

        $this->assertEquals(
            [ 'id' => 1, 'name' => 'glork', 'categories' => [ 1 ] ],
            $users[1]->getData()
        );

        $this->assertEquals(
            [ 'id' => 2, 'name' => 'thud', 'categories' => [ 2 ] ],
            $users[2]->getData()
        );
    }
}
