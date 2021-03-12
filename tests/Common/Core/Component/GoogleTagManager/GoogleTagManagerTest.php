<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Component\GoogleTagManager;

use Common\Core\Component\GoogleTagManager\GoogleTagManager;

/**
 * Defines test cases for GoogleTagManager class.
 */
class GoogleTagManagerTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->id = "GMT-0000000";

        $this->dl = $this->getMockBuilder('Common\Core\Component\DataLayer\Datalayer')
            ->disableOriginalConstructor()
            ->setMethods(['getDataLayerAMPCodeGTM'])
            ->getMock();

        $this->object = new GoogleTagManager($this->dl);
    }
    /**
     * Generates Google Tags Manager head code
     *
     * @return String the generated code
     */
    public function testGetGoogleTagManagerHeadCode()
    {
        $code = "<!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','" . $this->id . "');</script>
            <!-- End Google Tag Manager -->";

        $this->assertEquals($code, $this->object->getGoogleTagManagerHeadCode($this->id));
    }

    /**
     * Generates Google Tags Manager body code
     *
     * @return String the generated code
     */
    public function testGetGoogleTagManagerBodyCode()
    {
        $code = '<!-- Google Tag Manager (noscript) -->
            <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $this->id . '"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->';

        $this->assertEquals($code, $this->object->getGoogleTagManagerBodyCode($this->id));
    }

    /**
     * Generates Google Tags Manager body code for AMP
     *
     * @return String the generated code
     */
    public function testgetGoogleTagManagerBodyCodeAMP()
    {
        $this->dl->expects($this->any())->method('getDataLayerAMPCodeGTM')
            ->willReturn('');

        $code = '<!-- Google Tag Manager AMP -->
            <amp-analytics config="https://www.googletagmanager.com/amp.json?id=' . $this->id
                . '&gtm.url=SOURCE_URL" data-credentials="include"></amp-analytics>
            <!-- End Google Tag Manager AMP -->';

        $this->assertEquals($code, $this->object->getGoogleTagManagerBodyCodeAMP($this->id));
    }
}
