<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core\Oql;

use Common\ORM\Core\Oql\Sql\SqlTranslator;
use Common\ORM\Core\Metadata;

class SqlTanslatorTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->metadata = new Metadata([
            'name'       => 'Foobar',
            'properties' => [ 'foo' => 'string', 'baz' => 'integer' ],
            'mapping'    => [
                'database' => [
                    'table'   => 'foobar',
                    'columns' => [
                        'foo' => [],
                        'baz' => [],
                    ],
                    'metas'     => [ 'table' => 'foobar_meta' ],
                    'relations' => [
                        'norf' => [
                            'table'   => 'foobar_norf',
                            'ids'     => [ 'foo' => 'foo_id' ],
                            'columns' => [
                                'wibble' => 'string',
                                'flob'   => 'integer'
                            ]
                        ]
                    ],
                    'index' => [ [ 'columns' => [ 'foo' ], 'primary' => true ] ]
                ]
            ]
        ]);

        $this->translator = new SqlTranslator($this->metadata);
    }

    public function testTranslate()
    {
        $oql = 'foo = "bar" and norf = "gorp" and wibble = "fred" and (baz !='
            . '"qux" or baz in [1,2]) order by foo asc limit 20';

        list($tables, $sql, $params, $types) = $this->translator->translate($oql);
        $this->assertEquals([ 'foobar', 'foobar_meta', 'foobar_norf' ], $tables);
        $this->assertEquals(
            'foo = ? and foobar.foo = foobar_meta.foobar_foo and meta_key = ?'
            . ' and meta_value = ? and foobar.foo = foobar_norf.foo_id '
            . 'and wibble = ? and ( baz != ? or baz in ( ? , ? ) ) order by'
                . ' foo asc limit ?',
            $sql
        );
        $this->assertEquals([ 'bar', 'norf', 'gorp', 'fred', 'qux', '1' ,'2', '20' ], $params);
        $this->assertEquals([
            \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR,
            \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT
        ], $types);

        list($tables, $sql, $params, $types) = $this->translator->translate();
        $this->assertEquals([ 'foobar' ], $tables);
        $this->assertEmpty($sql);
        $this->assertEmpty($params);
        $this->assertEmpty($types);
    }

    public function testIsField()
    {
        $method = new \ReflectionMethod($this->translator, 'isField');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'foo', 'T_FIELD' ]));
        $this->assertTrue($method->invokeArgs($this->translator, [ 'mumble', 'T_FIELD' ]));
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
        $date     = new \DateTime();
        $current  = "'" . $date->format('Y-m-d H:i:s') . "'";
        $expected = $date->setTimeZone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');

        $method = new \ReflectionMethod($this->translator, 'translateToken');
        $method->setAccessible(true);

        $this->assertEquals(
            [ '?', 'foo', \PDO::PARAM_STR ],
            $method->invokeArgs($this->translator, [ '"foo"', 'T_STRING', false ])
        );
        $this->assertEquals(
            [ '?', '%foo%', \PDO::PARAM_STR ],
            $method->invokeArgs($this->translator, [ '"foo"', 'T_STRING', true ])
        );
        $this->assertEquals(
            [ '?', $expected, \PDO::PARAM_STR ],
            $method->invokeArgs($this->translator, [ $current, 'T_DATETIME', false ])
        );
        $this->assertEquals(
            [ '!=', null, null ],
            $method->invokeArgs($this->translator, [ '!=', 'O_NOT_EQUALS', false ])
        );
    }

    /**
     * @expectedException \Common\ORM\Core\Exception\InvalidTokenException
     */
    public function testTranslateInvalidToken()
    {
        unset($this->metadata->mapping['database']['metas']);
        $this->translator = new SqlTranslator($this->metadata);

        $method = new \ReflectionMethod($this->translator, 'translateToken');
        $method->setAccessible(true);

        $method->invokeArgs($this->translator, [ 'mumble', 'T_FIELD', false ]);
    }
}
