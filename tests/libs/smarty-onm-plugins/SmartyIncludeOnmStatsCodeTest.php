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

use Libs\Smarty\SmartyIncludeOnmStatsCode;

/**
 * Defines test cases for SmartyIncludeOnmStatsCode class.
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
     * Tests smarty_function_include_onm_stats_code when there is a content id
     * assigned to the template manager.
     */
    public function testSmartyIncludeOnmStatsCodeWhenContent()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/corge/glorp');

        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->smarty->expects($this->any())->method('getTemplateVars')
            ->willReturn([ 'contentId' => 123 ]);

        $code = smarty_function_include_onm_stats_code([], $this->smarty);

        $this->assertContains("content_id: '123'", $code);
    }


    /**
     * Tests smarty_function_include_onm_stats_code when the current request if
     * for a backend resource.
     */
    public function testSmartyIncludeOnmStatsCodeWhenBackendUrl()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/admin/garply');

        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->assertEmpty(smarty_function_include_onm_stats_code([], $this->smarty));
    }

    /**
     * Tests smarty_function_include_onm_stats_code when there is no content id
     * assigned to the template manager.
     */
    public function testSmartyIncludeOnmStatsCodeWhenNoContent()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/corge/glorp');

        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->smarty->expects($this->once())->method('getTemplateVars')
            ->willReturn([]);

        $this->assertEmpty(smarty_function_include_onm_stats_code([], $this->smarty));
    }

    /**
     * Tests smarty_function_include_onm_stats_code when there are no request.
     */
    public function testSmartyIncludeOnmStatsCodeWhenNoRequest()
    {
        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn(null);

        $this->assertEmpty(smarty_function_include_onm_stats_code([], $this->smarty));
    }
}
