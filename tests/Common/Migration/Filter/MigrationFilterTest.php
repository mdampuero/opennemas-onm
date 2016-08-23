<?php

namespace Tests\Common\Migration;

use Common\Migration\Filter\MigrationFilter;

class MigrationFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWithInvalidParameters()
    {
        $filter = $this->getMockBuilder('Common\Migration\Filter\MigrationFilter')
            ->setConstructorArgs([ null ])
            ->getMockForAbstractClass();
    }

    public function testGetEmptyParameter()
    {
        $filter = $this->getMockForAbstractClass('Common\Migration\Filter\MigrationFilter');

        $this->assertFalse($filter->getParameter('foo'));
    }

    public function testGetParameter()
    {
        $params = [ 'foo' => 'bar' ];

        $filter = $this->getMockBuilder('Common\Migration\Filter\MigrationFilter')
            ->setConstructorArgs([ $params ])
            ->getMockForAbstractClass();

        $this->assertEquals($params['foo'], $filter->getParameter('foo'));
    }
}
