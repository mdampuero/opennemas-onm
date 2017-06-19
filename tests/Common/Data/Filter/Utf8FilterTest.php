<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Data\Filter;

use Common\Data\Filter\Utf8Filter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for Utf8Filter class.
 */
class Utf8FilterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $container = $this->getMockBuilder('Container')->getMock();

        $this->filter = new Utf8Filter($container);
    }

    /**
     * Tests filter
     */
    public function testFilter()
    {
        $this->assertEquals('Bar', $this->filter->filter('Bar'));
        $this->assertEquals('mÃºmble', $this->filter->filter('múmble'));
        $this->assertEquals('EspaÃ±a', $this->filter->filter('España'));
    }
}
