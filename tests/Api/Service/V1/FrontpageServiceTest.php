<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Api\Service\V1;

use Api\Service\V1\FrontpageService;
use Common\ORM\Core\Entity;

/**
 * Defines test cases for FrontpageService class.
 */
class FrontpageServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->frontpageVersionService = $this->getMockBuilder('FrontpageVersionService' . uniqid())
            ->setMethods([ 'getFrontpageData', 'getPublicFrontpageData' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->service = new FrontpageService($this->container, 'Common\ORM\Core\Entity');
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.frontpage_version':
                return $this->frontpageVersionService;
        }

        return null;
    }

    /**
     * Tests getDataForCategoryAndVersion.
     */
    public function testGetDataForCategoryAndVersion()
    {
        $returnValue = [];

        $this->frontpageVersionService->expects($this->once())
            ->method('getFrontpageData')
            ->with(0, 0)
            ->will($this->returnValue([]));

        $this->assertEquals($returnValue, $this->service->getDataForCategoryAndVersion(0, 0));
    }

    /**
     * Tests getCurrentVersionForCategory.
     */
    public function testGetCurrentVersionForCategory()
    {
        $returnValue = [];

        $this->frontpageVersionService->expects($this->once())
            ->method('getPublicFrontpageData')
            ->with(0)
            ->will($this->returnValue([]));

        $this->assertEquals($returnValue, $this->service->getCurrentVersionForCategory(0));
    }
}
