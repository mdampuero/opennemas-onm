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

use Common\Core\Component\Helper\OqlHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for OqlHelper class.
 */
class OqlHelperTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->helper = new OqlHelper();
    }

    /**
     * Tests getFiltersFromQql with multiple values.
     */
    public function testGetFiltersFromOql()
    {
        $this->assertEquals([ '', '', 10, 1 ], $this->helper->getFiltersFromOql());
        $this->assertEquals(
            [ '', '', 30, 1 ],
            $this->helper->getFiltersFromOql('limit 30')
        );
        $this->assertEquals(
            [ '', '', 10, 4 ],
            $this->helper->getFiltersFromOql('limit 10 offset 30')
        );
        $this->assertEquals(
            [ '', 'flob desc', 10, 1 ],
            $this->helper->getFiltersFromOql('order by flob desc')
        );

        $this->assertEquals(
            [ 'glork = "quux"', 'flob desc', 10, 1 ],
            $this->helper->getFiltersFromOql('glork = "quux" order by flob desc')
        );

    }
}
