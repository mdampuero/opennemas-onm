<?php

namespace Test\Common\Core\Component\Google;

use Common\Core\Component\Google\TagManager;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for GoogleTagManager class.
 */
class TagManagerTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->id = "GMT-0000000";

        $this->instance = new Instance([
            'activated_modules' => [],
            'domains'           => [ 'grault.opennemas.com', 'grault.com' ],
            'internal_name'     => 'grault',
            'main_domain'       => 1
        ]);

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dl = $this->getMockBuilder('Common\Core\Component\DataLayer\Datalayer')
            ->disableOriginalConstructor()
            ->setMethods(['getDataLayer'])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->object = new TagManager($this->container);
    }

    /**
     * Callback function to return custom service based on the name.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;

            case 'core.service.data_layer':
                return $this->dl;
        }

        return null;
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
        $this->dl->expects($this->any())->method('getDataLayer')
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
        $this->dl->expects($this->any())->method('getDataLayer')
            ->willReturn('');

        $code = '<!-- Google Tag Manager AMP -->
            <amp-analytics config="https://www.googletagmanager.com/amp.json?id=' . $this->id
                . '&gtm.url=SOURCE_URL" data-credentials="include"></amp-analytics>
            <!-- End Google Tag Manager AMP -->';

        $this->assertEquals($code, $this->object->getGoogleTagManagerBodyCodeAMP($this->id));
    }
}
