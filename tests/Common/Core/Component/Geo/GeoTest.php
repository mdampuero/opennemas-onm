<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Component\Geo;

use Common\Core\Component\Geo\Geo;

/**
 * Defines test cases for Geo class.
 */
class GeoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->locale = $this->getMockBuilder('\Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getLocale' ])
            ->getMock();

        $this->geo = new Geo($this->locale);
    }

    /**
     * Tests getCountries with en_US and es_ES locales.
     */
    public function testGetCountries()
    {
        $this->locale->expects($this->at(0))->method('getLocale')->willReturn('en_US');
        $this->locale->expects($this->at(1))->method('getLocale')->willReturn('es_ES');

        $this->assertContains('Spain', $this->geo->getCountries());
        $this->assertContains('Francia', $this->geo->getCountries());
    }

    /**
     * Tests getRetion for know and unknown countries.
     */
    public function testGetRetions()
    {
        $this->assertEmpty($this->geo->getRegions('foo'));

        $this->assertContains('Madrid', $this->geo->getRegions('ES'));
    }
}
