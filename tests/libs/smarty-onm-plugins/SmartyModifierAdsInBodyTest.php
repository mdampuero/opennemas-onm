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
class SmartyModifierAdsInBodyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/modifier.ads_in_body.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getValue', 'getContainer' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->globals = $this->getMockBuilder('GlobalVariables')
            ->setMethods([ 'getDevice' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('AdvertisementHelper')
            ->setMethods([ 'isSafeFrameEnabled' ])
            ->getMock();

        $this->renderer = $this->getMockBuilder('AdvertisementRenderer')
            ->setMethods([ 'render', 'getAdvertisements' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);
        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);


        $GLOBALS['kernel'] = $this->kernel;
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.template':
                return $this->smarty;

            case 'core.globals':
                return $this->globals;

            case 'core.helper.advertisement':
                return $this->helper;

            case 'orm.manager':
                return $this->em;

            case 'frontend.renderer.advertisement':
                return $this->renderer;
        }

        return null;
    }

    /**
     * Tests smarty_modifier_ads_in_body
     */
    public function testAdsInBody()
    {
        $ad1            = new \Advertisement();
        $ad1->positions = [ 2201 ];
        $ad1->params    = [
            'devices' => [
                'desktop' => 1,
                'tablet'  => 1,
                'phone'   => 1,
            ]
        ];

        $ad2            = new \Advertisement();
        $ad2->positions = [ 2203 ];
        $ad2->params    = [
            'devices' => [
                'desktop' => 1,
                'tablet'  => 0,
                'phone'   => 0,
            ]
        ];

        $this->renderer->expects($this->at(0))->method('getAdvertisements')
            ->willReturn([ $ad1, $ad2 ]);

        $this->smarty->expects($this->at(2))->method('getValue')
            ->with('app')
            ->willReturn([
                'advertisementGroup' => 'waldo',
                'extension'          => 'plugh',
                'section'            => 'bar',
            ]);

        $this->globals->expects($this->any())->method('getDevice')
            ->willReturn('desktop');

        $this->helper->expects($this->at(0))->method('isSafeFrameEnabled')
            ->willReturn(false);

        $this->renderer->expects($this->at(1))->method('render')
            ->willReturn('<ad>');
        $this->renderer->expects($this->at(2))->method('render')
            ->willReturn('<ad>');

        $body        = '<p>foo bar baz</p><p>thud qwer asdf</p>';
        $bodyWithAds = '<p>foo bar baz</p><ad><p>thud qwer asdf</p><ad>';
        $this->assertEquals(
            $bodyWithAds,
            smarty_modifier_ads_in_body($body)
        );
    }

    /**
     * Tests smarty_modifier_ads_in_body when no ads
     */
    public function testAdsInBodyWhenEmpty()
    {
        $this->renderer->expects($this->at(0))->method('getAdvertisements')
            ->willReturn(null);

        $body = '<p>foo bar baz</p><p>thud qwer asdf</p>';
        $this->assertEquals(
            $body,
            smarty_modifier_ads_in_body($body)
        );
    }

    /**
     * Tests smarty_modifier_ads_in_body when no device
     */
    public function testAdsInBodyWhenNoDevice()
    {
        $ad1            = new \Advertisement();
        $ad1->positions = [ 2201 ];
        $ad1->params    = [
            'devices' => [
                'desktop' => 1,
                'tablet'  => 0,
                'phone'   => 0,
            ]
        ];

        $ad2            = new \Advertisement();
        $ad2->positions = [ 2203 ];
        $ad2->params    = [
            'devices' => [
                'desktop' => 1,
                'tablet'  => 0,
                'phone'   => 0,
            ]
        ];

        $this->renderer->expects($this->at(0))->method('getAdvertisements')
            ->willReturn([ $ad1, $ad2 ]);

        $this->smarty->expects($this->at(2))->method('getValue')
            ->with('app')
            ->willReturn([
                'advertisementGroup' => 'waldo',
                'extension'          => 'plugh',
                'section'            => 'bar',
            ]);

        $this->globals->expects($this->any())->method('getDevice')
            ->willReturn('phone');

        $this->helper->expects($this->at(0))->method('isSafeFrameEnabled')
            ->willReturn(false);

        $body = '<p>foo bar baz</p><p>thud qwer asdf</p>';
        $this->assertEquals(
            $body,
            smarty_modifier_ads_in_body($body)
        );
    }
}
