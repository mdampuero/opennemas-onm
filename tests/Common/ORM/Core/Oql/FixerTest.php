<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core\Oql;

use Common\ORM\Core\Oql\Fixer;

class FixerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->fixer = new Fixer();
    }

    /**
     * Tests fix for empty and non-empty values.
     */
    public function testFix()
    {
        $oql = 'glork = "norf"';

        $this->fixer->fix([]);
        $this->assertEmpty($this->fixer->getOql());

        $this->fixer->fix('');
        $this->assertEmpty($this->fixer->getOql());

        $this->fixer->fix($oql);
        $this->assertEquals($oql, $this->fixer->getOql());
    }

    /**
     * Tests addCondition for empty, only with modifiers and full OQL queries.
     */
    public function testAddCondition()
    {
        $this->fixer->fix('baz = "flob" order by fubar desc')->addCondition('xyzzy = 3');
        $this->assertEquals('xyzzy = 3 and (baz = "flob") order by fubar desc', $this->fixer->getOql());

        $this->fixer->fix('')->addCondition('xyzzy = 3');
        $this->assertEquals('xyzzy = 3', $this->fixer->getOql());

        $this->fixer->fix('limit 10')->addCondition('xyzzy = 3');
        $this->assertEquals('xyzzy = 3 limit 10', $this->fixer->getOql());
    }

    /**
     * Tests addLimit for empty, only with modifiers and full OQL queries.
     */
    public function testAddLimit()
    {
        $this->fixer->fix('')->addLimit(2);
        $this->assertEquals('limit 2', $this->fixer->getOql());

        $this->fixer->fix('limit 10')->addLimit(5);
        $this->assertEquals('limit 5', $this->fixer->getOql());

        $this->fixer->fix('order by wibble')->addLimit(20);
        $this->assertEquals('order by wibble limit 20', $this->fixer->getOql());

        $this->fixer->fix('flob = "thud" order by waldo asc')->addLimit(3);
        $this->assertEquals('flob = "thud" order by waldo asc limit 3', $this->fixer->getOql());
    }

    /**
     * Tests addOffset for empty, only with modifiers and full OQL queries.
     */
    public function testAddOffset()
    {
        $this->fixer->fix('')->addOffset(2);
        $this->assertEquals('offset 2', $this->fixer->getOql());

        $this->fixer->fix('limit 10')->addOffset(5);
        $this->assertEquals('limit 10 offset 5', $this->fixer->getOql());

        $this->fixer->fix('order by wibble asc offset 10')->addOffset(20);
        $this->assertEquals('order by wibble asc offset 20', $this->fixer->getOql());

        $this->fixer->fix('flob = "thud" order by waldo asc limit 4')->addOffset(3);
        $this->assertEquals('flob = "thud" order by waldo asc limit 4 offset 3', $this->fixer->getOql());
    }

    /**
     * Tests addOrder for empty, only with modifiers and full OQL queries.
     */
    public function testAddOrder()
    {
        $this->fixer->fix('')->addOrder('flob', 'desc');
        $this->assertEquals('order by flob desc', $this->fixer->getOql());

        $this->fixer->fix('order by mumble asc')->addOrder('mumble', 'desc');
        $this->assertEquals('order by mumble desc', $this->fixer->getOql());

        $this->fixer->fix('order by wibble desc')->addOrder('fubar', 'desc');
        $this->assertEquals('order by wibble desc, fubar desc', $this->fixer->getOql());

        $this->fixer->fix('flob = "thud" order by waldo asc limit 10')->addOrder('mumble', 'desc');
        $this->assertEquals('flob = "thud" order by waldo asc, mumble desc limit 10', $this->fixer->getOql());
    }
}
