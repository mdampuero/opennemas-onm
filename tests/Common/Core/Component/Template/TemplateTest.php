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

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
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

        return null;
    }

    /**
     * @covers Common\Core\Component\Template\Template::addFilter
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
     * @covers Common\Core\Component\Template\Template::getCacheId
     */
    public function testGetCacheId()
    {
        $template = new Template($this->container, []);

        $this->assertEquals(
            '',
            $template->getCacheId()
        );

        $this->assertEquals(
            'frontend|categoryname|1234234234234',
            $template->getCacheId('frontend', 'category-name', '1234234234234')
        );

        $this->assertEquals(
            'frontend|categoryname',
            $template->getCacheId('frontend', 'category-name')
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
     * @covers Common\Core\Component\Template\Template::getThemeVariantName
     */
    public function testGetThemeVariantName()
    {
        $theme       = $this->getMockBuilder('Theme')
            ->setMethods([ 'getData', 'getCurrentStyle' ])
            ->getMock();
        $theme->uuid = 'com.openhost.foobar';
        $theme->path = 'public/themes/foobar';

        $theme->expects($this->any())->method('getCurrentStyle')
            ->will($this->returnValue([
                'default' => true,
                'name' => 'default',
                'file' => 'style.css'
            ]));

        $dataSet = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ormManager->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')
            ->will($this->returnValue($dataSet));

        $dataSet->expects($this->any())->method('get')
            ->with('theme_style', 'default')
            ->will($this->returnValue('default'));

        $template = new Template($this->container, []);

        $themeProperty = new \ReflectionProperty($template, 'theme');
        $themeProperty->setAccessible(true);
        $template->theme = $theme;

        $this->assertEquals('default', $template->getThemeVariantName());
    }

    /**
     * @covers Common\Core\Component\Template\Template::getThemeVariantFile
     */
    public function testGetThemeVariantFile()
    {
        $theme       = $this->getMockBuilder('Theme')
            ->setMethods([ 'getData', 'getCurrentStyle' ])
            ->getMock();
        $theme->uuid = 'com.openhost.foobar';
        $theme->path = 'public/themes/foobar';

        $theme->expects($this->any())->method('getCurrentStyle')
            ->will($this->returnValue([
                'default' => true,
                'name' => 'default',
                'file' => 'style.css'
            ]));

        $dataSet = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ormManager->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')
            ->will($this->returnValue($dataSet));

        $dataSet->expects($this->any())->method('get')
            ->with('theme_style', 'default')
            ->will($this->returnValue('default'));

        $template = new Template($this->container, []);

        $themeProperty = new \ReflectionProperty($template, 'theme');
        $themeProperty->setAccessible(true);
        $template->theme = $theme;

        $this->assertEquals('style.css', $template->getThemeVariantFile());
    }
}
