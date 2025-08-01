<?php

namespace Tests\Frontend\Renderer;

use PHPUnit\Framework\TestCase;
use Frontend\Renderer\Statistics\AdobeRenderer;
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for AdobeRenderer class.
 */
class AdobeRendererTest extends TestCase
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
            ->setMethods(['customizeExtension', 'customizeIsRestricted'])
            ->getMock();

        $this->extractor = $this->getMockBuilder('Common\Core\Component\Core\VariablesExtractor')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->global = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContainer', 'getSection' ])
            ->getMock();

        $this->featuredHelper = $this->getMockBuilder('Common\Core\Component\Helper\FeaturedMediaHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'hasFeaturedMedia', 'getFeaturedMedia' ])
            ->getMock();

        $this->ph = $this->getMockBuilder('PhotoHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPhotoPath' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->ds);

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->renderer = new AdobeRenderer($this->container);
        $this->instance = new Instance();
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
            case 'core.instance':
                return $this->instance;
            case 'core.helper.featured_media':
                return $this->featuredHelper;
            case 'core.helper.photo':
                return $this->ph;
        }

        return null;
    }

    /**
     * Tests getParameters.
     */
    public function testAdobeGetParameters()
    {
        $content = new Content([ 'id' => 950 ]);

        $method = new \ReflectionMethod($this->renderer, 'getParameters');
        $method->setAccessible(true);

        $this->extractor->expects($this->at(0))->method('get')
            ->with('lastAuthorId')
            ->willReturn('4');
        $this->extractor->expects($this->at(1))->method('get')
            ->with('canonicalUrl')
            ->willReturn('Surtur');
        $this->extractor->expects($this->at(2))->method('get')
            ->with('mediaType')
            ->willReturn('Sylvanus');
        $this->extractor->expects($this->at(3))->method('get')
            ->with('tagNames')
            ->willReturn('Odin');
        $this->extractor->expects($this->at(4))->method('get')
            ->with('tagSlugs')
            ->willReturn('Loki');
        $this->extractor->expects($this->at(5))->method('get')
            ->with('extension')
            ->willReturn('Jormungandr');
        $params = $method->invokeArgs($this->renderer, [ $content ]);

        $this->assertIsArray($params);
    }
}
