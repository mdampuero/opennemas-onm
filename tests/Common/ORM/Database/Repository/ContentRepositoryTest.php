<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Database\Repository;

use Common\ORM\Core\Metadata;
use Common\ORM\Database\Repository\ContentRepository;

/**
 * Defines test cases for ContentRepository class.
 */
class ContentRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'executeQuery' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'Content',
            'properties' => [
                'pk_content' => 'integer',
                'title'       => 'string',
            ],
            'mapping' => [
                'database' => [
                    'table' => 'contents',
                    'columns' => [
                        'pk_content' => [
                            'type'    => 'integer',
                            'options' => [ 'default' => null ]
                        ],
                        'title' => [
                            'type'    => 'string',
                            'options' => [ 'default' => null, 'length' => 60 ]
                        ]
                    ],
                    'index' => [
                        [
                            'primary' => true,
                            'columns' => [ 'pk_content' ]
                        ]
                    ]
                ]
            ]
        ]);

        $this->cache = $this->getMockBuilder('Common\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository =
            new ContentRepository('foo', $this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests removeContentsInTrash.
     */
    public function testRemoveContentsInTrash()
    {
        $this->conn->expects($this->once())->method('executeQuery')
            ->with('DELETE FROM contents WHERE in_litter = 1');

        $this->repository->removeContentsInTrash();
    }
}
