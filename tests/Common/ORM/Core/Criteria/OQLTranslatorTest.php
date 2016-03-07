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
use Common\ORM\Core\Metadata;

class OQLTanslatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $metadata = new Metadata([
            'name'       => 'Foobar',
            'properties' => [ 'foo', 'baz' ]
        ]);

        $this->translator = new OQLTranslator($metadata);
    }

    public function testTranslate()
    {
        $oql = 'foo = "bar" and (baz != "qux" or baz in [1,2]) order by foo asc limit 20';

        list($sql, $params, $types) = $this->translator->translate($oql);

        $this->assertEquals('foo = ? and ( baz != ? or baz in ( ? , ? ) ) order by foo asc limit ?', $sql);
        $this->assertEquals([ '"bar"', '"qux"', '1' ,'2', '20' ], $params);
        $this->assertEquals([ \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ], $types);

        list($sql, $params, $types) = $this->translator->translate('');
        $this->assertEmpty($sql);
        $this->assertEmpty($params);
        $this->assertEmpty($types);
    }

    public function testIsField()
    {
        $method = new \ReflectionMethod($this->translator, 'isField');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'foo', 'T_FIELD' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'mumble', 'T_FIELD' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'mumble', 'T_STRING' ]));
    }

    public function testIsParameter()
    {
        $method = new \ReflectionMethod($this->translator, 'isParameter');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'T_STRING' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'foo' ]));
    }

    public function testIsTranslatable()
    {
        $method = new \ReflectionMethod($this->translator, 'isTranslatable');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'C_AND' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'foo' ]));
    }

    public function testTranslateToken()
    {
        $method = new \ReflectionMethod($this->translator, 'translateToken');
        $method->setAccessible(true);

        $this->assertEquals([ '?', 'foo', \PDO::PARAM_STR ], $method->invokeArgs($this->translator, [ 'foo', 'T_STRING', false ]));
        $this->assertEquals([ 'foo', null, null ], $method->invokeArgs($this->translator, [ 'foo', 'T_STRING', true ]));
        $this->assertEquals([ '!=', null, null ], $method->invokeArgs($this->translator, [ '!=', 'O_NOT_EQUALS', false ]));
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidTokenException
     */
    public function testTranslateInvalidToken()
    {
        $method = new \ReflectionMethod($this->translator, 'translateToken');
        $method->setAccessible(true);

        $method->invokeArgs($this->translator, [ 'mumble', 'T_FIELD', false ]);
    }
}
