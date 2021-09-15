<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\Component\Routing;

use Common\Model\Entity\Content;
use Framework\Component\Routing\ContentUrlMatcher;

class ContentUrlMatcherTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Cache')
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isReadyForPublish' ])
            ->getMock();

        $this->categoryHelper = $this->getMockBuilder('Common\Core\Component\Helper\CategoryHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getCategorySlug' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Repository\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'find' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('DatabaseConnection')
            ->setMethods([ 'fetchAll' ])
            ->getMock();

        $this->fm = $this->getMockBuilder('Opennemas\Data\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getTimeZone' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'hasMultilanguage' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->cache->expects($this->any())->method('fetch')
            ->willReturn([]);
        $this->conn->expects($this->any())->method('fetchAll')
            ->willReturn([]);
        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->fm->expects($this->any())->method('set')
            ->willReturn($this->fm);
        $this->fm->expects($this->any())->method('filter')
            ->willReturn($this->fm);
        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);
        $this->locale->expects($this->any())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('UTC'));

        $GLOBALS['kernel'] = $this->kernel;

        $this->content = new Content([
            'pk_content'        => 184,
            'created'           => '2015-01-14 23:50:16',
            'starttime'         => '2015-01-14 23:55:36',
            'endtime'           => null,
            'content_type_name' => 'article',
            'slug'              => 'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto',
            'content_status'    => '1',
            'in_litter'         => 0,
        ]);

        $this->content->category_slug = 'ciencia'; // Loaded separatedly to avoid ContentCategory call

        $this->matcher = new ContentUrlMatcher($this->em, $this->contentHelper, $this->categoryHelper);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'cache':
                return $this->cache;

            case 'core.helper.content':
                return $this->contentHelper;

            case 'core.locale':
                return $this->locale;

            case 'data.manager.filter':
                return $this->fm;

            case 'dbal_connection':
                return $this->conn;

            case 'core.instance':
                return $this->instance;

            default:
                return null;
        }
    }

    public function testValidContent()
    {
        $this->em->expects($this->once())->method('find')
            ->willReturn($this->content);

        $this->contentHelper->expects($this->once())->method('isReadyForPublish')
            ->willReturn(true);

        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016000184',
            'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto'
        );

        $this->assertTrue(is_object($return));
    }

    public function testValidContentWithShortId()
    {
        $this->em->expects($this->once())->method('find')
            ->willReturn($this->content);

        $this->contentHelper->expects($this->once())->method('isReadyForPublish')
            ->willReturn(true);

        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016184',
            'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto'
        );

        $this->assertTrue(is_object($return));
    }

    public function testWithInvalidDirtyID()
    {
        $this->em->expects($this->never())->method('find')
            ->willReturn($this->content);

        $return = $this->matcher->matchContentUrl(
            'article',
            '23501600014'
        );

        $this->assertFalse(is_object($return), 'The content is not matching');
    }

    public function testWithoutFullArgs()
    {
        $this->em->expects($this->any())->method('find')
            ->willReturn($this->content);


        $this->contentHelper->expects($this->any())->method('isReadyForPublish')
            ->willReturn(true);

        // Not valid category
        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016000184'
        );

        $this->assertTrue(is_object($return));

        // Not valid category
        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016000184',
            'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto'
        );
        $this->assertTrue(is_object($return));

        // Not valid category
        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016000184',
            null
        );
        $this->assertTrue(is_object($return));

        // Not valid category
        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016000184',
            null
        );
        $this->assertTrue(is_object($return));
    }

    public function testEmptyDirtyId()
    {
        $this->em->expects($this->never())->method('find')
            ->willReturn(null);

        $this->assertNull(
            $this->matcher->matchContentUrl('foo', null),
            'The content is not matching'
        );
    }

    public function testScheduledContent()
    {
        $content = clone $this->content;

        $content->starttime = '3015-01-14 23:55:36';
        $this->em->expects($this->once())->method('find')
            ->willReturn($content);

        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016000336',
            'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto',
            'ciencia'
        );

        $this->assertFalse(is_object($return), 'The content is not matching');
    }

    public function testDuedContent()
    {
        $content = clone $this->content;

        $content->endtime = '2015-01-14 23:55:36';
        $this->em->expects($this->once())->method('find')
            ->willReturn($content);

        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016000336',
            'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto',
            'ciencia'
        );
        $this->assertFalse(is_object($return), 'The content is not matching');
    }

    public function testInLitterContent()
    {
        $content = clone $this->content;

        $content->in_litter = 1;
        $this->em->expects($this->once())->method('find')
            ->willReturn($content);

        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016000336',
            'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto',
            'ciencia'
        );
        $this->assertFalse(is_object($return), 'The content is not matching');
    }

    public function testNotMatchingEntriesWithContent()
    {
        $this->em->expects($this->any())->method('find')
            ->willReturn($this->content);

        // Not valid content type
        $return = $this->matcher->matchContentUrl(
            'opinion',
            '20150114235016040336',
            'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto',
            'ciencia'
        );
        $this->assertFalse(is_object($return), 'The content is not matching');

        // Not valid content id
        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016040333',
            'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto',
            'ciencia'
        );
        $this->assertFalse(is_object($return), 'The content is not matching');

        // Not valid date in dirtyId
        $return = $this->matcher->matchContentUrl(
            'article',
            '20160114235016000336',
            'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto',
            'ciencia'
        );
        $this->assertFalse(is_object($return), 'The content is not matching');

        // Not valid slug
        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016000336',
            'invalidslug',
            'ciencia'
        );
        $this->assertFalse(is_object($return), 'The content is not matching');

        // Not valid category
        $return = $this->matcher->matchContentUrl(
            'article',
            '20150114235016000336',
            'subida-mar-ultimas-decadas-ha-sido-mas-rapida-previsto',
            'invalidcategory'
        );
        $this->assertFalse(is_object($return), 'The content is not matching');
    }
}
