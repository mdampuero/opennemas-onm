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

use Common\ORM\Core\Criteria\OQLTokenizer;

class OQLTokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->translator = new OQLTokenizer();
    }

    public function testTokenize()
    {
        $map = [
            [ 'foo', 'T_FIELD' ],
            [ '=', 'O_EQUALS' ],
            [ '"bar"', 'T_STRING' ],
            [ 'and', 'C_AND' ],
            [ '(', 'G_OPARENTHESIS' ],
            [ 'baz', 'T_FIELD' ],
            [ '!', 'O_NOT' ],
            [ '=', 'O_EQUALS' ],
            [ '"qux"', 'T_STRING' ],
            [ 'or', 'C_OR' ],
            [ 'baz', 'T_FIELD' ],
            [ 'in', 'O_IN' ],
            [ '[1,2]', 'T_ARRAY' ],
            [ ')', 'G_CPARENTHESIS' ],
            [ 'order by', 'M_ORDER' ],
            [ 'foo', 'T_FIELD' ],
            [ 'asc', 'M_ASC' ],
            [ 'limit', 'M_LIMIT' ],
            [ '20', 'T_INTEGER' ],
        ];

        $oql = 'foo="bar" and (baz!="qux" or baz in [1,2]) order by foo asc limit 20';

        $this->assertEquals($map, $this->translator->tokenize($oql));
    }

    /**
     * @expectedException \Exception
     */
    public function testCheckOQL()
    {
        $method = new \ReflectionMethod($this->translator, 'checkOQL');
        $method->setAccessible(true);

        $method->invokeArgs($this->translator, [ [ 'foobar' ] ]);
    }

    public function testGetTokens()
    {
        $method = new \ReflectionMethod($this->translator, 'getTokens');
        $method->setAccessible(true);

        $tokens = [ 'foo', '=', '"bar"', 'and', '(', 'baz', '!', '=', '"qux"', 'or', 'baz' ,'in', '[', '1', ',' ,'2', ']', ')' ];
        $oql    = 'foo="bar" and (baz!="qux" or baz in [1,2])';

        $this->assertEquals($tokens, $method->invokeArgs($this->translator, [ $oql ]));
    }

    /**
     * @expectedException \Exception
     */
    public function testTokenizeError()
    {
        $this->translator->tokenize('foo*bar');
    }

    public function testIsSentence()
    {
        $method = new \ReflectionMethod($this->translator, 'isSentence');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'C_AND' ]));
        $this->assertTrue($method->invokeArgs($this->translator, [ 'T_FIELD T_OPERATOR T_LITERAL' ]));
        $this->assertTrue($method->invokeArgs($this->translator, [ 'S_CONDITION S_MODIFIER' ]));

        $this->assertFalse($method->invokeArgs($this->translator, [ 'G_OBRACKET T_FIELD' ]));
    }

    public function testIsToken()
    {
        $method = new \ReflectionMethod($this->translator, 'isToken');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->translator, [ 'foobar' ]));
        $this->assertTrue($method->invokeArgs($this->translator, [ '"foobar"' ]));
        $this->assertTrue($method->invokeArgs($this->translator, [ '[' ]));

        $this->assertFalse($method->invokeArgs($this->translator, [ 'foo*bar' ]));
    }

    public function testRebuildArrays()
    {
        $method = new \ReflectionMethod($this->translator, 'rebuildArrays');
        $method->setAccessible(true);

        $tokens     = [ '[', '1', ',', '2', ',', '3', ']' ];
        $translated = [ 'G_OBRACKET', 'T_INTEGER', 'COMMA', 'T_INTEGER', 'COMMA', 'T_INTEGER', 'G_CBRACKET' ];

        $method->invokeArgs($this->translator, [ &$tokens, &$translated ]);

        $this->assertEquals([ '[1,2,3]' ], $tokens);
        $this->assertEquals([ 'T_ARRAY' ], $translated);
    }

    public function testTranslateSentence()
    {
        $method = new \ReflectionMethod($this->translator, 'translateSentence');
        $method->setAccessible(true);

        $this->assertEquals('T_CONNECTOR', $method->invokeArgs($this->translator, [ 'C_AND' ]));
        $this->assertEquals('S_CONDITION', $method->invokeArgs($this->translator, [ 'T_FIELD T_OPERATOR T_LITERAL' ]));
        $this->assertEquals('OQL', $method->invokeArgs($this->translator, [ 'S_CONDITION S_MODIFIER' ]));

        $this->assertFalse($method->invokeArgs($this->translator, [ 'G_OBRACKET T_FIELD' ]));
    }

    public function testTranslateToken()
    {
        $method = new \ReflectionMethod($this->translator, 'translateToken');
        $method->setAccessible(true);

        $this->assertEquals('T_FIELD', $method->invokeArgs($this->translator, [ 'foobar' ]));
        $this->assertEquals('T_STRING', $method->invokeArgs($this->translator, [ '"foobar"' ]));
        $this->assertEquals('G_OBRACKET', $method->invokeArgs($this->translator, [ '[' ]));

        $this->assertFalse($method->invokeArgs($this->translator, [ 'foo*bar' ]));
    }
}
