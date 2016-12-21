<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component\Tracker;

use Common\Migration\Component\Tracker\SimpleIdTracker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for class class.
 */
class SimpleIdTrackerTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'executeQuery', 'fetchAll' ])
            ->getMock();

        $this->tracker = new SimpleIdTracker($this->conn, 'grault');
    }

    /**
     * Tests load.
     */
    public function testLoad()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with("SELECT pk_content_old FROM translation_ids WHERE type = 'grault' ORDER BY pk_content_old DESC LIMIT 1")
            ->willReturn([
                [
                    'pk_content_old' => 'frog',
                    'pk_content'     => 'fubar',
                    'type'           => 'grault',
                    'slug'           => 'glorp'
                ]
            ]);

        $this->tracker->load();

        $this->assertContains('frog', $this->tracker->getParsed());
    }

    /**
     * Tests persist.
     */
    public function testAdd()
    {
        $this->conn->expects($this->once())->method('executeQuery')
            ->with(
                'INSERT INTO translation_ids VALUES (?,?,?,?)',
                [ 'xyzzy', 'flob', 'grault', 'quux' ]
            );

        $this->tracker->add('xyzzy', 'flob', 'quux');

        $this->assertEquals('xyzzy', $this->tracker->getParsed());
    }
}
