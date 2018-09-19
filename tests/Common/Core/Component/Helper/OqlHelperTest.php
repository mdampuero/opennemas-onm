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
use Common\ORM\Entity\User;

/**
 * Defines test cases for OqlHelper class.
 */
class OqlHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->um = $this->getMockBuilder('UserManager')
            ->setMethods([ 'findByUserMeta' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->um->expects($this->any())->method('findByUserMeta')
            ->willReturn([ new User([ 'id' => 1 ]), new User([ 'id' => 2 ]) ]);

        $this->container->expects($this->any())->method('get')
            ->willReturn($this->um);

        $this->helper = new OqlHelper($this->container);
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

        $this->assertEquals(
            [ 'fk_author NOT IN (1,2)', 'flob desc', 10, 1 ],
            $this->helper->getFiltersFromOql('blog = "0" order by flob desc')
        );

        $this->assertEquals(
            [ 'fk_author IN (1,2)', 'flob desc', 10, 1 ],
            $this->helper->getFiltersFromOql('blog = "1" order by flob desc')
        );
    }
}
