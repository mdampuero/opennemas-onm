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

class TemplateTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'getRequestLocale' ])
            ->getMock();

        $this->ormManager = $this->getMockBuilder('Manager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
    }

    public function serviceContainerCallback($name)
    {
        if ($name === 'orm.manager') {
            return $this->ormManager;
        }

        if ($name === 'core.locale') {
            return $this->locale;
        }

        return null;
    }

    /**
     * Tests addActiveTheme.
     */
    public function testAddActiveTheme()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->setMethods([ 'addTheme', 'setTemplateVars', 'setupCompiles', 'setupPlugins' ])
            ->setConstructorArgs([ $this->container, [] ])
            ->getMock();

        $template->expects($this->once())->method('setTemplateVars')->with('wubble');
        $template->expects($this->once())->method('setupCompiles')->with('wubble');
        $template->expects($this->once())->method('setupPlugins')->with('wubble');
        $template->expects($this->once())->method('addTheme')->with('wubble');

        $template->addActiveTheme('wubble');
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
    }

    /**
     * @covers Common\Core\Component\Template\Template::setConfig
     */
    public function testSetConfig()
    {
        $template = $this->createMock(\Common\Core\Component\Template\Template::class);

        $template->method('configLoad')
            ->willReturn(true);

        $template->method('getConfigVars')
            ->willReturn([]);


        $this->assertEquals(
            null,
            $template->setConfig('frontpage')
        );
    }

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
     * @covers Common\Core\Component\Template\Template::generateCacheId
     */
    public function testGenerateCacheId()
    {
        $template = new Template($this->container, []);

        $this->assertEquals(
            'home|',
            $template->generateCacheId('')
        );

        $this->assertEquals(
            'categoryname|1234234234234',
            $template->generateCacheId('frontend', 'category-name', '1234234234234')
        );

        $this->assertEquals(
            'categoryname|',
            $template->generateCacheId('frontend', 'category-name')
        );

        $this->assertEquals(
            'frontend|',
            $template->generateCacheId('frontend', '')
        );
    }

    /**
     * @covers Common\Core\Component\Template\Template::getContainer
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
     * @covers Common\Core\Component\Template\Template::getInstance
     */
    public function testGetInstanceWithNoInstance()
    {
        $template = new Template($this->container, []);

        $this->assertEquals(null, $template->getInstance());
    }

    /**
     * @covers Common\Core\Component\Template\Template::getTheme
     */
    public function testGetThemeWithNoTheme()
    {
        $template = new Template($this->container, []);

        $this->assertEquals(null, $template->getTheme());
    }

    /**
     * @covers Common\Core\Component\Template\Template::getTheme
     */
    public function testGetThemeWithTheme()
    {
        $theme       = $this->getMockBuilder('Theme')
            ->setMethods([ 'getData' ])
            ->getMock();
        $theme->uuid = 'com.openhost.foobar';
        $theme->path = 'public/themes/foobar';

        $template = new Template($this->container, []);

        $themeProperty = new \ReflectionProperty($template, 'theme');
        $themeProperty->setAccessible(true);
        $template->theme = $theme;

        $this->assertEquals($theme, $template->getTheme());
    }

    /**
     * @covers Common\Core\Component\Template\Template::getThemeSkinName
     */
    public function testGetThemeSkinName()
    {
        $theme       = $this->getMockBuilder('Theme')
            ->setMethods([ 'getData', 'getCurrentSkinName' ])
            ->getMock();
        $theme->uuid = 'com.openhost.foobar';
        $theme->path = 'public/themes/foobar';

        $theme->expects($this->any())->method('getCurrentSkinName')
            ->will($this->returnValue('default'));

        $dataSet2 = $this->getMockBuilder('DataSet2')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ormManager->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')
            ->will($this->returnValue($dataSet2));

        $dataSet2->expects($this->any())->method('get')
            ->with('theme_skin', 'default')
            ->will($this->returnValue('default'));

        $template = new Template($this->container, []);

        $themeProperty = new \ReflectionProperty($template, 'theme');
        $themeProperty->setAccessible(true);
        $template->theme = $theme;

        $this->assertEquals('default', $template->getThemeSkinName());
    }

    /**
     * @covers Common\Core\Component\Template\Template::getThemeSkinProperty
     */
    public function testGetThemeVariantProperty()
    {
        $theme       = $this->getMockBuilder('Theme')
            ->setMethods([ 'getData', 'getCurrentSkinProperty' ])
            ->getMock();
        $theme->uuid = 'com.openhost.foobar';
        $theme->path = 'public/themes/foobar';

        $theme->expects($this->any())->method('getCurrentSkinProperty')
            ->will($this->returnValue('style.css'));

        // I have to put the dataSet2 weird name due to class alias collision on
        // PHPUnit.
        $dataSet2 = $this->getMockBuilder('DataSet2')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ormManager->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')
            ->will($this->returnValue($dataSet2));

        $dataSet2->expects($this->any())->method('get')
            ->with('theme_skin', 'default')
            ->will($this->returnValue('default'));

        $template = new Template($this->container, []);

        $themeProperty = new \ReflectionProperty($template, 'theme');
        $themeProperty->setAccessible(true);
        $template->theme = $theme;

        $this->assertEquals('style.css', $template->getThemeSkinProperty('css_file'));
    }
}
