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

use Common\Data\Filter\SlugFilter;

/**
 * Defines tests cases for SlugFilter class.
 */
class SlugFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')->getMock();
    }

    /**
     * Tests filter when no parameters provided.
     */
    public function testFilterWithNoParameters()
    {
        $str    = 'The string to convert';
        $output = 'the-string-to-convert';

        $filter        = new SlugFilter($this->container);
        $filter->utils = \Mockery::mock('\Onm\StringUtils_' . uniqid());
        $filter->utils->shouldReceive('generateSlug')->once()
            ->with($str, true, '-')
            ->andReturn($output);

        $returnValue = $filter->filter($str);

        $this->assertEquals($output, $returnValue);
    }

    /**
     * Test filter when parameters provided.
     */
    public function testFilterWithParameters()
    {
        $str    = 'The string to convert';
        $output = 'the-string-to-convert';
        $params = [ 'separator' => '', 'stop-list' => false ];

        $filter        = new SlugFilter($this->container, $params);
        $filter->utils = \Mockery::mock('\Onm\StringUtils_' . uniqid());
        $filter->utils->shouldReceive('generateSlug')->once()
            ->with($str, $params['stop-list'], $params['separator'])
            ->andReturn($output);

        $returnValue = $filter->filter($str, $params);

        $this->assertEquals($output, $returnValue);
    }
}
