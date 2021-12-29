<?php

namespace Common\Core\Component\Helper;

use Common\Model\Entity\Content;

/**
 * Defines test cases for ContentHelper class.
 */
class ObituaryHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->content = new Content([
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00'),
            'maps'           => 'Link to GoogleMaps',
            'mortuary'       => 'Location',
            'website'        => 'Website'
        ]);

        $this->cache = $this->getMockBuilder('Cache' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Repository\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'find', 'findBy' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Common\Core\Component\Helper\SubscriptionHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isHidden' ])
            ->getMock();

        $this->service = $this->getMockBuilder('Api\Service\V1\ContentService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItemBy' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale' . uniqid())
            ->setMethods([ 'getTimeZone' ])->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Common\Api\Service\TagService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getListByIds' ])
            ->getMock();

        $this->locale->expects($this->any())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('UTC'));

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->contentHelper = new ObituaryHelper($this->container);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Returns a mocked service based on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.content':
                return $this->service;

            case 'api.service.tag':
                return $this->ts;

            case 'cache.connection.instance':
                return $this->cache;

            case 'core.helper.obituary':
                return $this->contentHelper;

            case 'core.helper.subscription':
                return $this->helper;

            case 'core.template.frontend':
                return $this->template;

            case 'entity_repository':
                return $this->em;

            case 'core.locale':
                return $this->locale;

            default:
                return null;
        }
    }

    /**
     * Tests getMaps.
     */
    public function testGetMaps()
    {
        $this->assertEquals('Link to GoogleMaps', $this->contentHelper->getMaps($this->content));

        $this->content->maps = '';

        $this->assertNull($this->contentHelper->getMaps($this->content));
    }

    /**
     * Tests getMortuary.
     */
    public function testGetMortuary()
    {
        $this->assertEquals('Location', $this->contentHelper->getMortuary($this->content));

        $this->content->mortuary = '';

        $this->assertNull($this->contentHelper->getMortuary($this->content));
    }

    /**
     * Tests getWebsite.
     */
    public function testGetWebsite()
    {
        $this->assertEquals('Website', $this->contentHelper->getWebsite($this->content));

        $this->content->website = '';

        $this->assertNull($this->contentHelper->getWebsite($this->content));
    }
}
