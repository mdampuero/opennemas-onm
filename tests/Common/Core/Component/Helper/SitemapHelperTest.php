<?php

namespace Common\Core\Component\Helper;

use Common\Model\Entity\Content;
use ReflectionProperty;

/**
 * Defines the test cases for the sitemap helper.
 */
class SitemapHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instanceConfig = [
            'total'   => 100,
            'perpage' => 100,
            'article' => true,
            'opinion' => false,
            'event'   => false,
            'kiosko'  => false,
            'letter'  => false,
            'opinion' => false,
            'poll'    => false,
            'tag'     => false,
            'video'   => false
        ];

        $this->managerConfig = [
            'total'   => 500,
            'perpage' => 500,
            'article' => true,
            'opinion' => true,
            'event'   => true,
            'kiosko'  => true,
            'letter'  => true,
            'opinion' => true,
            'poll'    => true,
            'tag'     => true,
            'video'   => true
        ];

        $this->connection = $this->getMockBuilder('Opennemas\Orm\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dataset = $this->getMockBuilder('DataSet' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->er = $this->getMockBuilder('Repository\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'countBy', 'findBy' ])
            ->getMock();

        $this->fs = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->setMethods([ 'exists', 'mkdir' ])
            ->getMock();

        $this->finder = $this->getMockBuilder('Symfony\Component\Finder')
            ->setMethods([ 'files', 'in', 'hasResults', 'getRelativePathName' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Common\Core\Component\Security\Security')
            ->setMethods([ 'hasExtension' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Common\Model\Entity\Instance')
            ->setMethods([ 'getSitemapShortPath' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->dataset->expects($this->at(0))->method('get')
            ->with('sitemap')
            ->willReturn($this->instanceConfig);

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->dataset);

        $this->instance->expects($this->any())->method('getSitemapShortPath')
            ->willReturn('sitemap/opennemas');

        $this->security->expects($this->any())->method('hasExtension')
            ->willReturn(true);

        $this->helper = new SitemapHelper(
            $this->container,
            $this->instance,
            $this->em,
            $this->connection,
            '/baz/glorp'
        );

        // Replace some internal variables to the mock.
        $property = new ReflectionProperty(get_class($this->helper), 'finder');
        $property->setAccessible(true);
        $property->setValue($this->helper, $this->finder);

        $property = new ReflectionProperty(get_class($this->helper), 'fs');
        $property->setAccessible(true);
        $property->setValue($this->helper, $this->fs);
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'entity_repository':
                return $this->er;

            case 'core.security':
                return $this->security;
        }

        return null;
    }

    /**
     * Tests the method getSettings.
     */
    public function testGetSettings()
    {
        $this->dataset->expects($this->at(0))->method('get')
            ->with('sitemap')
            ->willReturn(null);

        $this->dataset->expects($this->at(1))->method('get')
            ->with('sitemap')
            ->willReturn($this->managerConfig);

        $this->assertEquals($this->managerConfig, $this->helper->getSettings());

        $this->dataset->expects($this->at(0))->method('get')
            ->with('sitemap')
            ->willReturn($this->instanceConfig);

        $this->assertEquals($this->instanceConfig, $this->helper->getSettings());
    }

    /**
     * Tests the method getDates.
     */
    public function testGetDates()
    {
        $result = [
            [ 'dates' => '2020-02' ],
            [ 'dates' => '2021-03' ],
            [ 'dates' => '2021-06' ]
        ];

        $query = 'SELECT CONCAT(CONVERT(year(changed), NCHAR),\'-\', LPAD(month(changed),2,"0")) as \'dates\''
            . 'FROM `contents` WHERE year(changed) is not null '
            . 'AND `content_type_name` IN ("article") '
            . 'group by dates order by dates';

        $this->connection->expects($this->once())->method('fetchAll')
            ->with($query)
            ->willReturn($result);

        $this->assertEquals([ '2020-02', '2021-03', '2021-06' ], $this->helper->getDates());

        $this->instanceConfig['article'] = false;

        $property = new ReflectionProperty(get_class($this->helper), 'settings');
        $property->setAccessible(true);
        $property->setValue($this->helper, $this->instanceConfig);

        $this->assertEmpty($this->helper->getDates());
    }

    /**
     * Tests the method getSitemapInfo.
     */
    public function testGetSitemapInfo()
    {
        $helper = $this->getMockBuilder('Common\Core\Component\Helper\SitemapHelper')
            ->setConstructorArgs(
                [
                    $this->container,
                    $this->instance,
                    $this->em,
                    $this->connection,
                    '/baz/glorp'
                ]
            )
            ->setMethods([ 'getSitemaps' ])
            ->getMock();

        $property = new ReflectionProperty(get_class($this->helper), 'settings');
        $property->setAccessible(true);
        $property->setValue($helper, $this->instanceConfig);

        $result = [
            'items'  => [ 'sitemap.2020.02.1.xml.gz', 'sitemap.2021.03.1.xml.gz' ],
            'years'  => [ '2020', '2021' ],
            'months' => [ '02', '03' ]
        ];

        $years = [
            [ 'dates' => '2020' ],
            [ 'dates' => '2021' ]
        ];

        $months = [
            [ 'dates' => '02' ],
            [ 'dates' => '03' ],
        ];

        $yearsQuery = 'SELECT CONVERT(year(changed), NCHAR) as \'dates\''
            . 'FROM `contents` WHERE year(changed) is not null '
            . 'AND `content_type_name` IN ("article") '
            . 'group by dates order by dates';

        $monthsQuery = 'SELECT LPAD(month(changed),2,"0") as \'dates\''
            . 'FROM `contents` WHERE month(changed) is not null '
            . 'AND `content_type_name` IN ("article") '
            . 'group by dates order by dates';

        $this->connection->expects($this->at(0))->method('fetchAll')
            ->with($yearsQuery)
            ->willReturn($years);

        $this->connection->expects($this->at(1))->method('fetchAll')
            ->with($monthsQuery)->willReturn($months);

        $helper->expects($this->once())->method('getSitemaps')
            ->willReturn($result['items']);

        $this->assertEquals($result, $helper->getSitemapsInfo());
    }

    /**
     * Tests the method getSitemaps when there are no results.
     */
    public function testGetSitemaps()
    {
        $this->finder->expects($this->at(0))->method('files')
            ->willReturn($this->finder);

        $this->finder->expects($this->at(1))->method('hasResults')
            ->willReturn(false);

        $this->assertEquals([], $this->helper->getSitemaps());
    }

    /**
     * Tests the method getTypes when string flag is disabled.
     */
    public function testGetTypes()
    {
        $this->assertEquals([ 'article' ], $this->helper->getTypes($this->instanceConfig));
    }

    /**
     * Tests the method getContents.
     */
    public function testGetContents()
    {
        $date = gmdate('Y-m-d');

        $expected = [
            new Content([ 'content_type_name' => 'article' ]),
            new Content([ 'content_type_name' => 'opinion' ])
        ];

        $filters = [
            'content_type_name' => [
                [
                    'value'    => [ 'article', 'opinion' ],
                    'operator' => 'IN'
                ]
            ],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '>' ],
            ],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'changed ' => [
                [
                    'value' => sprintf(
                        '"%s" AND DATE_ADD("%s", INTERVAL 1 MONTH)',
                        date('Y-m-01 00:00:00', strtotime($date)),
                        date('Y-m-01 00:00:00', strtotime($date))
                    ),
                    'field' => true,
                    'operator' => 'BETWEEN'
                ]
            ],
        ];

        $this->er->expects($this->at(0))->method('findBy')
            ->with($filters, [ 'changed' => 'asc' ], 500)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->helper->getContents($date, [ 'article', 'opinion' ], 500));

        $this->er->expects($this->at(0))->method('countBy')
            ->with($filters)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->helper->getContents($date, [ 'article', 'opinion' ]));
    }

    /**
     * Tests the method deleteSitemaps
     *
     * The result will always be an empty array
     * because the unlink will always return false when performing a test.
     */
    public function testDeleteSitemaps()
    {
        $sitemaps = [
            '2021.02.1.xml.gz',
            '2021.02.3.xml.gz',
            '2021.02.10.xml.gz'
        ];

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\SitemapHelper')
            ->setConstructorArgs(
                [
                    $this->container,
                    $this->instance,
                    $this->em,
                    $this->connection,
                    '/baz/glorp'
                ]
            )
            ->setMethods([ 'getSitemaps' ])
            ->getMock();

        $helper->expects($this->once())->method('getSitemaps')
            ->willReturn($sitemaps);

        $this->assertEquals([], $helper->deleteSitemaps(
            [
                'year'  => '2021',
                'month' => '02',
                'page'  => '10'
            ]
        ));
    }
}
