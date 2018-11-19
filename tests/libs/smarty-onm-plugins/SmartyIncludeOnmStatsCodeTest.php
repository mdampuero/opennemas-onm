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
 * Defines test cases for smarty_function_include_onm_stats_code function.
 */
class SmartyIncludeOnmStatsCodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.include_onm_stats_code.php';
        include_once './libs/smarty-onm-plugins/function.script_tag.php';

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getRequestUri' ])
            ->getMock();

        $this->rs = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getTemplateVars' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->with('request_stack')->willReturn($this->rs);

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);
    }

    /**
     * Tests smarty_function_include_onm_stats_code when the request refers to
     * an external synchronized content.
     */
    public function testIncludeOnmStatsCodeWhenBackend()
    {
        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/admin/foo');

        $this->assertEmpty(smarty_function_include_onm_stats_code([], $this->smarty));
    }

    /**
     * Tests smarty_function_include_onm_stats_code when the request refers to
     * an external synchronized content.
     */
    public function testIncludeOnmStatsCodeWhenExternal()
    {
        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->exactly(3))->method('getRequestUri')
            ->willReturn('/ext/qux');

        $this->assertEmpty(smarty_function_include_onm_stats_code([], $this->smarty));
    }

    /**
     * Tests smarty_function_include_onm_stats_code when there is no request.
     */
    public function testIncludeOnmStatsCodeWhenNoRequest()
    {
        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn(null);

        $this->assertEmpty(smarty_function_include_onm_stats_code([], $this->smarty));
    }

    /**
     * Tests smarty_function_include_onm_stats_code when the request refers to
     * a content preview.
     */
    public function testIncludeOnmStatsCodeWhenPreview()
    {
        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->exactly(2))->method('getRequestUri')
            ->willReturn('/foobar/wobble/preview');

        $this->assertEmpty(smarty_function_include_onm_stats_code([], $this->smarty));
    }

    /**
     * Tests smarty_function_include_onm_stats_code when the request does not
     * refer to a content.
     */
    public function testIncludeOnmStatsCodeWhenNoContent()
    {
        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->exactly(3))->method('getRequestUri')
            ->willReturn('/foobar/wobble');

        $this->smarty->expects($this->any())->method('getTemplateVars')
            ->willReturn([]);

        $this->assertEmpty(smarty_function_include_onm_stats_code([], $this->smarty));
    }

    /**
     * Tests smarty_function_include_onm_stats_code when the request refers to a
     * content.
     */
    public function testIncludeOnmStatsCodeWhenContent()
    {
        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->exactly(3))->method('getRequestUri')
            ->willReturn('/foobar/wobble');

        $this->smarty->expects($this->any())->method('getTemplateVars')
            ->willReturn([ 'contentId' => 1234 ]);

        $this->assertContains(
            "jQuery.onmStats({ content_id: '1234' });",
            smarty_function_include_onm_stats_code([], $this->smarty)
        );
    }

}
