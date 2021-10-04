<?php

namespace Tests\Frontend\Renderer;

use PHPUnit\Framework\TestCase;
use Frontend\Renderer\Statistics\PrometeoRenderer;
use Common\Model\Entity\Content;

/**
 * Defines test cases for PrometeoRenderer class.
 */
class PrometeoRendererTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->ds = $this->getMockForAbstractClass('Opennemas\Orm\Core\DataSet');

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->dl = $this->getMockBuilder('Common\Core\Component\DataLayer\Datalayer')
            ->disableOriginalConstructor()
            ->setMethods(['customizeExtension'])
            ->getMock();

        $this->extractor = $this->getMockBuilder('Common\Core\Component\Core\VariablesExtractor')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->global = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContainer', 'getSection' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->ds);

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->renderer = new PrometeoRenderer($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'orm.manager':
                return $this->em;
            case 'core.globals':
                return $this->global;
            case 'core.service.data_layer':
                return $this->dl;
            case 'core.variables.extractor':
                return $this->extractor;
        }

        return null;
    }

    /**
     * Tests getParameters.
     */
    public function testGetParameters()
    {
        $content = new Content([ 'id' => 950 ]);

        $method = new \ReflectionMethod($this->renderer, 'getParameters');
        $method->setAccessible(true);

        $this->dl->expects($this->any())->method('customizeExtension')
            ->willReturn('foo');
        $this->extractor->expects($this->at(0))->method('get')
            ->with('extension')
            ->willReturn('bar');
        $this->extractor->expects($this->at(1))->method('get')
            ->with('tagSlugs')
            ->willReturn('baz');

        $params = $method->invokeArgs($this->renderer, [ $content ]);

        $this->assertIsArray($params);
    }

    /**
     * Tests validate when prometeo is correctly configured.
     */
    public function testValidateWhenCorrectConfiguration()
    {
        $renderer   = new PrometeoRenderer($this->container);
        $reflection = new \ReflectionClass($renderer);
        $config     = $reflection->getProperty('config');

        $config->setAccessible(true);
        $config->setValue($renderer, ['id' => 9999]);

        $method = new \ReflectionMethod($renderer, 'validate');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($renderer, []));
    }

    /**
     * Tests validate when prometeo is not correctly configured.
     */
    public function testValidateWhenIncorrectConfiguration()
    {
        $method = new \ReflectionMethod($this->renderer, 'validate');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->renderer, []));
    }
}
