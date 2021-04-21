<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Template;

use Common\Core\Component\Template\Template;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Theme;

class TemplateTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter', 'hasParameter' ])
            ->getMock();

        $this->lm = $this->getMockBuilder('\Onm\LayoutManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'setPath' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'addTextDomain', 'getRequestLocale' ])
            ->getMock();

        $this->ormManager = $this->getMockBuilder('Manager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getSchemeAndHttpHost' ])
            ->getMock();

        $this->rs = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getBaseUrl' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.locale':
                return $this->locale;

            case 'core.template.layout':
                return $this->lm;

            case 'orm.manager':
                return $this->ormManager;

            case 'core.instance':
                return $this->instance;

            case 'request_stack':
                return $this->rs;

            default:
                return null;
        }
    }

    /**
     * Tests addActiveTheme.
     */
    public function testAddActiveTheme()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setMethods([
                'addTheme', 'setTemplateVars', 'setupCompiles', 'setupLayouts',
                'setupPlugins'
            ])->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $theme = new Theme([
            'parameters' => [ 'layouts' => [] ],
            'realpath'   => '/themes/foobar',
            'uuid'       => 'es.openhost.theme.foobar'
        ]);

        $template->expects($this->once())->method('setTemplateVars');
        $template->expects($this->once())->method('setupCompiles')->with($theme);
        $template->expects($this->once())->method('setupPlugins')->with($theme);
        $template->expects($this->once())->method('setupLayouts')->with($theme);
        $template->expects($this->once())->method('addTheme')->with($theme);

        $template->addActiveTheme($theme);
    }

    /**
     * Tests addFilter for valid and invalid sections.
     */
    public function testAddFilter()
    {
        $template        = new Template($this->container, []);
        $filtersProperty = new \ReflectionProperty($template, 'filters');
        $filtersProperty->setAccessible(true);

        $template->addFilter('pre', 'filterone');
        $this->assertEquals(
            ['pre' => [ 'filterone' ]],
            $filtersProperty->getValue($template)
        );

        $template = new Template($this->container, []);
        $template->addFilter('post', 'filtertwo');
        $this->assertEquals(
            ['post' => [ 'filtertwo' ]],
            $filtersProperty->getValue($template)
        );

        $template = new Template($this->container, []);
        $template->addFilter('invalid-section', 'filtertwo');
        $this->assertEquals(
            [ ],
            $filtersProperty->getValue($template)
        );
    }

    /**
     * Tests addInstance.
     */
    public function testAddInstance()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setMethods([ 'setupCache' ])
            ->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $template->expects($this->once())->method('setupCache')->with('fred');

        $template->addInstance('fred');
    }

    /**
     * Tests addTheme when the theme to add is monorepo.
     */
    public function testAddThemeForMonoRepo()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setMethods([ 'addTemplateDir', 'setupCache' ])
            ->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $theme = new Theme([
            'realpath'    => '/glorp/waldo',
            'text_domain' => 'wubble'
        ]);

        $template->expects($this->once())->method('addTemplateDir')
            ->with('/glorp/waldo/tpl');
        $this->locale->expects($this->once())->method('addTextDomain')
            ->with('wubble', '/glorp/waldo/locale');

        $template->addTheme($theme);
    }

    /**
     * Tests addTheme when the theme to add is multirepo.
     */
    public function testAddThemeForMultiRepo()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setMethods([ 'addTemplateDir', 'setupCache' ])
            ->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $theme = new Theme([
            'multirepo'   => true,
            'realpath'    => '/glorp/waldo',
            'text_domain' => 'wubble'
        ]);

        $template->expects($this->at(0))->method('addTemplateDir')
            ->with('/glorp/waldo/src/tpl');
        $template->expects($this->at(1))->method('addTemplateDir')
            ->with('/glorp/waldo/vendor/baseline/src/tpl');
        $this->locale->expects($this->once())->method('addTextDomain')
            ->with('wubble', '/glorp/waldo/locale');

        $template->addTheme($theme);
    }

    /**
     * Tests getCacheId with multiple values.
     */
    public function testGetCacheId()
    {
        $template = new Template($this->container, []);

        $this->locale->expects($this->any())->method('getRequestLocale')
            ->willReturn('en');

        $this->assertEquals('', $template->getCacheId());

        $this->assertEquals(
            'frontend|categoryname|1234234234234|en',
            $template->getCacheId('frontend', 'category-name', '1234234234234')
        );

        $this->assertEquals(
            'frontend|categoryname|en',
            $template->getCacheId('frontend', 'category-name')
        );

        $template->assign('token', 12345);

        $this->assertEquals(
            'frontend|categoryname|en|12345',
            $template->getCacheId('frontend', 'category-name')
        );

        $this->assertEquals(
            'fubar|baz|thud|en|12345',
            $template->getCacheId('fubar', [ 'baz', [ 'thud' ] ])
        );
    }

    /**
     * Tests getContainer.
     */
    public function testGetContainer()
    {
        $template = new Template($this->container, []);

        $this->assertEquals(
            $this->container,
            $template->getContainer()
        );
    }

    /**
     * Tests getImageDir when request there is no request in the stack and when
     * there is a request in the stack.
     */
    public function testGetImageDir()
    {
        $theme = new Theme([
            'uuid' => 'com.openhost.foobar',
            'path' => '/themes/foobar/'
        ]);

        $template = new Template($this->container, []);

        $themeProperty = new \ReflectionProperty($template, 'theme');
        $themeProperty->setAccessible(true);
        $template->theme = $theme;

        $this->rs->expects($this->at(0))->method('getCurrentRequest')
            ->willReturn(null);
        $this->rs->expects($this->at(1))->method('getCurrentRequest')
            ->willReturn($this->request);
        $this->rs->expects($this->at(2))->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->any())->method('getSchemeAndHttpHost')
            ->willReturn('http://quux.glork');

        $this->assertFalse($template->getImageDir());

        $this->assertEquals(
            'http://quux.glork/themes/foobar/images/',
            $template->getImageDir()
        );
    }

    /**
     * Tests getInstance when instance is and instance is not configured.
     */
    public function testGetInstance()
    {
        $template = new Template($this->container, []);
        $instance = new Instance([ 'internal_name' => 'glork' ]);

        $this->assertEmpty($template->getInstance());

        $property = new \ReflectionProperty($template, 'instance');
        $property->setAccessible(true);
        $property->setValue($template, $instance);

        $this->assertEquals($instance, $template->getInstance());
    }

    /**
     * Tests getTheme when the theme is or theme is not configured.
     */
    public function testGetTheme()
    {
        $template = new Template($this->container, []);

        $theme = new Theme([
            'uuid' => 'com.openhost.foobar',
            'path' => '/themes/foobar'
        ]);

        $this->assertEmpty($template->getTheme());

        $property = new \ReflectionProperty($template, 'theme');
        $property->setAccessible(true);
        $property->setValue($template, $theme);

        $this->assertEquals($theme, $template->getTheme());
    }

    /**
     * Tests getThemeSkinName.
     */
    public function testGetThemeSkinName()
    {
        $dataSet = $this->getMockBuilder('DataSet2')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ormManager->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')
            ->will($this->returnValue($dataSet));

        $dataSet->expects($this->any())->method('get')
            ->with('theme_skin', 'default')
            ->will($this->returnValue('default'));

        $template = new Template($this->container, []);

        $themeProperty = new \ReflectionProperty($template, 'theme');
        $themeProperty->setAccessible(true);

        $this->assertEquals('default', $template->getThemeSkinName());
    }

    /**
     * Tests getThemeSkinProperty.
     */
    public function testGetThemeSkinProperty()
    {
        $theme = $this->getMockBuilder('Theme')
            ->setMethods([ 'getData', 'getSkinProperty' ])
            ->getMock();

        $theme->expects($this->any())->method('getSkinProperty')
            ->will($this->returnValue('style.css'));

        // I have to put the dataSet2 weird name due to class alias collision on
        // PHPUnit.
        $dataSet = $this->getMockBuilder('DataSet2')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ormManager->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')
            ->will($this->returnValue($dataSet));

        $dataSet->expects($this->any())->method('get')
            ->with('theme_skin', 'default')
            ->will($this->returnValue('default'));

        $template = new Template($this->container, []);

        $themeProperty = new \ReflectionProperty($template, 'theme');
        $themeProperty->setAccessible(true);
        $template->theme = $theme;

        $this->assertEquals('style.css', $template->getThemeSkinProperty('css_file'));
    }

    /**
     * Tests getValue for multiple values.
     */
    public function testGetValue()
    {
        $template = new Template($this->container, []);

        $template->assign('garply', 'flob');

        $this->assertEmpty($template->getValue('xyzzy'));
        $this->assertEquals('flob', $template->getValue('garply'));
    }

    /**
     * Tests hasValue for multiple values.
     */
    public function testHasValue()
    {
        $template = new Template($this->container, []);

        $template->assign('garply', 'flob');

        $this->assertFalse($template->hasValue('xyzzy'));
        $this->assertTrue($template->hasValue('garply'));
    }

    /**
     * Tests render.
     */
    public function testRender()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'assign', 'fetch' ])
            ->getMock();

        $template->expects($this->once())->method('assign')
            ->with([ 'foobar' => 'plugh' ]);
        $template->expects($this->once())->method('fetch')
            ->with('wobble.tpl', 'flob')
            ->willReturn('<qux></qux>');

        $this->assertEquals('<qux></qux>', $template->render('wobble.tpl', [
            'cache_id' => 'flob',
            'foobar'   => 'plugh'
        ]));
    }

    /**
     * Tests setConfig with cache enabled.
     */
    public function testSetConfigWithCacheEnabled()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([
                'configLoad', 'getConfigVars', 'setCaching', 'setCacheLifetime'
            ])->getMock();

        $template->expects($this->once())->method('configLoad')
            ->willReturn(true);

        $template->expects($this->once())->method('getConfigVars')
            ->willReturn([ 'caching' => true ]);

        $template->expects($this->once())->method('setCaching')
            ->willReturn(true);

        $template->expects($this->once())->method('setCacheLifetime')
            ->with(86400)->willReturn(true);

        $this->assertEquals(null, $template->setConfig('frontpage'));
    }

    /**
     * Tests setConfig with cache disabled.
     */
    public function testSetConfigWithCacheDisabled()
    {
         $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
             ->disableOriginalConstructor()
             ->setMethods([ 'configLoad', 'getConfigVars' ])
             ->getMock();

         $template->expects($this->once())->method('configLoad')
             ->willReturn(true);

         $template->expects($this->once())->method('getConfigVars')
             ->willReturn([]);

         $this->assertEquals(null, $template->setConfig('frontpage'));
    }

    /**
     * Tests setFile when file argument is empty and not empty.
     */
    public function testSetFile()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setMethods([ 'addTheme', 'setTemplateVars', 'setupCompiles', 'setupPlugins' ])
            ->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $response = $template->setFile('qux');
        $property = new \ReflectionProperty($template, 'file');

        $property->setAccessible(true);

        $this->assertEquals($template, $response);
        $this->assertEquals('qux', $property->getValue($template));
    }

    /**
     * Tests setValue.
     */
    public function testSetValue()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setMethods([ 'assign' ])
            ->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $template->expects($this->once())->method('assign')
            ->with('wibble', 14600);

        $template->setValue('wibble', 14600);
    }

    /**
     * Tests registerFilters when filters are empty and not empty.
     */
    public function testRegisterFilters()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setMethods([ 'addFilter' ])
            ->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $method = new \ReflectionMethod($template, 'registerFilters');
        $method->setAccessible(true);

        $template->expects($this->at(0))->method('addFilter')
            ->with('output', 'baz');
        $template->expects($this->at(1))->method('addFilter')
            ->with('output', 'plugh');

        $this->assertEmpty($method->invokeArgs($template, [ null ]));

        $method->invokeArgs($template, [ [
            'ignore_cli' => [ 'xyzzy' ],
            'output'     => [ 'baz', 'xyzzy', 'plugh' ]
        ] ]);
    }

    /**
     * Tests setTemplateVars.
     */
    public function testSetTemplateVars()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setMethods([ 'assign' ])
            ->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $theme = new Theme([
            'uuid' => 'com.openhost.foobar',
            'path' => '/themes/foobar/'
        ]);

        $property = new \ReflectionProperty($template, 'theme');
        $property->setAccessible(true);

        $property->setValue($template, $theme);

        $method = new \ReflectionMethod($template, 'setTemplateVars');
        $method->setAccessible(true);

        $template->expects($this->once())->method('assign')
            ->with([
                'app'       => null,
                '_template' => $template
            ]);

        $method->invokeArgs($template, []);
    }

    /**
     * Tests setupLayouts.
     */
    public function testSetupLayouts()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $method = new \ReflectionMethod($template, 'setupLayouts');
        $method->setAccessible(true);

        $theme = new Theme([
            'realpath'   => '/themes/foobar',
            'uuid'       => 'es.openhost.theme.foobar'
        ]);

        $this->lm->expects($this->once())->method('setPath')
            ->with('/themes/foobar/layouts');

        $method->invokeArgs($template, [ $theme ]);
    }

    /**
     * Tests setupPlugins.
     */
    public function testSetupPlugins()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setMethods([ 'addPluginsDir', 'loadFilter' ])
            ->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $theme = new Theme([
            'uuid'     => 'es.openhost.theme.foobar',
            'path'     => '/themes/foobar/',
            'realpath' => '/wobble/themes/foobar'
        ]);

        $method = new \ReflectionMethod($template, 'setupPlugins');
        $method->setAccessible(true);

        $property = new \ReflectionProperty($template, 'filters');
        $property->setAccessible(true);

        $property->setValue($template, [ 'output' => [ 'xyzzy' ] ]);

        $template->expects($this->at(0))->method('addPluginsDir')
            ->with('/wobble/themes/foobar/plugins');
        $template->expects($this->at(1))->method('addPluginsDir')
            ->with(SITE_LIBS_PATH . '/smarty-onm-plugins/');
        $template->expects($this->once())->method('loadFilter')
            ->with('output', 'xyzzy');

        $method->invokeArgs($template, [ $theme ]);
    }
}
