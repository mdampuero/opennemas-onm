<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\TemplateCacheHelper;
use Common\ORM\Entity\Category;
use Common\ORM\Entity\Content;
use Common\ORM\Entity\User;

/**
 * Defines test cases for TemplateCacheHelper class.
 */
class TemplateCacheHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Common\Core\Component\Template\Cache\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'delete' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->em->expects($this->any())->method('getConnection')
            ->willReturn($this->conn);

        $this->helper = new TemplateCacheHelper($this->cache, $this->em);
    }

    /**
     * Tests deleteContentsByUsers when list of users is and is not empty.
     */
    public function testDeleteContentsByUsers()
    {
        $user = new User([ 'id' => 1355 ]);
        $user->setOrigin('manager');

        $this->helper->deleteContentsByUsers([]);
        $this->helper->deleteContentsByUsers([ $user ]);

        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT pk_content FROM contents WHERE fk_author IN (?)'
                . ' AND content_status = 1 AND in_litter = 0 and starttime >= ?',
                [ [ 1355, 22081 ], date('Y-m-d H:i:s', strtotime('-1 day')) ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY, \PDO::PARAM_STR ]
            )->willReturn([
                [ 'pk_content' => 493 ],
                [ 'pk_content' => 14880 ]
            ]);

        $this->cache->expects($this->at(0))->method('delete')
            ->with('content', 493);
        $this->cache->expects($this->at(1))->method('delete')
            ->with('content', 14880);

        $this->helper->deleteContentsByUsers([
            new User([ 'id' => 1355 ]),
            new User([ 'id' => 22081 ])
        ]);
    }

    /**
     * Tests deleteNewsstands.
     */
    public function testDeleteNewsstands()
    {
        $this->cache->expects($this->at(0))->method('delete')
            ->with('newsstand', 'list');
        $this->cache->expects($this->at(1))->method('delete')
            ->with('content', 2866);
        $this->cache->expects($this->at(2))->method('delete')
            ->with('content', 18701);

        $this->helper->deleteNewsstands([
            new Content([ 'pk_content' => 2866 ]),
            new Content([ 'pk_content' => 18701 ])
        ]);
    }

    public function testDeleteUsers()
    {
        $this->cache->expects($this->at(0))->method('delete')
            ->with('frontpage', 'authors');
        $this->cache->expects($this->at(1))->method('delete')
            ->with('opinion', 'list');

        $this->helper->deleteUsers([]);

        $this->cache->expects($this->at(0))->method('delete')
            ->with('frontpage', 'authors');
        $this->cache->expects($this->at(1))->method('delete')
            ->with('opinion', 'list');
        $this->cache->expects($this->at(2))->method('delete')
            ->with('frontpage', 'author', 19264);
        $this->cache->expects($this->at(3))->method('delete')
            ->with('opinion', 'listauthor', 19264);
        $this->cache->expects($this->at(4))->method('delete')
            ->with('frontpage', 'author', 20012);
        $this->cache->expects($this->at(5))->method('delete')
            ->with('opinion', 'listauthor', 20012);

        $this->helper->deleteUsers([
            new User([ 'id' => 19264 ]),
            new User([ 'id' => 20012 ])
        ]);
    }
}
