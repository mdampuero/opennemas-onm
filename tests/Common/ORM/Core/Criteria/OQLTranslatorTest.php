<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core\Criteria;

use Common\ORM\Core\Criteria\OQLTranslator;

class OQLTanslatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->translator = new OQLTranslator();
    }

    public function testTranslate()
    {
        $oql = 'foo = "bar" and (baz != "qux" or baz in [1,2]) order by foo asc limit 20';

        list($sql, $params, $types) = $this->translator->translate($oql);

        $this->assertEquals('foo = ? and ( baz != ? or baz in ( ? , ? ) ) order by foo asc limit ?', $sql);
        $this->assertEquals([ '"bar"', '"qux"', '1' ,'2', '20' ], $params);
        $this->assertEquals([ \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ], $types);
    }

    public function testIsOperator()
    {
        $method = new \ReflectionMethod($this->translator, 'isOperator');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'C_AND' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'foo' ]));
    }

    public function testIsParameter()
    {
        $method = new \ReflectionMethod($this->translator, 'isParameter');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'T_STRING' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'foo' ]));
    }
}
