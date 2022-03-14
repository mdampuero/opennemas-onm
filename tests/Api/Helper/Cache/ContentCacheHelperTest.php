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

        $this->queue = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->disableOriginalConstructor()
            ->setMethods([ 'push' ])
            ->getMock();

        $this->cache = $this->getMockBuilder('Opennemas\Cache\Core\Cache')
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper = new ArticleCacheHelper($this->instance, $this->queue, $this->cache);
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
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'rss-instant-articles'
                )
            ]));

        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    sprintf('archive-page-%s', $now->format('Y-m-d'))
                )
            ]));

        $this->queue->expects($this->at(2))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'category-22'
                )
            ]));

        $this->queue->expects($this->at(3))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'content-author-0-frontpage'
                )
            ]));

        $this->queue->expects($this->at(4))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'article-frontpage$'
                )
            ]));

        $this->queue->expects($this->at(5))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'article-1'
                )
            ]));

        $this->queue->expects($this->at(6))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'content_type_name-widget-article' .
                    '.*category-widget-((22)|(all))' .
                    '.*tag-widget-(((12)|(13)|(14))|(all))' .
                    '.*author-widget-((0)|(all))'
                )
            ]));

        $this->queue->expects($this->at(7))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'last-suggested-22'
                )
            ]));

        $this->queue->expects($this->at(8))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'rss-author-0'
                )
            ]));

        $this->queue->expects($this->at(9))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'rss-article$'
                )
            ]));

        $this->queue->expects($this->at(10))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'sitemap'
                )
            ]));

        $this->queue->expects($this->at(11))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    'tag-(12)|(13)|(14)'
                )
            ]));

        $this->helper->deleteItem($item);
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

        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ instance-%s.*%s',
                    $this->instance->internal_name,
                    sprintf('archive-page-%s', $now->format('Y-m'))
                )
            ]));

        $helper->deleteItem($item);
    }
}
