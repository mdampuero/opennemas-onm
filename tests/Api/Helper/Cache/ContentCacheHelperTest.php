<?php

namespace Tests\Api\Helper\Cache;

use Api\Helper\Cache\ArticleCacheHelper;
use Api\Helper\Cache\ContentCacheHelper;
use Api\Helper\Cache\NewsstandCacheHelper;
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;
use DateTime;
use Opennemas\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for ContentCacheHelper class.
 */
class ContentCacheHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'glorp' ]);

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->queue = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->disableOriginalConstructor()
            ->setMethods([ 'push' ])
            ->getMock();

        $this->cs = $this->getMockBuilder('Common\Core\Component\Security\Security')
            ->disableOriginalConstructor()
            ->setMethods([ 'hasExtension' ])
            ->getMock();

        $this->cache = $this->getMockBuilder('Opennemas\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove', 'getSetMembers' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->cache->expects($this->any())->method('getSetMembers')
            ->willReturn([ 'widget_content_listing', 'widget_infinite_scroll' ]);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->helper = new ArticleCacheHelper($this->instance, $this->queue, $this->cache);

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
            case 'core.security':
                return $this->cs;

            default:
                return null;
        }
    }

    /**
     * Tests getXTags.
     */
    public function testGetXTags()
    {
        $opinion = new Content(
            [
                'pk_content'        => 1,
                'content_type_name' => 'opinion'
            ]
        );

        $article = new Content(
            [
                'pk_content'        => 2,
                'content_type_name' => 'article',
                'categories'        => [ 2 ]
            ]
        );

        $cacheHelper = new ContentCacheHelper($this->instance, $this->queue, $this->cache);

        $this->assertEquals('opinion-1', $cacheHelper->getXTags($opinion));
        $this->assertEquals('article-2', $cacheHelper->getXTags($article));
    }

    /**
     * Tests deleteItem.
     */
    public function testDeleteItem()
    {
        $now = new DateTime();

        $item = new Content([
            'pk_content'        => 1,
            'content_type_name' => 'article',
            'categories'        => [ 22 ],
            'starttime'         => $now,
            'tags'              => [ 12, 13, 14 ]
        ]);

        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ ^instance-%s.*%s',
                    $this->instance->internal_name,
                    sprintf(
                        '((archive-page-%s)|(authors-frontpage)|(category-22)|(article-frontpage$)|' .
                        '(article-frontpage,category-article-22)|(article-1)|' .
                        '(content_type_name-widget-article.*category-widget-(22|all).*tag-widget-((12|13|14)|all)' .
                        '.*author-widget-(0|all))|(last-suggested-22)|(rss-article$)|(sitemap)|(tag-(12|13|14))|' .
                        '(header-date))',
                        $now->format('Y-m-d')
                    )
                )
            ]));

        $this->helper->deleteItem($item);
    }

    /**
     * Tests getModuleKeys.
     */
    public function testGetModuleKeys()
    {
        $now = new DateTime();

        $item = new Content([
            'pk_content'        => 1,
            'content_type_name' => 'opinion',
            'starttime'         => $now,
            'tags'              => [ 12, 13, 14 ]
        ]);

        $this->assertEquals([], $this->helper->getModuleKeys($item));
    }
    /**
     * Tests deleteItem when the content is a newsstand.
     */
    public function testDeleteItemWhenNewsstand()
    {
        $now    = new DateTime();
        $helper = new NewsstandCacheHelper($this->instance, $this->queue, $this->cache);

        $item = new Content(
            [
                'pk_content'        => 10,
                'content_type_name' => 'kiosko',
                'path'              => '/glorp/baz',
                'starttime'         => $now
            ]
        );

        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('req.url ~ %s', $item->path)
            ]));

        $helper->deleteItem($item);
    }
}
