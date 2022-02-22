<?php

namespace Tests\Api\Helper\Cache;

use Api\Helper\Cache\AlbumCacheHelper;
use Api\Helper\Cache\ContentCacheHelper;
use Api\Helper\Cache\NewsstandCacheHelper;
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;
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

        $this->assertEquals('opinion-1-inner', $cacheHelper->getXTags($opinion));
        $this->assertEquals('article-2-inner,category-2', $cacheHelper->getXTags($article));
    }

    /**
     * Tests deleteItem when the content is a newsstand.
     */
    public function testDeleteItemWhenNewsstand()
    {
        $newsstand = new Content(
            [
                'content_type_name' => 'kiosko',
                'path'              => '/glorp/baz.foo',
                'pk_content'       => 1,
                'tags'             => []
            ]
        );

        $cacheHelper = new NewsstandCacheHelper($this->instance, $this->queue, $this->cache);

        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('req.url ~ %s', $newsstand->path)
            ]));

        $cacheHelper->deleteItem($newsstand);
    }

    /**
     * Tests deleteItem when the content is an album.
     */
    public function testDeleteItemWhenAlbum()
    {
        $album = new Content(
            [
                'categories'        => [ 2 ],
                'content_type_name' => 'album',
                'fk_author'         => 2,
                'pk_content'        => 1,
                'tags'              => [ 1, 2, 3 ]
            ]
        );

        $cacheHelper = new AlbumCacheHelper($this->instance, $this->queue, $this->cache);

        $this->cache->expects($this->once())->method('removeByPattern')
            ->with('*WidgetAlbumLatest*');

        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'authors-frontpage')
            ]));


        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'category-2')
            ]));

        $this->queue->expects($this->at(2))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'content-author-2')
            ]));

        $this->queue->expects($this->at(3))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'frontpage-page')
            ]));

        $this->queue->expects($this->at(4))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'album-*-inner')
            ]));

        $this->queue->expects($this->at(5))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'album-frontpage$')
            ]));

        $this->queue->expects($this->at(6))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'rss-author-2')
            ]));

        $this->queue->expects($this->at(7))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'rss-frontpage$')
            ]));

        $this->queue->expects($this->at(8))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'rss-album$')
            ]));

        $this->queue->expects($this->at(9))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'rss-album,category-2')
            ]));

        $this->queue->expects($this->at(10))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', 'sitemap')
            ]));

        $this->queue->expects($this->at(11))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', 'glorp', '(tag-1)|(tag-2)|(tag-3)')
            ]));

        $cacheHelper->deleteItem($album);
    }
}
