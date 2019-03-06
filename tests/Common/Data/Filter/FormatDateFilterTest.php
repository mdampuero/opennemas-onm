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

use Common\Data\Filter\FormatDateFilter;

/**
 * Defines tests cases for MapFilter class.
 */
class FormatDateFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'hasParameter', 'getParameter' ])
            ->getMock();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFilterWithoutDate()
    {
        $date   = null;
        $params = [];

        $filter = new FormatDateFilter($this->container, $params);
        $this->assertFalse($filter->filter($date));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFilterWithoutType()
    {
        $date   = '2011-09-23 18:44:09';
        $params = [];

        $filter = new FormatDateFilter($this->container, $params);
        $this->assertFalse($filter->filter($date));
    }

    /**
     * @expectedException \Exception
     */
    public function testFilterWithInvalidDate()
    {
        $date   = '2011-09-23 18: 4';
        $params = [];

        $filter = new FormatDateFilter($this->container, $params);
        $this->assertFalse($filter->filter($date));
    }

    public function testFilterWithCustomLocale()
    {
        $date   = '2011-09-23 18:44:09';
        $params = [
            'type'   => 'long|short',
            'locale' => 'es_ES',
        ];
        $filter = new FormatDateFilter($this->container, $params);

        $this->assertEquals('23 de septiembre de 2011, 18:44', $filter->filter($date));

        $params['locale'] = 'it_IT';

        $filter = new FormatDateFilter($this->container, $params);
        $this->assertEquals('23 settembre 2011 18:44', $filter->filter($date));
    }

    public function testFilterWithCustomTimezone()
    {
        $date   = '2011-09-23 18:44:09 CET';
        $params = [
            'type'     => 'long|long',
            'locale'   => 'en_US',
            'timezone' => 'UTC',
        ];
        $filter = new FormatDateFilter($this->container, $params);

        // I use assert contains as in PHP 7.1 the string returned is September 23, 2011 at 5:44:09 PM GTM
        // and in PHP 7.3 September 23, 2011 at 5:44:09 PM UTC
        $this->assertContains('September 23, 2011 at 5:44:09 PM', $filter->filter($date));
    }

    public function testFilterWithCustomFormat()
    {
        $date   = '2011-09-23 18:44:09 CET';
        $params = [
            'type'   => 'custom',
            'locale' => 'en_US',
            'format' => 'Y-M-d',
        ];
        $filter = new FormatDateFilter($this->container, $params);

        $this->assertEquals('2011-9-23', $filter->filter($date));
    }
}
