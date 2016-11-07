<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component\Persister;

use Common\Migration\Component\Persister\ContentPersister;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Persister class.
 */
class PersisterTest extends KernelTestCase
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

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConnection' ])
            ->getMockForAbstractClass();

        $this->em->expects($this->once())->method('getConnection')
            ->willReturn($this->conn);

        $this->persister = new ContentPersister($this->em);
    }

    /**
     * Tests persist.
     */
    public function testPrepare()
    {
        $sqls = [ 'UPDATE fubar SET norf = "glork"' ];

        $this->conn->expects($this->once())->method('executeQuery')
            ->with('UPDATE fubar SET norf = "glork"');

        $this->persister->prepare($sqls);
    }
}
