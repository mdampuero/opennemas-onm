<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Defines test cases for SmartyUrl class.
 */
class SmartyModifierTranslateDateWithFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/modifier.translate_date_with_format.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'getLocale' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->container->expects($this->any())->method('get')
            ->willReturn($this->locale);
    }

    /**
     * Tests smarty_modifier_translate_date_with_format when date is not a
     * string.
     */
    public function testTranslateDateWithFormatNoString()
    {
        $this->assertEmpty(smarty_modifier_translate_date_with_format(123));
        $this->assertEmpty(smarty_modifier_translate_date_with_format([]));
        $this->assertEmpty(smarty_modifier_translate_date_with_format(new \StdClass()));
    }

    /**
     * Tests smarty_modifier_translate_date_with_format when date is null
     */
    public function testTranslateDateWithFormatNull()
    {
        $this->assertEquals(
            null,
            smarty_modifier_translate_date_with_format(new \DateTime())
        );
    }

    /**
     * Tests smarty_modifier_translate_date_with_format with default format and
     * for English language.
     */
    public function testTranslateDateWithFormatEnglish()
    {
        $this->locale->expects($this->any())->method('getLocale')
            ->willReturn('en_US');

        $this->assertEquals(
            'Monday, 11 December',
            smarty_modifier_translate_date_with_format('2017-12-11 00:00:10')
        );
    }

    /**
     * Tests smarty_modifier_translate_date_with_format with default format and
     * for Galician language.
     */
    public function testTranslateDateWithFormatGalician()
    {
        $this->locale->expects($this->any())->method('getLocale')
            ->willReturn('gl_ES');

        $this->assertEquals(
            'Luns, 11 de Decembro',
            smarty_modifier_translate_date_with_format('2017-12-11 00:00:10')
        );
    }

    /**
     * Tests smarty_modifier_translate_date_with_format with default format and
     * for Spanish language.
     */
    public function testTranslateDateWithFormatSpanish()
    {
        $this->locale->expects($this->any())->method('getLocale')
            ->willReturn('es_ES');

        $this->assertEquals(
            'Lunes, 11 de Diciembre',
            smarty_modifier_translate_date_with_format('2017-12-11 00:00:10')
        );
    }
}
