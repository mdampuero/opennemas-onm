<?php

namespace Tests\Api\Service\V1;

use Api\Service\V1\WidgetService;
use Opennemas\Orm\Core\Entity;

/**
 * Defines test cases for the WidgetService class.
 */
class WidgetServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->widgetService = $this->getMockBuilder('Api\Service\V1\WidgetService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getList' ])
            ->getMock();

        $this->oqlFixer = $this->getMockBuilder('Opennemas\Orm\Core\Oql\Fixer')
            ->disableOriginalConstructor()
            ->setMethods([ 'fix', 'addCondition', 'getOql' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->service = new WidgetService($this->container, 'Common\Model\Entity\Content');

        $this->method = new \ReflectionMethod($this->service, 'getOqlForList');
        $this->method->setAccessible(true);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.widget':
                return $this->widgetService;

            case 'orm.oql.fixer':
                return $this->oqlFixer;
        }

        return null;
    }

    /**
     * Tests getOqlForList when there are no matches.
     */
    public function testGetOqlForListWhenEmptyMatches()
    {
        $this->assertEquals(
            'glorp = 1',
            $this->method->invokeArgs($this->service, [ 'glorp = 1' ])
        );
    }

    /**
     * Tests getOqlForList when there are no intelligentWidgets.
     */
    public function testGetOqlForListWhenSearchTypeHtmlNotIntelligent()
    {
        $this->widgetService->expects($this->once())->method('getList')
            ->with('widget_type = "intelligentwidget"')
            ->willReturn([ 'items' => [] ]);

        $this->assertEquals(
            'glorp = 1 ',
            $this->method->invokeArgs($this->service, [ 'glorp = 1 and widget_type="html"' ])
        );
    }

    /**
     * Tests getOqlForList when the oql is fixed correctly.
     */
    public function testGetOqlForListWhenFixed()
    {
        $this->widgetService->expects($this->once())->method('getList')
            ->with('widget_type = "intelligentwidget"')
            ->willReturn([ 'items' => [ new Entity([ 'id' => 1 ]) ]]);

        $this->oqlFixer->expects($this->once())->method('fix')
            ->with('glorp = 1 ')
            ->willReturn($this->oqlFixer);

        $this->oqlFixer->expects($this->once())->method('addCondition')
            ->with('pk_content !in [1]')
            ->willReturn($this->oqlFixer);

        $this->oqlFixer->expects($this->once())->method('getOql')
            ->willReturn('glorp = 1 and pk_content !in [1]');

        $this->assertEquals(
            'glorp = 1 and pk_content !in [1]',
            $this->method->invokeArgs($this->service, [ 'glorp = 1 and widget_type="html"' ])
        );
    }
}
