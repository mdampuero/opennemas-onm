<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\Component\Routing;

use Framework\Component\Routing\ContentUrlMatcher;

class ContentUrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->em = $this->getMockBuilder('Repository\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'findOneBy' ])
            ->getMock();

        $this->matcher = new ContentUrlMatcher($this->em);
    }

    public function testEmptyDirtyId()
    {
        $this->assertFalse($this->matcher->matchContentUrl('foo', null));
    }

    public function testValidDirtyId()
    {
        $this->em->expects($this->once())->method('findOneBy')
            ->willReturn('foo');

        $this->matcher
            ->matchContentUrl('foo', '20141005124701000184', '/bar', 'baz');
    }
}
