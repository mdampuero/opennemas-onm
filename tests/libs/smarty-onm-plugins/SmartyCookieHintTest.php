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

/**
 * Defines test cases for SmartyCookieHintTest class.
 */
class SmartyCookieHintTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.cookie_hint.php';

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->willReturn($this->em);

        $this->html = "<div id='cookies_overlay' style='display: none;'>"
            . "            <div class='cookies-overlay'>                <p>"
            . "                    <button class='closeover' onclick='acceptCookies()'"
            . " type='button'>&times;</button>                    "
            . "This website uses its own and third party cookies to elaborate "
            . "statistical information and to be able to show you advertising "
            . "related to your preferences through the analysis of your navigation."
            . " <a target=\"_blank\" href=\"%s\">See details &gt; </a> "
            . "               </p>            </div>        </div>        "
            . "<script>            function getCookie(name)"
            . " {                var cookies = document.cookie.split(';');    "
            . "            for (var i = 0; i < cookies.length; i++) {         "
            . "           var cookie = cookies[i].replace(/^\s+/,'').replace(/\s+$/,'');"
            . "                    if (cookie.indexOf(name) == 0) {            "
            . "            return cookie.substring(name.length + 1, cookie.length); "
            . "                   }                }            }            "
            . "function acceptCookies() {                var date = new Date(); "
            . "               date.setTime(date.getTime() + 365*24*60*60*1000); "
            . "               document.cookie = 'cookie_overlay_accepted=1; "
            . "expires=' +                    date.toGMTString() + ' ;path=/'; "
            . "               var overlay = document.getElementById('cookies_overlay');"
            . "                overlay.parentElement.removeChild(overlay);            }"
            . "            (function() {                "
            . "if (getCookie('cookie_overlay_accepted') != 1) {                    "
            . "document.getElementById('cookies_overlay').style.display = 'block';"
            . "                }            })();        </script>";
    }

    /**
     * Tests smarty_function_cookie_hint when no url.
     */
    public function testCookieHintWhenNoUrl()
    {
        $this->ds->expects($this->at(0))
            ->method('get')
            ->with('cookies')
            ->willReturn('default');

        $this->ds->expects($this->at(1))
            ->method('get')
            ->with('cookies_hint_url')
            ->willReturn('');

        $this->assertEquals(
            sprintf($this->html, ''),
            smarty_function_cookie_hint(null, $this->smarty)
        );
    }

    /**
     * Tests smarty_function_cookie_hint when url is set.
     */
    public function testCookieHintWhenUrl()
    {
        $this->ds->expects($this->at(0))
            ->method('get')
            ->with('cookies')
            ->willReturn('default');

        $this->ds->expects($this->at(1))
            ->method('get')
            ->with('cookies_hint_url')
            ->willReturn('http://www.cookie-hint-url.com');

        $this->assertEquals(
            sprintf($this->html, 'http://www.cookie-hint-url.com'),
            smarty_function_cookie_hint(null, $this->smarty)
        );
    }

    /**
     * Tests smarty_function_cookie_hint when it is not activated.
     */
    public function testCookieHintNotActivated()
    {
        $this->ds->expects($this->at(0))
            ->method('get')
            ->with('cookies')
            ->willReturn('none');

        $this->assertEmpty(smarty_function_cookie_hint(null, $this->smarty));
    }
}
