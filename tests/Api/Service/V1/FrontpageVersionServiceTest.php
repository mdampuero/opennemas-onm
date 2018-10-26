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

use Api\Service\V1\FrontpageVersionService;
use Common\ORM\Core\Entity;

/**
 * Defines test cases for ContentPositionService class.
 */
class FrontpageVersionServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->contentPositionService = $this->getMockBuilder('ContentPositionService' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'getContentPositions', 'getCategoriesWithManualFrontpage' ])
            ->getMock();

        $this->entityRepository = $this->getMockBuilder('EntityManager' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'findMulti' ])
            ->getMock();


        $this->ormManager = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getConverter' ,'getMetadata', 'getRepository', 'persist',
                'remove', 'getConnection'
            ])->getMock();


        $this->locale = $this->getMockBuilder('Locale' . uniqid())
            ->setMethods([ 'getTimeZone' ])->getMock();

        $this->dispatcher =
            $this->getMockBuilder('Dispatcher' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->cache =
            $this->getMockBuilder('Cache' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch', 'save' ])
            ->getMock();


        $this->filterManager =
            $this->getMockBuilder('FilterManager' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'set' ])
            ->getMock();


        $this->frontpageRepository =
            $this->getMockBuilder('FrontpageRepository' . uniqid())
            ->setMethods([
                'getCatFrontpageRel', 'getCurrentVersionForCategory',
                'getNextVersionForCategory', 'countBy'
            ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->service = new FrontpageVersionService(
            $this->container,
            'Common\ORM\Core\Entity'
        );
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.content_position':
                return $this->contentPositionService;

            case 'orm.manager':
                return $this->ormManager;

            case 'entity_repository':
                return $this->entityRepository;

            case 'core.locale':
                return $this->locale;

            case 'core.dispatcher':
                return $this->dispatcher;

            case 'cache':
                return $this->cache;

            case 'data.manager.filter':
                return $this->filterManager;
        }

        return null;
    }

    /**
     * Tests the constructor
     **/
    public function testConstructor()
    {
        $services = [
            'entityRepository',
            'ormManager',
            'locale',
            'dispatcher',
            'cache',
            'filterManager',
        ];
        foreach ($services as $serviceName) {
            $this->assertAttributeEquals($this->{$serviceName}, $serviceName, $this->service);
        }
    }
}
