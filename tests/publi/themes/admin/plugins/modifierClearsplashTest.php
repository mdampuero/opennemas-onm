<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\publi\themes\admin\plugins;

/**
 * Defines test cases for SmartyUrl class.
 */
class ModifierClearsplashTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './public/themes/admin/plugins/modifier.clearslash.php';
    }

    /**
     * Tests smarty_function_url when router throws an exception.
     */
    public function testClearslash()
    {
        $this->assertEquals("Is your name O'reilly?", smarty_modifier_clearslash("Is your name O\'reilly?"));

        $this->assertEquals("Is your name O'reilly?", smarty_modifier_clearslash("Is your name O\\\\'reilly?"));

        $this->assertEquals([
            'en' => "Is your name O'reilly?",
            'es' => "Is your name O'reilly?"
        ], smarty_modifier_clearslash([
            'en' => "Is your name O\'reilly?",
            'es' => "Is your name O\'reilly?"
        ]));
    }
}
