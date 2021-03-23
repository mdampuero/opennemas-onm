<?php

namespace Test\Common\Core\Component\Google;

use Common\Core\Component\Google\TagManager;

/**
 * Defines test cases for GoogleTagManager class.
 */
class TagManagerTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->id = "GMT-0000000";

        $this->dl = $this->getMockBuilder('Common\Core\Component\DataLayer\Datalayer')
            ->disableOriginalConstructor()
            ->setMethods(['getDataLayerArray'])
            ->getMock();

        $this->object = new TagManager($this->dl);
    }

    /**
     * Tests getGoogleTagManagerHeadCode.
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
     * Tests getGoogleTagManagerBodyCode.
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
     * Tests getGoogleTagManagerBodyCodeAMP.
     */
    public function testGetGoogleTagManagerBodyCodeAMP()
    {
        $this->dl->expects($this->any())->method('getDataLayerArray')
            ->willReturn(['foo' => 'bar', 'waldo' => 'wobble']);

        $code = '<!-- Google Tag Manager AMP -->
            <amp-analytics config="https://www.googletagmanager.com/amp.json?id=' . $this->id
                . '&gtm.url=SOURCE_URL" data-credentials="include"><script type="application/json">
                { "vars" : {"foo":"bar","waldo":"wobble"} }
            </script></amp-analytics>
            <!-- End Google Tag Manager AMP -->';

        $this->assertEquals($code, $this->object->getGoogleTagManagerBodyCodeAMP($this->id));
    }

    /**
     * Tests getGoogleTagManagerBodyCodeAMP when no data layer
     */
    public function testGetGoogleTagManagerBodyCodeAMPNoDataLayer()
    {
        $this->dl->expects($this->any())->method('getDataLayerArray')
            ->willReturn('');

        $code = '<!-- Google Tag Manager AMP -->
            <amp-analytics config="https://www.googletagmanager.com/amp.json?id=' . $this->id
                . '&gtm.url=SOURCE_URL" data-credentials="include"></amp-analytics>
            <!-- End Google Tag Manager AMP -->';

        $this->assertEquals($code, $this->object->getGoogleTagManagerBodyCodeAMP($this->id));
    }
}
