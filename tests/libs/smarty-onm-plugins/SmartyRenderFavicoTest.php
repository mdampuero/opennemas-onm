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
 * Defines test cases for SmartyRenderFavico class.
 */
class SmartyRenderFavicoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.render_favico.php';

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->globals = $this->getMockBuilder('GlobalVariables')
            ->setMethods([ 'getInstance' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getMediaShortPath' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->ph = $this->getMockBuilder('PhotoHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPhotoPath' ])
            ->getMock();

        $this->sh = $this->getMockBuilder('SettingHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getLogo', 'hasLogo' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->container->expects($this->any())
            ->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->globals->expects($this->any())
            ->method('getInstance')
            ->willReturn($this->instance);
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.globals':
                return $this->globals;

            case 'orm.manager':
                return $this->em;

            case 'core.helper.photo':
                return $this->ph;

            case 'core.helper.setting':
                return $this->sh;
        }

        return null;
    }

    /**
     * Tests smarty_function_render_favico.
     */
    public function testRenderFavico()
    {
        $link = '/foo/bar/sections/foobar.png';

        $this->sh->expects($this->once())->method('hasLogo')
            ->willReturn(true);

        $this->ph->expects($this->once())->method('getPhotoPath')
            ->willReturn($link);

        $output = "<link rel='icon' type='image/png' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='apple-touch-icon' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='apple-touch-icon' sizes='57x57' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='apple-touch-icon' sizes='60x60' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='apple-touch-icon' sizes='72x72' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='apple-touch-icon' sizes='76x76' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='apple-touch-icon' sizes='114x114' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='apple-touch-icon' sizes='120x120' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='apple-touch-icon' sizes='144x144' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='apple-touch-icon' sizes='152x152' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='apple-touch-icon' sizes='180x180' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='icon' type='image/png' sizes='192x192' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='icon' type='image/png' sizes='96x96' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='icon' type='image/png' sizes='32x32' href='/foo/bar/sections/foobar.png'>\n"
            . "\t<link rel='icon' type='image/png' sizes='16x16' href='/foo/bar/sections/foobar.png'>\n";

        $this->assertEquals(
            $output,
            smarty_function_render_favico(null, $this->smarty)
        );
    }
}
