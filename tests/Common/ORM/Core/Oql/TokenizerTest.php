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

use \Common\ORM\Core\Oql\Tokenizer;

class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->tokenizer = new Tokenizer();
    }

    public function testTokenize()
    {
        $map = [
            [ 'foo',      'T_FIELD' ],
            [ '=',        'O_EQUALS' ],
            [ '"bar"',    'T_STRING' ],
            [ 'and',      'C_AND' ],
            [ '(',        'G_OPARENTHESIS' ],
            [ 'baz',      'T_FIELD' ],
            [ '!=',       'O_NOT_EQUALS' ],
            [ '"qux"',    'T_STRING' ],
            [ 'or',       'C_OR' ],
            [ 'baz',      'T_FIELD' ],
            [ 'in',       'O_IN' ],
            [ '[',        'G_OBRACKET' ],
            [ '1',        'T_INTEGER' ],
            [ ',',        'COMMA' ],
            [ '2',        'T_INTEGER' ],
            [ ']',        'G_CBRACKET' ],
            [ ')',        'G_CPARENTHESIS' ],
            [ 'order by', 'M_ORDER_BY' ],
            [ 'foo',      'T_FIELD' ],
            [ 'asc',      'M_ASC' ],
            [ 'limit',    'M_LIMIT' ],
            [ '20',       'T_INTEGER' ],
        ];

        $oql = 'foo="bar" and (baz!="qux" or baz in [1,2]) order by foo asc limit 20';

        $this->assertEquals($map, $this->tokenizer->tokenize($oql));
    }

    /**
     * @expectedException \Common\ORM\Core\Exception\InvalidQueryException
     */
    public function testCheckOQL()
    {
        $method = new \ReflectionMethod($this->tokenizer, 'checkOQL');
        $method->setAccessible(true);

        $method->invokeArgs($this->tokenizer, [ [ 'foobar' ] ]);
    }

    /**
     * Tests getTokens.
     */
    public function testGetTokens()
    {
        $method = new \ReflectionMethod($this->tokenizer, 'getTokens');
        $method->setAccessible(true);

        $tokens = [
            [ 'foo', 'T_FIELD' ],
            [ '=', 'O_EQUALS' ],
            [ '"bar"', 'T_STRING' ],
            [ 'and', 'C_AND' ],
            [ '(', 'G_OPARENTHESIS' ],
            [ 'baz', 'T_FIELD' ],
            [ '!=', 'O_NOT_EQUALS' ],
            [ '"qux"', 'T_STRING' ],
            [ 'or', 'C_OR' ],
            [ 'baz', 'T_FIELD' ],
            [ 'in', 'O_IN' ],
            [ '[', 'G_OBRACKET' ],
            [ '1', 'T_INTEGER' ],
            [ ',', 'COMMA' ],
            [ '2', 'T_INTEGER' ],
            [ ']', 'G_CBRACKET' ],
            [ ')', 'G_CPARENTHESIS' ]
        ];
        $oql    = 'foo="bar" and (baz!="qux" or baz in [1,2])';

        $this->assertEquals($tokens, $method->invokeArgs($this->tokenizer, [ $oql ]));
    }

    /**
     * @expectedException \Common\ORM\Core\Exception\InvalidTokenException
     */
    public function testTokenizeError()
    {
        $this->tokenizer->tokenize('foo*bar');
    }

    /**
     * Tests replaceTokens.
     */
    public function testReplaceTokens()
    {
        $method = new \ReflectionMethod($this->tokenizer, 'replaceTokens');
        $method->setAccessible(true);

        $oql    = 'thud = "xyzzy"';
        $matrix = [];

        $oql = $method->invokeArgs($this->tokenizer, [ $oql, &$matrix, [ 'T_STRING' ] ]);

        $this->assertEquals('thud =  T_STRING ', $oql);
        $this->assertEquals([ 'T_STRING' => [ '"xyzzy"' ] ], $matrix);

        $oql = $method->invokeArgs($this->tokenizer, [ $oql, &$matrix, [ 'O_EQUALS' ] ]);

        $this->assertEquals('thud O_EQUALS T_STRING ', $oql);
        $this->assertEquals([ 'T_STRING' => [ '"xyzzy"' ], 'O_EQUALS' => [ ' =  ' ] ], $matrix);
    }
}
