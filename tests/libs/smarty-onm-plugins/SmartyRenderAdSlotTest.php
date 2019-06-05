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

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->renderer = $this->getMockBuilder('AdvertisementRenderer')
            ->setMethods([ 'getDeviceCssClasses', 'renderInline', 'getMark' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getValue', 'getTemplateVars' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);
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
            case 'core.renderer.advertisement':
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
        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('ads_positions')
            ->willReturn([ 111, 222, 333 ]);

        $this->assertEmpty(
            smarty_function_render_ad_slot([ 'position' => 123 ], $this->smarty)
        );
    }

    /**
     * Tests smarty_function_render_ad_slot when safeframe is enabled.
     */
    public function testRenderAdSlotWhenSafeFrameInSettings()
    {
        $ad                     = new \Advertisement();
        $ad->positions          = [ 123 ];
        $ad->type_advertisement = [ 123 ];
        $ad->starttime          = '2000-01-01 00:00:00';
        $ad->endtime            = null;
        $ad->params             = [ 'orientation' => 'left' ];

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('ads_positions')
            ->willReturn(123);

        $this->smarty->expects($this->at(2))->method('getValue')
            ->with('render_params')
            ->willReturn([ 'ads-format' => 'safeframe' ]);

        $this->smarty->expects($this->any())->method('getTemplateVars')
            ->with('advertisements')
            ->willReturn([ $ad ]);

        $this->ds->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 1 ]);

        $this->assertEquals(
            '<div class="ad-slot oat" data-position="123"></div>',
            smarty_function_render_ad_slot([ 'position' => 123 ], $this->smarty)
        );
    }

    /**
     * Tests smarty_function_render_ad_slot when safeframe is disabled and no
     * advertisements in list.
     */
    public function testRenderAdSlotWhenInlineAndEmpty()
    {
        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('ads_positions')
            ->willReturn(123);

        $this->smarty->expects($this->at(2))->method('getValue')
            ->with('render_params')
            ->willReturn([ 'ads-format' => 'inline' ]);

        $this->smarty->expects($this->at(3))->method('getTemplateVars')
            ->with('advertisements')
            ->willReturn(null);

        $this->ds->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $this->assertEmpty(smarty_function_render_ad_slot([ 'position' => 123 ], $this->smarty));
    }

    /**
     * Tests smarty_function_render_ad_slot when safeframe is disabled and no
     * enabled advertisements in list.
     */
    public function testRenderAdSlotWhenInlineAndNoEnabledAdvertisement()
    {
        $ad = new \Advertisement();

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('ads_positions')
            ->willReturn(123);

        $this->smarty->expects($this->at(2))->method('getValue')
            ->with('render_params')
            ->willReturn([ 'ads-format' => 'inline' ]);

        $this->smarty->expects($this->at(3))->method('getTemplateVars')
            ->with('advertisements')
            ->willReturn([ $ad ]);

        $this->ds->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $this->assertEmpty(smarty_function_render_ad_slot([ 'position' => 123 ], $this->smarty));
    }

    /**
     * Tests smarty_function_render_ad_slot when safeframe is enabled but inline is
     * forced in template and enabled advertisements in list.
     */
    public function testRenderBannerWhenInlineForced()
    {
        $ad                     = new \Advertisement();
        $ad->positions          = [ 123 ];
        $ad->type_advertisement = [ 123 ];
        $ad->starttime          = '2000-01-01 00:00:00';
        $ad->endtime            = null;
        $ad->params             = [ 'orientation' => 'left' ];

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('ads_positions')
            ->willReturn(123);

        $this->smarty->expects($this->at(2))->method('getValue')
            ->with('render_params')
            ->willReturn([ 'ads-format' => 'inline' ]);

        $this->smarty->expects($this->at(3))->method('getTemplateVars')
            ->with('advertisements')
            ->willReturn([ $ad ]);

        $this->smarty->expects($this->at(4))->method('getValue')
            ->with('app')
            ->willReturn([
                'extension' => 'foo',
                'advertisementGroup' => 'bar'
            ]);

        $this->smarty->expects($this->at(5))->method('getValue')
            ->with('actual_category')
            ->willReturn([ 'baz' ]);

        $this->smarty->expects($this->at(6))->method('getValue')
            ->with('content')
            ->willReturn(new \StdClass());

        $this->renderer->expects($this->once())->method('renderInline')
            ->with($ad)->willReturn('foo garply');
        $this->renderer->expects($this->once())->method('getDeviceCSSClasses')
            ->with($ad)->willReturn('corge');
        $this->renderer->expects($this->once())->method('getMarK')
            ->with($ad)->willReturn('Advertisement');
        $this->ds->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 1 ]);

        $this->assertEquals(
            '<div class="ad-slot oat oat-visible oat-left corge" data-mark="Advertisement">foo garply</div>',
            smarty_function_render_ad_slot(
                [ 'format' => 'inline', 'position' => 123, 'mode' => 'consume' ],
                $this->smarty
            )
        );
    }

    /**
     * Tests smarty_function_render_ad_slot when safeframe is enabled but inline is
     * forced in template and enabled advertisements in list.
     */
    public function testRenderAdSlotWhenInlineForcedWithCustomMark()
    {
        $ad                     = new \Advertisement();
        $ad->positions          = [ 123 ];
        $ad->type_advertisement = [ 123 ];
        $ad->starttime          = '2000-01-01 00:00:00';
        $ad->endtime            = null;
        $ad->params             = [ 'orientation' => 'left' ];

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('ads_positions')
            ->willReturn(123);

        $this->smarty->expects($this->at(2))->method('getValue')
            ->with('render_params')
            ->willReturn([ 'ads-format' => 'inline' ]);

        $this->smarty->expects($this->at(3))->method('getTemplateVars')
            ->with('advertisements')
            ->willReturn([ $ad ]);

        $this->smarty->expects($this->at(4))->method('getValue')
            ->with('app')
            ->willReturn([
                'extension' => 'foo',
                'advertisementGroup' => 'bar'
            ]);

        $this->smarty->expects($this->at(5))->method('getValue')
            ->with('actual_category')
            ->willReturn([ 'baz' ]);

        $this->smarty->expects($this->at(6))->method('getValue')
            ->with('content')
            ->willReturn(new \StdClass());

        $this->renderer->expects($this->once())->method('renderInline')
            ->with($ad)->willReturn('foo garply');
        $this->renderer->expects($this->once())->method('getDeviceCSSClasses')
            ->with($ad)->willReturn('corge');
        $this->renderer->expects($this->once())->method('getMarK')
            ->with($ad)->willReturn('Sponsor');
        $this->ds->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 1 ]);

        $this->assertEquals(
            '<div class="ad-slot oat oat-visible oat-left corge" data-mark="Sponsor">foo garply</div>',
            smarty_function_render_ad_slot([ 'format' => 'inline', 'position' => 123 ], $this->smarty)
        );
    }
}
