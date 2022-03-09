<?php

namespace Tests\Api\Helper\Cache;

use Api\Helper\Cache\AlbumCacheHelper;
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
}
