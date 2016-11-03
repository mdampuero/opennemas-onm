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

use Common\Migration\Component\Tracker\ImportTracker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for class class.
 */
class ImportTrackerTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll' ])
            ->getMock();

        $this->translator = new ImportTracker($this->conn);
    }

    /**
     * Tests load.
     */
    public function testLoad()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT pk_content, urn_source, slug FROM contents')
            ->willReturn([
                [
                    'urn_source' => 'frog',
                    'pk_content' => 'fubar',
                    'slug'       => 'glorp'
                ]
            ]);

        $this->translator->load();

        $this->assertTrue($this->translator->isParsed('frog'));
    }
}
