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
class SmartyRenderBannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/insert.renderbanner.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->renderer = $this->getMockBuilder('AdvertisementRenderer')
            ->setMethods([ 'getDeviceCssClasses', 'renderInline' ])
            ->getMock();

        $this->sm = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
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
        if ($name === 'setting_repository') {
            return $this->sm;
        }

        if ($name === 'core.renderer.advertisement') {
            return $this->renderer;
        }

        return null;
    }

    /**
     * Tests smarty_insert_renderbanner when safeframe is enabled.
     */
    public function testRenderBannerWhenSafeFrameInSettings()
    {
        $this->sm->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 1 ]);

        $this->assertEquals(
            '<div class="ad-slot oat" data-type="123"></div>',
            smarty_insert_renderbanner([ 'type' => 123 ], $this->smarty)
        );
    }

    /**
     * Tests smarty_insert_renderbanner when safeframe is disabled and no
     * advertisements in list.
     */
    public function testRenderBannerWhenInlineAndEmpty()
    {
        $params = new \StdClass();
        $ads    = new \StdClass();

        $params->value = [ 'ads-format' => 'safeframe' ];
        $ads->value    = null;

        $this->smarty->tpl_vars = [
            'render_params'  => $params,
            'advertisements' => $ads
        ];

        $this->sm->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $this->assertEmpty(smarty_insert_renderbanner([ 'type' => 123 ], $this->smarty));
    }

    /**
     * Tests smarty_insert_renderbanner when safeframe is disabled and no
     * enabled advertisements in list.
     */
    public function testRenderBannerWhenInlineAndNoEnabledAdvertisement()
    {
        $params = new \StdClass();
        $ads    = new \StdClass();
        $ad     = new \Advertisement();

        $params->value = [ 'ads-format' => 'safeframe' ];
        $ads->value    = [ $ad ];

        $this->smarty->tpl_vars = [
            'render_params'  => $params,
            'advertisements' => $ads
        ];

        $this->sm->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $this->assertEmpty(smarty_insert_renderbanner([ 'type' => 123 ], $this->smarty));
    }

    /**
     * Tests smarty_insert_renderbanner when safeframe is enabled but inline is
     * forced in template and enabled advertisements in list.
     */
    public function testRenaderBannerWhenInlineForced()
    {
        $params = new \StdClass();
        $ads    = new \StdClass();
        $ad     = new \Advertisement();

        $ad->type_advertisement = 123;
        $ad->starttime          = '2000-01-01 00:00:00';
        $ad->endtime            = null;
        $ad->params             = [ 'orientation' => 'left' ];

        $params->value = [];
        $ads->value    = [ $ad ];

        $this->smarty->tpl_vars = [
            'render_params'  => $params,
            'advertisements' => $ads
        ];

        $this->renderer->expects($this->once())->method('renderInline')
            ->with($ad)->willReturn('foo garply');
        $this->renderer->expects($this->once())->method('getDeviceCSSClasses')
            ->with($ad)->willReturn('corge');
        $this->sm->expects($this->once())->method('get')->with('ads_settings')
            ->willReturn([ 'safe_frame' => 1 ]);

        $this->assertEquals(
            '<div class="ad-slot oat oat-visible oat-left corge">foo garply</div>',
            smarty_insert_renderbanner([ 'format' => 'inline', 'type' => 123 ], $this->smarty)
        );
    }
}
