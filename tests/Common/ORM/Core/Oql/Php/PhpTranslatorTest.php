<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core\OQL\Php;

use Common\ORM\Core\Oql\Php\PhpTranslator;
use Common\ORM\Core\Metadata;

class PhpTanslatorTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->metadata = new Metadata([
            'name'       => 'Foobar',
            'properties' => [ 'foo' => 'string', 'baz' => 'integer' ],
            'mapping'    => [
                'database' => [
                    'table' => 'foobar',
                    'metas' => [ 'table' => 'foobar_meta' ],
                    'index' => [ [ 'columns' => [ 'foo' ], 'primary' => true ] ]
                ]
            ]
        ]);

        $this->translator = new PhpTranslator($this->metadata);
    }

    /**
     * Tests translate.
     */
    public function testTranslate()
    {
        $oql = 'foo <= "bar" and norf = "gorp" and (baz != "qux" or baz in [1,2]) order by foo asc limit 20';

        list($filter, $order, $size, $offset) = $this->translator->translate($oql);
        $this->assertEquals(
            'return $this->evaluate($entity, \'isLessEquals\', \'foo\', \'bar\')'
            . ' && $this->evaluate($entity, \'isEquals\', \'norf\', \'gorp\')'
            . ' && ($this->evaluate($entity, \'isNotEquals\', \'baz\', \'qux\')'
            . ' || $this->evaluate($entity, \'isInArray\', \'baz\', [\'1\', \'2\']));',
            $filter
        );
        $this->assertEquals([ 'foo', 'asc' ], $order);
        $this->assertEquals(20, $size);
        $this->assertEquals(0, $offset);

        list($filter, $order, $size, $offset) = $this->translator->translate();
        $this->assertEmpty($filter);
        $this->assertEmpty($order);
        $this->assertEmpty($size);
        $this->assertEmpty($offset);
    }

    /**
     * Tests counsumeInfixOperator.
     */
    public function testConsumeInfixOperator()
    {
        $method = new \ReflectionMethod($this->translator, 'consumeInfixOperator');
        $method->setAccessible(true);

        $this->translator->operators = [ 'O_EQUALS' ];
        $this->translator->params    = [
            [ 'value' => 'foo', 'type' => 'T_FIELD' ],
            [ 'value' => '"bar"', 'type' => 'T_STRING' ]
        ];

        $this->assertEquals([ 'isEquals' => [ 'foo', '"bar"' ] ], $method->invokeArgs($this->translator, []));
    }
    /**
     * Tests counsumeOperator.
     */
    public function testConsumeOperator()
    {
        $method = new \ReflectionMethod($this->translator, 'consumeOperator');
        $method->setAccessible(true);

        $this->translator->operators = [ 'C_AND' ];

        $this->assertEquals([ ' && ' => [] ], $method->invokeArgs($this->translator, []));
    }
    /**
     * Tests counsumeOrder.
     */
    public function testConsumeOrder()
    {
        $method = new \ReflectionMethod($this->translator, 'consumeOrderBy');
        $method->setAccessible(true);

        $this->translator->operators = [ 'M_ORDER_BY', 'M_ASC', 'COMMA', 'M_DESC' ];
        $this->translator->params    = [
            [ 'value' => 'foo', 'type' => 'T_FIELD' ],
            [ 'value' => 'bar', 'type' => 'T_FIELD' ],
        ];

        $this->assertEquals(
            [ 'orderBy' => [ 'foo', 'asc', 'bar', 'desc' ] ],
            $method->invokeArgs($this->translator, [])
        );
    }

    /**
     * Tests counsumePrefixOperator.
     */
    public function testConsumePrefixOperator()
    {
        $method = new \ReflectionMethod($this->translator, 'consumePrefixOperator');
        $method->setAccessible(true);

        $this->translator->operators = [ 'M_LIMIT' ];
        $this->translator->params    = [ [ 'value' => 10, 'type' => 'T_INTEGER' ] ];

        $this->assertEquals([ 'limit' => [ 10 ] ], $method->invokeArgs($this->translator, []));
    }

    /**
     * Tests getFilter.
     */
    public function testGetFilter()
    {
        $method = new \ReflectionMethod($this->translator, 'getFilter');
        $method->setAccessible(true);

        $this->assertEquals(
            "return \$this->evaluate(\$entity, 'isEquals', 'foo', 'bar');",
            $method->invokeArgs(
                $this->translator,
                [ [ [ 'isEquals' => [ '"foo"', '"bar"' ] ] ] ]
            )
        );

        $this->assertEmpty($method->invokeArgs($this->translator, [ [ [ 'offset' => [] ] ] ]));
        $this->assertEmpty($method->invokeArgs($this->translator, [ [] ]));
    }

    /**
     * Tests getOffset for present and missing offset parameter.
     */
    public function testGetOffset()
    {
        $method = new \ReflectionMethod($this->translator, 'getOffset');
        $method->setAccessible(true);

        $this->assertEquals(10, $method->invokeArgs($this->translator, [ [ [ 'offset' => [ '10' ] ] ] ]));
        $this->assertEmpty($method->invokeArgs($this->translator, [ [ [ 'orderBy' => [] ] ] ]));
        $this->assertEmpty($method->invokeArgs($this->translator, [ [] ]));
    }

    /**
     * Tests getOrder for present and missing ordering criteria.
     */
    public function testGetOrder()
    {
        $method = new \ReflectionMethod($this->translator, 'getOrder');
        $method->setAccessible(true);

        $this->assertEquals([ 'foo', 'asc' ], $method->invokeArgs($this->translator, [ [ [ 'orderBy' => [ 'foo', 'asc' ] ] ] ]));
        $this->assertEmpty($method->invokeArgs($this->translator, [ [ [ 'offset' => [ 1 ] ] ] ]));
        $this->assertEmpty($method->invokeArgs($this->translator, [ [] ]));
    }

    /**
     * Tests getSize for present and missing limit parameter.
     */
    public function testGetSize()
    {
        $method = new \ReflectionMethod($this->translator, 'getSize');
        $method->setAccessible(true);

        $this->assertEquals(10, $method->invokeArgs($this->translator, [ [ [ 'limit' => [ '10' ] ] ] ]));
        $this->assertEmpty($method->invokeArgs($this->translator, [ [ [ 'offset' => [] ] ] ]));
        $this->assertEmpty($method->invokeArgs($this->translator, [ [] ]));
    }

    /**
     * Tests isArray for valid and non-valid opening array tokens.
     */
    public function testIsArray()
    {
        $method = new \ReflectionMethod($this->translator, 'isArray');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'G_OBRACKET' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'T_FIELD' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'mumble' ]));
    }

    /**
     * Tests isField for field and non-field values.
     */
    public function testIsField()
    {
        $method = new \ReflectionMethod($this->translator, 'isField');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'foo', 'T_FIELD' ]));
        $this->assertTrue($method->invokeArgs($this->translator, [ 'mumble', 'T_FIELD' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'mumble', 'T_STRING' ]));

        $this->translator = new PhpTranslator(new Metadata([
            'name'       => 'Foobar',
            'properties' => [ 'foo' => 'string', 'baz' => 'integer' ],
            'mapping'    => []
        ]));

        $this->assertFalse($method->invokeArgs($this->translator, [ 'mumble', 'T_FIELD' ]));
    }

    /**
     * Tests isIngorable for ignorable and non-ignorable values.
     */
    public function testIsIgnorable()
    {
        $method = new \ReflectionMethod($this->translator, 'isIgnorable');
        $method->setAccessible(true);

        $property = new \ReflectionProperty($this->translator, 'ignoreMode');
        $property->setAccessible(true);
        $property->setValue($this->translator, false);

        $this->assertFalse($method->invokeArgs($this->translator, [ 'foo' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'M_LIMIT' ]));

        $property->setValue($this->translator, true);

        $this->assertFalse($method->invokeArgs($this->translator, [ 'foo' ]));
        $this->assertTrue($method->invokeArgs($this->translator, [ 'M_LIMIT' ]));
    }

    /**
     * Tests isTranslatable for translatable and non-translatable values.
     */
    public function testIsTranslatable()
    {
        $method = new \ReflectionMethod($this->translator, 'isTranslatable');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'C_AND' ]));
        $this->assertFalse($method->invokeArgs($this->translator, [ 'foo' ]));
    }

    /**
     * Tests translateOperator.
     */
    public function testTranslateOperator()
    {
        $method = new \ReflectionMethod($this->translator, 'translateOperator');
        $method->setAccessible(true);

        $this->assertEquals(' && ', $method->invokeArgs($this->translator, [ 'C_AND' ]));
        $this->assertEquals('isLess', $method->invokeArgs($this->translator, [ 'O_LESS' ]));
        $this->assertEquals('orderBy', $method->invokeArgs($this->translator, [ 'M_ORDER_BY' ]));
    }

    /**
     * Tests translateParameter.
     */
    public function testTranslateParameter()
    {
        $date     = new \DateTime();
        $current  = "'" . $date->format('Y-m-d H:i:s') . "'";
        $expected = $date->setTimeZone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');

        $method = new \ReflectionMethod($this->translator, 'translateParameter');
        $method->setAccessible(true);

        $this->assertEquals('waldo', $method->invokeArgs($this->translator, [ '"waldo"', 'T_STRING' ]));
        $this->assertEquals(123, $method->invokeArgs($this->translator, [ 123, 'T_INTEGER' ]));
        $this->assertEquals($expected, $method->invokeArgs($this->translator, [ $current, 'T_DATETIME' ]));
    }
}
