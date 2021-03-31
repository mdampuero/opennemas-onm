<?php

namespace Tests\Common\Model\Database\Repository;

use Common\Model\Database\Repository\UserRepository;
use Opennemas\Orm\Core\Metadata;

class UserRepositoryTest extends \PHPUnit\Framework\TestCase
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
            'name'  => 'User',
            'class' => 'Common\Model\Entity\User',
            'properties' => [
                'id'   => 'integer',
                'name' => 'string',
            ],
            'converters' => [
                'default' => [
                    'class' => 'Opennemas\Orm\Database\Data\Converter\BaseConverter'
                ]
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

        $this->cache = $this->getMockBuilder('Opennemas\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->repository =
            new UserRepository('foo', $this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests findSubscribers.
     */
    public function testfindSubscribers()
    {
        $user1 = [
            'id' => 1,
            'email' => 'gorp@flob.org',
            'name' => 'gorp',
            'activated' => 1,
            'user_groups' => '7, 10',
        ];

        $user2 = [
            'id' => 2,
            'email' => 'grault@flob.org',
            'name' => 'grault',
            'activated' => 1,
            'user_groups'  => null,
        ];

        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with(
                'SELECT id, email, name, activated,'
                . ' GROUP_CONCAT(DISTINCT user_group_id) as user_groups FROM users '
                . ' LEFT JOIN user_user_group ON user_user_group.user_id = id'
                . ' WHERE type != 0'
                . ' GROUP BY id'
            )->willReturn([ $user1, $user2 ]);

        $this->conn->expects($this->at(1))->method('fetchAll')
            ->with('SELECT * FROM usermeta')
            ->willReturn([
                [ 'user_id' => 1, 'meta_key' => 'register_date', 'meta_value' => '2020-02-13 13:30:00' ],
                [ 'user_id' => 1, 'meta_key' => 'foobar', 'meta_value' => 'baz' ],
                [ 'user_id' => 9, 'meta_key' => 'foobar', 'meta_value' => 'baz' ],
             ]);

        $user1['user_groups'] = [7, 10];
        $user2['user_groups'] = [];

        $user1['register_date'] = '2020-02-13 13:30:00';
        $user1['foobar']        = 'baz';

        $this->assertEquals(
            [ $user1['id'] => $user1, $user2['id'] => $user2 ],
            $this->repository->findSubscribers()
        );
    }
}
