<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
     * Tests getReportSubscribers.
     */
    public function testGetReportSubscribers()
    {
        $user1 = [
            'id' => 1,
            'email' => 'gorp@flob.org',
            'name' => 'gorp',
            'activated' => 1,
            'user_groups' => '7, 10',
            'register_date' => '2020-02-13 13:30:00'
        ];

        $user2 = [
            'id' => 1,
            'email' => 'grault@flob.org',
            'name' => 'grault',
            'activated' => 1,
            'user_groups' => null,
            'register_date' => null
        ];

        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT id, email, name, activated,'
                . ' GROUP_CONCAT(user_group_id) as user_groups,'
                . ' meta_value as register_date FROM users'
                . ' LEFT JOIN user_user_group ON user_user_group.user_id = id'
                . ' LEFT JOIN usermeta ON usermeta.user_id = id AND meta_key = "register_date"'
                . ' WHERE type != 0'
                . ' GROUP BY id'
            )->willReturn([ $user1, $user2 ]);

        $user1['user_groups'] = [7, 10];
        $user1['register_date'] = '2020-02-13';

        $user2['user_groups'] = [];
        $user2['register_date'] = '';


        $this->assertEquals(
            [ $user1, $user2 ],
            $this->repository->getReportSubscribers()
        );
    }
}
