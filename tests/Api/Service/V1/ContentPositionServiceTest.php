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

use Api\Service\V1\ContentPositionService;
use Common\ORM\Core\Entity;

/**
 * Defines test cases for ContentPositionService class.
 */
class ContentPositionServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'getContentPositions', 'getCategoriesWithManualFrontpage' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger' . uniqid())
            ->setMethods([ 'info' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getConverter' ,'getMetadata', 'getRepository', 'persist',
                'remove', 'getConnection'
            ])->getMock();

        $this->connection = $this->getMockBuilder('Connection' . uniqid())
            ->setMethods([
                'executeUpdate'
            ])->getMock();

        $this->user = new Entity([
            'email'    => 'flob@garply.com',
            'username' => 'unknownusername',
            'id'       => 1,
            'name'     => 'flob',
            'password' => 'quux',
            'type'     => 1
        ]);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->service = new ContentPositionService($this->container, 'Common\ORM\Core\Entity');
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.frontpage_version':
                return $this->frontpageVersionService;

            case 'orm.manager':
                return $this->em;

            case 'application.log':
                return $this->logger;

            case 'core.user':
                return $this->user;
        }

        return null;
    }

    /**
     * Tests getContentPositions.
     */
    public function testGetContentPositions()
    {
        $returnValue = [];

        $this->repository->expects($this->once())->method('getContentPositions')
            ->with(0, 0)
            ->willReturn($returnValue);

        $this->assertEquals($returnValue, $this->service->getContentPositions(0, 0));
    }

    /**
     * Tests getCategoriesWithManualFrontpage.
     */
    public function testGetCategoriesWithManualFrontpage()
    {
        $returnValue = [];

        $this->repository->expects($this->once())->method('getCategoriesWithManualFrontpage')
            ->willReturn($returnValue);

        $this->assertEquals($returnValue, $this->service->getCategoriesWithManualFrontpage());
    }

    /**
     * Tests clearContentPositionsForHomePageOfCategory.
     */
    public function testClearContentPositionsForHomePageOfCategory()
    {
        $this->em->expects($this->once())->method('getConnection')
            ->willReturn($this->connection);
        $this->logger->expects($this->once())->method('info')
            ->willReturn($this->connection);

        $this->assertTrue($this->service->clearContentPositionsForHomePageOfCategory(0, 0));
    }
}
