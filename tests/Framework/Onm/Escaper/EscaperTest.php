<?php
namespace Tests\Framework\Onm\Escaper;

use \Onm\Escaper\Escaper;

class EscaperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Onm\Escaper\Escaper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Escaper;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Onm\Escaper\Escaper::getEncoding
     */
    public function testGetEncoding()
    {
        $this->assertEquals('utf-8', $this->object->getEncoding());
    }

    /**
     * @covers \Onm\Escaper\Escaper::escapeHtml
     */
    public function testEscapeHtml()
    {
        $this->assertEquals(
            'Thank you, danke, gracias, merci, Σας ευχαριστούμε, gracie, ありがとう, dank u, obrigado, вы, 谢谢, 감사하십시오…',
            $this->object->escapeHtml('Thank you, danke, gracias, merci, Σας ευχαριστούμε, gracie, ありがとう, dank u, obrigado, вы, 谢谢, 감사하십시오…')
        );
    }

    /**
     * @covers \Onm\Escaper\Escaper::escapeHtmlAttr
     */
    public function testEscapeHtmlAttr()
    {
        $this->assertEquals(
            'Thank&#x20;you,&#x20;danke,&#x20;gracias,&#x20;merci,&#x20;&#x03A3;&#x03B1;&#x03C2;&#x20;&#x03B5;&#x03C5;&#x03C7;&#x03B1;&#x03C1;&#x03B9;&#x03C3;&#x03C4;&#x03BF;&#x03CD;&#x03BC;&#x03B5;,&#x20;gracie,&#x20;&#x3042;&#x308A;&#x304C;&#x3068;&#x3046;,&#x20;dank&#x20;u,&#x20;obrigado,&#x20;&#x0432;&#x044B;,&#x20;&#x8C22;&#x8C22;,&#x20;&#xAC10;&#xC0AC;&#xD558;&#xC2ED;&#xC2DC;&#xC624;&#x2026;',
            $this->object->escapeHtmlAttr('Thank you, danke, gracias, merci, Σας ευχαριστούμε, gracie, ありがとう, dank u, obrigado, вы, 谢谢, 감사하십시오…')
        );
    }

    /**
     * @covers \Onm\Escaper\Escaper::escapeJs
     */
    public function testEscapeJs()
    {
        $this->assertEquals(
            'Thank\x20you,\x20danke,\x20gracias,\x20merci',
            $this->object->escapeJs('Thank you, danke, gracias, merci')
        );
    }

    /**
     * @covers \Onm\Escaper\Escaper::escapeUrl
     */
    public function testEscapeUrl()
    {
        $this->assertEquals(
            'Thank%20you%2C%20danke%2C%20gracias%2C%20merci',
            $this->object->escapeUrl('Thank you, danke, gracias, merci')
        );
    }

    /**
     * @covers \Onm\Escaper\Escaper::escapeCss
     *
     * @todo   Implement testEscapeCss().
     */
    public function testEscapeCss()
    {
        $this->assertEquals(
            'Thank\20 you\2C \20 danke\2C \20 gracias\2C \20 merci',
            $this->object->escapeCss('Thank you, danke, gracias, merci')
        );
    }
}
