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
 * Defines test cases for SmartyUrl class.
 */
class SmartyRenderAdSlotTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.render_ad_slot.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods(['isInTime'])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getTimeZone' ])
            ->getMock();

        $this->renderer = $this->getMockBuilder('AdvertisementRenderer')
            ->setMethods([ 'render', 'getInlineFormats', 'getAdvertisements', 'getPositions', 'setAdvertisements' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getValue', 'setValue' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->contentHelper->expects($this->any())->method('isInTime')
            ->willReturn(true);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->locale->expects($this->any())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('UTC'));

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mock.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.locale':
                return $this->locale;

            case 'core.helper.content':
                return $this->contentHelper;

            case 'frontend.renderer.advertisement':
                return $this->renderer;

            case 'orm.manager':
                return $this->em;
        }

        return null;
    }

    /**
     * Tests smarty_function_render_ad_slot when type is not in ads_position.
     */
    public function testRenderAdSlotWhenTypeIsNotInAdsPosition()
    {
        $this->ds->expects($this->at(0))->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $this->renderer->expects($this->at(0))->method('getPositions')
            ->willReturn([ 111, 222, 333 ]);

        $this->assertEmpty(
            smarty_function_render_ad_slot([ 'position' => 123 ], $this->smarty)
        );
    }

    /**
     * Tests smarty_function_render_ad_slot when not array.
     */
    public function testRenderAdSlotWhenNotArray()
    {
        $ad            = new \Advertisement();
        $ad->positions = [ 123 ];

        $this->ds->expects($this->at(0))->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $this->renderer->expects($this->at(0))->method('getPositions')
            ->willReturn([ 123 ]);

        $this->renderer->expects($this->at(1))->method('getAdvertisements')
            ->willReturn(null);

        $this->assertEmpty(
            smarty_function_render_ad_slot([ 'position' => 123 ], $this->smarty)
        );
    }

    /**
     * Tests smarty_function_render_ad_slot when no ads.
     */
    public function testRenderAdSlotWhenNoAds()
    {
        $ad            = new \Advertisement();
        $ad->positions = [ 111 ];

        $this->ds->expects($this->at(0))->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $this->renderer->expects($this->at(0))->method('getPositions')
            ->willReturn([ 123 ]);

        $this->renderer->expects($this->at(1))->method('getAdvertisements')
            ->willReturn([ $ad ]);

        $this->assertEmpty(
            smarty_function_render_ad_slot([ 'position' => 123 ], $this->smarty)
        );
    }

    /**
     * Tests smarty_function_render_ad_slot when safeframe is enabled.
     */
    public function testRenderAdSlotWhenSafeFrame()
    {
        $ad                     = new \Advertisement();
        $ad->positions          = [ 123 ];
        $ad->type_advertisement = [ 123 ];
        $ad->starttime          = '2000-01-01 00:00:00';
        $ad->endtime            = null;

        $this->renderer->expects($this->at(0))->method('getPositions')
            ->willReturn([ 123 ]);

        $this->renderer->expects($this->at(1))->method('getAdvertisements')
            ->willReturn([ $ad ]);

        $this->smarty->expects($this->at(3))->method('getValue')
            ->with('ads_format')
            ->willReturn('baz');

        $this->ds->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 1 ]);

        $this->renderer->expects($this->at(2))->method('getInlineFormats')
            ->willReturn([ 'foo', 'gorp', 'wibble' ]);

        $this->assertEquals(
            '<div class="ad-slot oat" data-position="123"></div>',
            smarty_function_render_ad_slot([ 'position' => 123 ], $this->smarty)
        );
    }

    /**
     * Tests smarty_function_render_ad_slot mode consume is enabled.
     */
    public function testRenderAdSlotWhenModeConsume()
    {
        $ad            = new \Advertisement();
        $ad->starttime = '2000-01-01 00:00:00';
        $ad->positions = [ 123 ];

        $params = [
            'position' => 123,
            'mode'     => 'consume',
        ];

        $this->ds->expects($this->at(0))->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $this->renderer->expects($this->at(0))->method('getPositions')
            ->willReturn([ 123 ]);

        $this->renderer->expects($this->at(1))->method('getAdvertisements')
            ->willReturn([ $ad ]);

        $this->renderer->expects($this->at(2))->method('setAdvertisements')
            ->willReturn([]);

        $this->smarty->expects($this->at(4))->method('getValue')
            ->with('app')
            ->willReturn([
                'extension'          => 'foobar',
                'advertisementGroup' => 'gulp'
            ]);

        $this->assertEmpty(
            smarty_function_render_ad_slot($params, $this->smarty)
        );
    }

    /**
     * Tests smarty_function_render_ad_slot when safeframe is not enabled.
     */
    public function testRenderAdSlotWhenNoSafeFrame()
    {
        $ad            = new \Advertisement();
        $ad->positions = [ 123 ];
        $ad->starttime = '2000-01-01 00:00:00';
        $ad->endtime   = null;

        $this->renderer->expects($this->at(0))->method('getPositions')
            ->willReturn([ 123 ]);

        $this->renderer->expects($this->at(1))->method('getAdvertisements')
            ->willReturn([ $ad ]);

        $this->smarty->expects($this->at(4))->method('getValue')
            ->with('app')
            ->willReturn([
                'extension'          => 'foobar',
                'advertisementGroup' => 'gulp'
            ]);

        $this->ds->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $this->renderer->expects($this->at(2))->method('render')
            ->willReturn('Ad output');

        $this->assertEquals(
            'Ad output',
            smarty_function_render_ad_slot([ 'position' => 123 ], $this->smarty)
        );
    }
}
