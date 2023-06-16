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
use Common\Core\Component\Locale\Locale;
use Common\Model\Entity\Category;
use Common\Model\Entity\Content;
use Common\Model\Entity\Frontpage;
use Opennemas\Orm\Core\Entity;

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

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods(['isReadyForPublish'])
            ->getMock();

        $this->contentPositionService = $this->getMockBuilder('ContentPositionService' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([
                'getContentPositions',
                'getCategoriesWithManualFrontpage',
                'clearContentPositionsForHomePageOfCategory'
            ])
            ->getMock();

        $this->cs = $this->getMockBuilder('Api\Service\V1\CategoryService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem', 'getList' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->fs = $this->getMockBuilder('Api\Service\V1\FrontpageService')
            ->disableOriginalConstructor()
            ->setMethods([ 'createItem' ])
            ->getMock();

        $this->entityRepository = $this->getMockBuilder('EntityManager' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'findMulti', 'find' ])
            ->getMock();

        $this->ormManager = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getConverter' ,'getMetadata', 'getRepository', 'persist',
                'remove', 'getConnection', 'getDataSet'
            ])->getMock();

        $this->locale = $this->getMockBuilder('Locale' . uniqid())
            ->setMethods([ 'getContext', 'setContext', 'getTimeZone' ])
            ->getMock();

        $this->locale->expects($this->any())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('UTC'));

        $this->locale->expects($this->any())->method('getContext')
            ->willReturn('frontend');

        $this->dispatcher = $this->getMockBuilder('Dispatcher' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->cache = $this->getMockBuilder('Cache' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch', 'save' ])
            ->getMock();

        $this->filterManager = $this->getMockBuilder('FilterManager' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'set', 'filter', 'get' ])
            ->getMock();

        $this->frontpageVersionsRepository = $this->getMockBuilder('FrontpageVersionRepository' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([
                'getCatFrontpageRel', 'getCurrentVersionForCategory',
                'getNextVersionForCategory', 'countBy', 'findBy'
            ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->ormManager->expects($this->any())->method('getRepository')
            ->willReturn($this->frontpageVersionsRepository);

        $this->service = new FrontpageVersionService(
            $this->container,
            'Opennemas\Orm\Core\Entity'
        );

        $this->content1 = new Content([
            'id'             => 1,
            'content_status' => 1,
            'pk_fk_content'  => 0,
            'starttime'      => null,
            'endtime'        => new \Datetime()
        ]);
        $this->content1->endtime->setTimestamp($this->content1->endtime->getTimestamp() + 10536000);

        $this->content2 = new Content([
            'id'             => 2,
            'content_status' => 1,
            'pk_fk_content'  => 0,
            'starttime'      => new \Datetime(),
            'endtime'        => new \Datetime()
        ]);
        $this->content2->starttime->setTimestamp($this->content2->starttime->getTimestamp() + 11536000);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.category':
                return $this->cs;

            case 'api.service.content_position':
                return $this->contentPositionService;

            case 'api.service.frontpage':
                return $this->fs;

            case 'orm.manager':
                return $this->ormManager;

            case 'entity_repository':
                return $this->entityRepository;

            case 'core.helper.content':
                return $this->contentHelper;

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
            'frontpageVersionsRepository'
        ];
        foreach ($services as $serviceName) {
            $this->assertAttributeEquals($this->{$serviceName}, $serviceName, $this->service);
        }
    }

    /**
     * Tests checkLastSaved.
     */
    public function testcheckLastSaved()
    {
        $this->assertFalse($this->service->checkLastSaved(1, 3, ''));
        $this->assertTrue($this->service->checkLastSaved(1, 3, '2050-01-01 00:00:00'));
    }

    /**
     * Tests getLastSaved.
     */
    public function testgetLastSaved()
    {
        $this->assertNotEmpty($this->service->getLastSaved(1, 3));
    }

    /**
     * Tests getFrontpageDataFromCache.
     */
    public function testgetFrontpageDataFromCached()
    {
        $method = new \ReflectionMethod($this->service, 'getFrontpageDataFromCache');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->service, [ 1, 3 ]));
        $this->assertNull($method->invokeArgs($this->service, [ 1, '' ]));
    }

    /**
     * Tests setFrontpageDataFromCache.
     */
    public function testsetFrontpageDataFromCache()
    {
        $method = new \ReflectionMethod($this->service, 'setFrontpageDataFromCache');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->service, [ 1, 3, 4]));
        $this->assertNull($method->invokeArgs($this->service, [ 1, '', '' ]));
    }

    /**
     * Tests purgeCacheForCategoryIdAndVersionId.
     */
    public function testpurgeCacheForCategoryIdAndVersionId()
    {
        $method = new \ReflectionMethod($this->service, 'purgeCacheForCategoryIdAndVersionId');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->service, [ 1, 3]));
    }

    /**
     * Tests filterPublishedContents.
     */
    public function testfilterPublishedContents()
    {
        $method = new \ReflectionMethod($this->service, 'filterPublishedContents');
        $method->setAccessible(true);

        $this->contentHelper->expects($this->once())->method('isReadyForPublish')
            ->willReturn(true);

        $contents = [ 'id' => 8883 ];

        $this->assertEquals(
            $contents,
            $method->invokeArgs($this->service, [ $contents ])
        );
    }

    /**
     * Tests getCurrentVersiont.
     */
    public function testgetCurrentVersiont()
    {
        $version1               = new \Common\Model\Entity\FrontpageVersion();
        $version2               = new \Common\Model\Entity\FrontpageVersion();
        $version2->publish_date = new \Datetime('2050-01-01 00:00:00');
        $version3               = new \Common\Model\Entity\FrontpageVersion();
        $version3->publish_date = new \Datetime();

        $versions = [ (object) $version1, (object) $version2, (object) $version3 ];

        $this->assertEquals($version3, $this->service->getCurrentVersion($versions));
    }

    /**
     * Tests getCatFrontpagesRel.
     */
    public function testgetCatFrontpagesRel()
    {
        $this->assertNull($this->service->getCatFrontpagesRel());
    }

    /**
     * Tests getCurrentVersionFromDB.
     */
    public function testgetCurrentVersionFromDB()
    {
        $this->assertNull($this->service->getCurrentVersionFromDB(1));
    }

    /**
     * Tests getNextVersionForCategory.
     */
    public function testgetNextVersionForCategory()
    {
        $this->assertNull($this->service->getNextVersionForCategory(1));
    }

    /**
     * Tests getDefaultNameFV.
     */
    public function testgetDefaultNameFV()
    {
        $this->assertEquals('2020-03-04 11:55', $this->service->getDefaultNameFV('1583322926'));
    }

    /**
     * Tests getInvalidationTime.
     */
    public function testgetInvalidationTime()
    {
        $method = new \ReflectionMethod($this->service, 'getInvalidationTime');
        $method->setAccessible(true);

        $contents = [ $this->content2, $this->content1 ];

        $date = new \Datetime();
        $date->setTimestamp($date->getTimestamp() + 10536000);

        $this->assertEquals(
            $date->getTimestamp(),
            $method->invokeArgs($this->service, [ $contents, 1 ])->getTimestamp()
        );
    }

    /**
     * Tests getInvalidationTime with frontpageVersionId not empty.
     */
    public function testgetInvalidationTimeWithfrontpageVersionId()
    {
        $manager = $this->getMockBuilder('Api\Service\V1\FrontpageVersionService')
            ->setConstructorArgs([ $this->container, 'Opennemas\Orm\Core\Entity' ])
            ->setMethods([ 'getNextVersionForCategory', 'getItem' ])
            ->getMock();

        $method = new \ReflectionMethod($manager, 'getInvalidationTime');
        $method->setAccessible(true);

        $version               = new \Common\Model\Entity\FrontpageVersion();
        $version->publish_date = new \Datetime();
        $version->publish_date->setTimestamp($version->publish_date->getTimestamp() + 10536000);

        $manager->expects($this->at(0))->method('getNextVersionForCategory')
            ->with(1)->willReturn(1);
        $manager->expects($this->at(1))->method('getItem')
            ->with(1)->willReturn($version);

        $contents = [ ];

        $date = new \Datetime();
        $date->setTimestamp($date->getTimestamp() + 10536000);

        $this->assertEquals(
            $date->getTimestamp(),
            $method->invokeArgs($manager, [ $contents, 1 ])->getTimestamp()
        );
    }

    /**
     * Tests getContentPositionsAndContents.
     */
    public function testgetContentPositionsAndContents()
    {
        $this->entityRepository->expects($this->any())->method('find')
            ->willReturn(1);

        $manager = $this->getMockBuilder('Api\Service\V1\FrontpageVersionService')
            ->setConstructorArgs([ $this->container, 'Opennemas\Orm\Core\Entity' ])
            ->setMethods([ 'getContentPositions' ])
            ->getMock();

        $method = new \ReflectionMethod($manager, 'getContentPositionsAndContents');
        $method->setAccessible(true);

        $contents = [ $this->content2, $this->content1 ];

        $contentPositions = [ $contents ];

        $manager->expects($this->at(0))->method('getContentPositions')
            ->with(1, 3)->willReturn($contentPositions);

        $this->assertIsArray($method->invokeArgs($manager, [ 1, 3 ]));
    }

    /**
     * Tests deleteVersionItem.
     */
    public function testdeleteVersionItem()
    {
        $this->contentPositionService->expects($this->any())->method('clearContentPositionsForHomePageOfCategory')
            ->with(1, 3)->willReturn(true);

        $manager = $this->getMockBuilder('Api\Service\V1\FrontpageVersionService')
            ->setConstructorArgs([ $this->container, 'Opennemas\Orm\Core\Entity' ])
            ->setMethods([ 'deleteItem' ])
            ->getMock();

        $manager->expects($this->once())->method('deleteItem')
            ->with(3);

        $this->assertNull($manager->deleteVersionItem(1, 3));
    }

    /**
     * Tests getPublicFrontpageData.
     */
    public function testgetPublicFrontpageData()
    {
        $manager = $this->getMockBuilder('Api\Service\V1\FrontpageVersionService')
            ->setConstructorArgs([ $this->container, 'Opennemas\Orm\Core\Entity' ])
            ->setMethods([
                'getContentsInCurrentVersionforCategory',
                'getNextVersionForCategory',
                'getItem',
                'getLastSaved'
            ])
            ->getMock();

        $version               = new \Common\Model\Entity\FrontpageVersion();
        $version->id           = 1;
        $version->publish_date = new \Datetime();
        $version->publish_date->setTimestamp($version->publish_date->getTimestamp() + 10536000);

        $contents = [ $this->content2, $this->content1 ];

        $contentPositions = [ $contents ];

        $manager->expects($this->once())->method('getContentsInCurrentVersionforCategory')
            ->with(1)->willReturn([$version, $contentPositions, $contents]);
        $manager->expects($this->once())->method('getNextVersionForCategory')
            ->with(1)->willReturn(1);
        $manager->expects($this->once())->method('getItem')
            ->with(1)->willReturn($version);
        $manager->expects($this->any())->method('getLastSaved')
            ->with(1, 1)->willReturn(1);
        $this->contentHelper->expects($this->any())->method('isReadyForPublish')
            ->willReturn(true);

        $this->assertIsArray($manager->getPublicFrontpageData(1));
    }

    /**
     * Tests getContentsInCurrentVersionforCategory.
     */
    public function testgetContentsInCurrentVersionforCategory()
    {
        $manager = $this->getMockBuilder('Api\Service\V1\FrontpageVersionService')
            ->setConstructorArgs([ $this->container, 'Opennemas\Orm\Core\Entity' ])
            ->setMethods([ 'getCurrentVersionFromDB', 'getItem', 'getContentPositions' ])
            ->getMock();

        $version               = new \Common\Model\Entity\FrontpageVersion();
        $version->id           = 1;
        $version->publish_date = new \Datetime();
        $version->publish_date->setTimestamp($version->publish_date->getTimestamp() + 10536000);

        $contents = [ $this->content2, $this->content1 ];

        $contentPositions = [ $contents ];

        $this->entityRepository->expects($this->any())->method('find')
            ->willReturn($this->content1);

        $manager->expects($this->at(0))->method('getCurrentVersionFromDB')
            ->with(1)->willReturn(1);
        $manager->expects($this->any())->method('getItem')
            ->with(1)->willReturn($version);
        $manager->expects($this->any())->method('getContentPositions')
            ->with(1, 1)->willReturn($contentPositions);

        $this->assertIsArray($manager->getContentsInCurrentVersionforCategory(1));
    }

    /**
     * Tests getFrontpageData.
     */
    public function testgetFrontpageData()
    {
        $manager = $this->getMockBuilder('Api\Service\V1\FrontpageVersionService')
            ->setConstructorArgs([ $this->container, 'Opennemas\Orm\Core\Entity' ])
            ->setMethods([
                'getFrontpageWithCategory',
                'getCurrentVersion',
                'getContentPositions'
            ])
            ->getMock();

        $method = new \ReflectionMethod($manager, 'getFrontpageData');

        $version1     = new \Common\Model\Entity\FrontpageVersion();
        $version1->id = 1;
        $version2     = new \Common\Model\Entity\FrontpageVersion();
        $version2->id = 1;

        $contents = [ $this->content2, $this->content1 ];

        $contentPositions = [ $contents ];

        $manager->expects($this->at(0))->method('getFrontpageWithCategory')
            ->with(1)->willReturn([1, [$version1, $version2]]);
        $manager->expects($this->at(1))->method('getCurrentVersion')
            ->with([$version1, $version2])->willReturn($version1);
        $manager->expects($this->at(2))->method('getContentPositions')
            ->with(1, 1)->willReturn($contentPositions);

        list($frontpages, $versions, $contentPositions, $contents, $versionId) =
            $method->invokeArgs($manager, [ 1, '' ]);

        $this->assertEquals($frontpages, 1);

        $manager->expects($this->at(0))->method('getFrontpageWithCategory')
            ->with(1)->willReturn([1, null]);
        $manager->expects($this->at(1))->method('getContentPositions')
            ->with(1, null)->willReturn($contentPositions);

        list($frontpages, $versions, $contentPositions, $contents, $versionId) =
            $method->invokeArgs($manager, [ 1, 1 ]);

        $this->assertEquals($frontpages, 1);

        $manager->expects($this->at(0))->method('getFrontpageWithCategory')
            ->with(1)->willReturn([1, [$version1, $version2]]);
        $manager->expects($this->at(1))->method('getContentPositions')
            ->with(1, 1)->willReturn($contentPositions);

        list($frontpages, $versions, $contentPositions, $contents, $versionId) =
            $method->invokeArgs($manager, [ 1, 1 ]);

        $this->assertEquals($frontpages, 1);
    }

    /**
     * Tests getContentPositions.
     */
    public function testgetContentPositions()
    {
        $this->contentPositionService->expects($this->any())->method('getContentPositions')
            ->willReturn([]);

        $this->assertEquals([], $this->service->getContentPositions(1, 1));
    }

    /**
     * Tests getContentIds.
     */
    public function testgetContentIds()
    {
        $manager = $this->getMockBuilder('Api\Service\V1\FrontpageVersionService')
            ->setConstructorArgs([ $this->container, 'Opennemas\Orm\Core\Entity' ])
            ->setMethods([ 'getCurrentVersionFromDB', 'getContentPositions' ])
            ->getMock();

        $contents = [ $this->content2, $this->content1 ];

        $contentPositions = [ $contents ];

        $manager->expects($this->any())->method('getCurrentVersionFromDB')
            ->with(1)->willReturn(2);
        $manager->expects($this->any())->method('getContentPositions')
            ->with(1, 2)->willReturn($contentPositions);

        $this->assertIsArray($manager->getContentIds(1, 2, null));
        $this->assertIsArray($manager->getContentIds(1, null, null));
    }

    /**
     * Tests getFrontpageWithCategoryWhenEmptyCategory.
     */
    public function testGetFrontpageWithCategoryWhenEmptyCategory()
    {
        $category = new Category([ 'id' => 1 ]);

        $this->cs->expects($this->once())
            ->method('getList')
            ->willReturn([ 'items' => [ $category ] ]);

        $this->ormManager->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->frontpageVersionsRepository->expects($this->once())
            ->method('getCatFrontpageRel')
            ->willReturn([ 1 => $category ]);

        $this->contentPositionService->expects($this->any())->method('getCategoriesWithManualFrontpage')
            ->willReturn([]);

        $this->frontpageVersionsRepository->expects($this->once())->method('findBy')
            ->with("category_id = 0 order by publish_date desc")
            ->willReturn([]);

        list($frontpages, $versions) = $this->service->getFrontpageWithCategory(null);

        $category = new Category([ 'id' => 1, 'name' => null ]);
        $this->assertEquals([
            [ 'id' => 1, 'name' => null, 'frontpage_id' => $category, 'manual' => true ],
            [ 'id' => 0, 'name' => 'Frontpage', 'manual' => false ]
        ], $frontpages);
        $this->assertEquals([], $versions);
    }

    /**
     * Tests getFrontpageWithCategory when not empty category.
     */
    public function testGetFrontpageWithCategoryWhenNotEmptyCategory()
    {
        $category = new Category([ 'id' => 1, 'title' => 'category' ]);

        $this->cs->expects($this->once())
            ->method('getList')
            ->willReturn([ 'items' => [ $category ] ]);

        $this->ormManager->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->frontpageVersionsRepository->expects($this->once())
            ->method('getCatFrontpageRel')
            ->willReturn([ 0 => $category ]);

        $this->contentPositionService->expects($this->any())->method('getCategoriesWithManualFrontpage')
            ->willReturn([]);

        $this->filterManager->expects($this->at(0))->method('set')
            ->with('category')
            ->willReturn($this->filterManager);

        $this->filterManager->expects($this->at(1))->method('filter')
            ->with('localize')
            ->willReturn($this->filterManager);

        $this->filterManager->expects($this->at(2))->method('get')
            ->willReturn('category');

        $this->frontpageVersionsRepository->expects($this->once())->method('findBy')
            ->with("category_id = 1 order by publish_date desc")
            ->willReturn([]);

        list($frontpages, $versions) = $this->service->getFrontpageWithCategory(1);

        $this->assertEquals([
            [ 'id' => 0, 'name' => 'Frontpage', 'manual' => true ],
            [ 'id' => 1, 'name' => 'category', 'manual' => false ]
        ], $frontpages);
        $this->assertEquals([], $versions);
    }

    /**
     * Tests saveFrontPageVersion.
     */
    public function testsaveFrontPageVersion()
    {
        $this->frontpageVersionsRepository->expects($this->any())->method('countBy')
            ->willReturn(3);

        $manager = $this->getMockBuilder('Api\Service\V1\FrontpageVersionService')
            ->setConstructorArgs([ $this->container, 'Opennemas\Orm\Core\Entity' ])
            ->setMethods([ 'createItem' ])
            ->getMock();

        $version               = new \Common\Model\Entity\FrontpageVersion();
        $version->id           = 1;
        $version->publish_date = new \Datetime();
        $version->publish_date->setTimestamp($version->publish_date->getTimestamp() + 10536000);

        $manager->expects($this->any())->method('createItem')
            ->willReturn($version);

        $this->assertEquals(
            $version,
            $manager->saveFrontPageVersion([ 'frontpage_id' => 1 ])
        );
    }

    /**
     * Tests saveFrontPageVersion with Exception.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testsaveFrontPageVersionWithException()
    {
        $this->frontpageVersionsRepository->expects($this->any())->method('countBy')
            ->willReturn(30);

        $this->service->saveFrontPageVersion([ 'frontpage_id' => 1 ]);
    }

    /**
     * Tests saveFrontPageVersion with empty frontpage version.
     */
    public function testsaveFrontPageVersionWithEmptyFronpageVersion()
    {
        $this->frontpageVersionsRepository->expects($this->any())->method('countBy')
            ->willReturn(3);

        $this->cs->expects($this->any())->method('getItem')
            ->with(1)
            ->willReturn(new Category(['name' => 'log']));

        $this->fs->expects($this->any())->method('createItem')
            ->with(['name' => 'log'])
            ->willReturn(new Frontpage(['id' => 1]));

        $manager = $this->getMockBuilder('Api\Service\V1\FrontpageVersionService')
            ->setConstructorArgs([ $this->container, 'Opennemas\Orm\Core\Entity' ])
            ->setMethods([ 'createItem' ])
            ->getMock();

        $version               = new \Common\Model\Entity\FrontpageVersion();
        $version->id           = 1;
        $version->publish_date = new \Datetime();
        $version->publish_date->setTimestamp($version->publish_date->getTimestamp() + 10536000);

        $manager->expects($this->any())->method('createItem')
            ->willReturn($version);

        $this->assertEquals(
            $version,
            $manager->saveFrontPageVersion([
                'frontpage_id' => null,
                 'category_id' => 1
                ])
        );
    }

    /**
     * Tests saveFrontPageVersion when frontpage version exists.
     */
    public function testsaveFrontPageVersionWhenFronpageVersionExists()
    {
        $manager = $this->getMockBuilder('Api\Service\V1\FrontpageVersionService')
            ->setConstructorArgs([ $this->container, 'Opennemas\Orm\Core\Entity' ])
            ->setMethods([ 'updateItem' ])
            ->getMock();

        $version              = new \Common\Model\Entity\FrontpageVersion();
        $version->id          = 1;
        $version->category_id = 2;

        $manager->expects($this->any())->method('updateItem')
            ->willReturn($version);

        $this->assertEquals(
            $version,
            $manager->saveFrontPageVersion([ 'id' => 1, 'category_id' => 2 ])
        );
    }
}
