<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Data\Core;

use Common\Data\Core\AdapterManager;

/**
 * Defines test cases for AdapterManager class.
 */
class AdapterManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $container = $this->getMockBuilder('Container')
            ->getMock();

        $this->am = new AdapterManager($container);
    }

    /**
     * Tests filter.
     */
    public function testAdapter()
    {
        $this->am->adapt('google_analytics', 'frog');
    }

    /**
     * Tests filter when the filter to apply is invalid.
     *
     * @expectedException Common\Data\Core\Exception\InvalidAdapterException
     */
    public function testAdapterWhenInvalidAdapter()
    {
        $this->am->adapt('gorp', 'frog');
    }
}
