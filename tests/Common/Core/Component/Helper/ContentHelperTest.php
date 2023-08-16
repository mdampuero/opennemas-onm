<?php

namespace Common\Core\Component\Helper;

use Api\Exception\GetItemException;
use Common\Model\Entity\Content;
use Common\Model\Entity\Tag;

/**
 * Defines test cases for ContentHelper class.
 */
class ContentHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->content = new Content([
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->cache = $this->getMockBuilder('Cache' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Repository\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'find', 'findBy' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Common\Core\Component\Helper\SubscriptionHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isHidden' ])
            ->getMock();

        $this->service = $this->getMockBuilder('Api\Service\V1\ContentService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItemBy' ])
            ->getMock();

        $this->theme = $this->getMockBuilder('Common\Model\Entity\Theme')
            ->disableOriginalConstructor()
            ->setMethods([ 'getSuggestedEpp' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale' . uniqid())
            ->setMethods([ 'getTimeZone' ])->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Common\Api\Service\TagService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getListByIds' ])
            ->getMock();

        $this->locale->expects($this->any())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('UTC'));

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->contentHelper = new ContentHelper($this->container);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->theme->expects($this->any())->method('getSuggestedEpp')
            ->willReturn(4);

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
            case 'api.service.content':
                return $this->service;

            case 'api.service.tag':
                return $this->ts;

            case 'cache.connection.instance':
                return $this->cache;

            case 'core.helper.content':
                return $this->contentHelper;

            case 'core.helper.subscription':
                return $this->helper;

            case 'core.template.frontend':
                return $this->template;

            case 'core.theme':
                return $this->theme;

            case 'entity_repository':
                return $this->em;

            case 'core.locale':
                return $this->locale;

            default:
                return null;
        }
    }

    /**
     * Tests getBody.
     */
    public function testGetBody()
    {
        $this->assertNull($this->contentHelper->getBody($this->content));

        $this->content->body = 'His ridens eu sed quod ignota.';
        $this->assertEquals('His ridens eu sed quod ignota.', $this->contentHelper->getBody($this->content));

        $this->content->body = 'Percipit "mollis" at scriptorem usu.';
        $this->assertEquals('Percipit "mollis" at scriptorem usu.', $this->contentHelper->getBody($this->content));
    }

    /**
     * Tests getBodyWithLiveUpdates.
     */
    public function testGetBodyWithLiveUpdates()
    {
        $this->assertNull($this->contentHelper->getBodyWithLiveUpdates($this->content));

        $this->content->body = 'His ridens eu sed quod ignota.';

        $timezone = $this->locale->getTimeZone();
        $now      = new \DateTime(null, $timezone);

        $this->content->coverage_start_time = $now;
        $this->content->coverage_end_time   = $now;
        $this->content->live_blog_posting   = 1;
        $this->content->live_blog_updates   = [[
            'body' => 'Percipit "mollis" at scriptorem usu.'
        ]];

        $this->assertEquals(
            'His ridens eu sed quod ignota. Percipit "mollis" at scriptorem usu.',
            $this->contentHelper->getBodyWithLiveUpdates($this->content)
        );
    }

    /**
     * Tests getBody when the content has a custom body.
     */
    public function testGetBodyWhenCustom()
    {
        $this->content->description       = 'Lorem, ipsum dolor.';
        $this->content->content_type_name = 'video';

        $this->assertEquals($this->content->description, $this->contentHelper->getBody($this->content));
    }

    /**
     * Tests getCacheExpireDate when there is an error.
     */
    public function testGetCacheExpireDateWhenError()
    {
        $this->service->expects($this->at(0))->method('getItemBy')
            ->will($this->throwException(new \Exception()));

        $this->service->expects($this->at(1))->method('getItemBy')
            ->will($this->throwException(new \Exception()));

        $this->assertNull($this->contentHelper->getCacheExpireDate());
    }

    /**
     * Tests getCacheExpireDate.
     */
    public function testGetCacheExpireDate()
    {
        $this->service->expects($this->any())->method('getItemBy')
            ->willReturn($this->content);

        $this->assertEquals(
            $this->content->starttime->format('Y-m-d H:i:s'),
            $this->contentHelper->getCacheExpireDate()
        );
    }

    /**
     * Tests getCaption for objects and for items as related content.
     */
    public function testGetCaption()
    {
        $item = new Content([
            'content_status' => 1,
            'starttime'      => new \Datetime('2000-01-01 00:00:00')
        ]);

        $this->assertNull($this->contentHelper->getCaption($item));
        $this->assertEquals('Suas sonet appellantur patrioque', $this->contentHelper->getCaption([
            'item'     => $item,
            'caption'  => 'Suas sonet appellantur patrioque',
            'position' => 365
        ]));
    }

    /**
     * Tests getContent when a content is already provided as parameter.
     */
    public function testGetContentFromParameter()
    {
        $this->assertNull($this->contentHelper->getContent());
        $this->assertEquals($this->content, $this->contentHelper->getContent($this->content));
    }

    /**
     * Tests getContent when the content is provided as an array with keys
     * item, position and caption.
     */
    public function testGetContentForRelatedContent()
    {
        $this->assertNull($this->contentHelper->getContent());
        $this->assertEquals($this->content, $this->contentHelper->getContent([
            'caption'  => 'Moderatius eum soleat omittantur massa usu oportere.',
            'item'     => $this->content,
            'position' => 0,
        ]));
    }

    /**
     * Tests getContent when item is not found.
     */
    public function testGetContentWhenNotFound()
    {
        $this->em->expects($this->once())->method('find')
            ->with('Photo', 43)
            ->will($this->throwException(new GetItemException()));

        $this->assertNull($this->contentHelper->getContent(43, 'Photo'));
    }

    /**
     * Tests getContent when the content id is provided as parameter.
     */
    public function testGetContentFromParameterWhenId()
    {
        $this->em->expects($this->once())->method('find')
            ->with('Photo', 43)->willReturn($this->content);

        $this->assertEquals($this->content, $this->contentHelper->getContent(43, 'Photo'));
    }

    /**
     * Tests getContent when the item is extracted from template and it is a
     * content.
     */
    public function testGetContentFromTemplateWhenContent()
    {
        $this->template->expects($this->once())->method('getValue')
            ->with('item')->willReturn($this->content);

        $this->assertEquals($this->content, $this->contentHelper->getContent());
    }

    /**
     * Tests getCreationDate.
     */
    public function testGetCreationDate()
    {
        $this->content->created = '2010-10-10 10:00:00';

        $this->assertEquals(
            new \Datetime('2010-10-10 10:00:00'),
            $this->contentHelper->getCreationDate($this->content)
        );
    }

    /**
     * Tests getDescription.
     */
    public function testGetDescription()
    {
        $this->assertNull($this->contentHelper->getDescription($this->content));

        $this->content->description = 'His ridens eu sed quod ignota.';
        $this->assertEquals('His ridens eu sed quod ignota.', $this->contentHelper->getDescription($this->content));

        $this->content->description = 'Percipit "mollis" at scriptorem usu.';
        $this->assertEquals(
            'Percipit &quot;mollis&quot; at scriptorem usu.',
            $this->contentHelper->getDescription($this->content)
        );
    }

    /**
     * Tests getProperty when a content is already provided as parameter.
     */
    public function testGetProperty()
    {
        $this->content->wobble = 'wubble';

        $this->assertNull($this->contentHelper->getProperty($this->content, 'corge'));
        $this->assertEquals('wubble', $this->contentHelper->getProperty($this->content, 'wobble'));
    }

    /**
     * Tests getId.
     */
    public function testGetId()
    {
        $this->assertEmpty($this->contentHelper->getId($this->content));

        $this->content->pk_content = 690;

        $this->assertEquals(690, $this->contentHelper->getId($this->content));
    }

    /**
     * Tests getPublicationDate.
     */
    public function testGetPublicationDate()
    {
        $this->assertTrue(new \Datetime() <= $this->contentHelper->getPublicationDate(new Content()));

        $this->content->created   = new \Datetime('2010-10-10 10:00:00');
        $this->content->starttime = null;

        $this->assertEquals(
            new \Datetime('2010-10-10 10:00:00'),
            $this->contentHelper->getPublicationDate($this->content)
        );

        $this->content->created   = new \Datetime('2010-10-10 10:00:00');
        $this->content->starttime = new \Datetime('2010-10-10 20:00:00');

        $this->assertEquals(
            new \Datetime('2010-10-10 20:00:00'),
            $this->contentHelper->getPublicationDate($this->content)
        );
    }

    /**
     * Tests getPretitle.
     */
    public function testGetPretitle()
    {
        $this->assertNull($this->contentHelper->getPretitle($this->content));

        $this->content->pretitle = 'His ridens eu sed quod ignota.';
        $this->assertEquals('His ridens eu sed quod ignota.', $this->contentHelper->getPretitle($this->content));

        $this->content->pretitle = 'Percipit "mollis" at scriptorem usu.';
        $this->assertEquals(
            'Percipit &quot;mollis&quot; at scriptorem usu.',
            $this->contentHelper->getPretitle($this->content)
        );
    }

    /**
     * Tests getProperty when the item is extracted from template and it is
     * not a content.
     */
    public function testGetPropertyFromTemplateWhenNoContent()
    {
        $this->template->expects($this->once())->method('getValue')
            ->with('item')->willReturn(null);

        $this->assertNull($this->contentHelper->getProperty(null, 'flob'));
    }

    /**
     * Tests getScheduling state.
     */
    public function testGetSchedulingState()
    {
        $this->content->starttime = null;

        $this->assertEquals(\Content::NOT_SCHEDULED, $this->contentHelper->getSchedulingState($this->content));

        $this->content->starttime = new \Datetime('2020-01-01 00:00:00');

        $this->assertEquals(\Content::IN_TIME, $this->contentHelper->getSchedulingState($this->content));

        $this->content->endtime = new \Datetime('2020-01-02 00:00:00');

        $this->assertEquals(\Content::DUED, $this->contentHelper->getSchedulingState($this->content));

        $this->content->starttime = new \DateTime('9999-01-02 00:00:00');
        $this->content->endtime   = null;

        $this->assertEquals(\Content::POSTPONED, $this->contentHelper->getSchedulingState($this->content));
    }

    /**
     * Tests getSuggested.
     */
    public function testGetSuggested()
    {
        $this->cache->expects($this->at(0))->method('get')
            ->with('suggested_contents_article_2')
            ->willReturn([ new Content([ 'pk_content' => 2 ]) ]);

        $this->assertEquals(
            [ new Content([ 'pk_content' => 2 ]) ],
            $this->contentHelper->getSuggested('article', 2, 1)
        );

        $this->em->expects($this->at(0))->method('findBy')
            ->will($this->throwException(new \Exception()));

        $this->assertEquals([], $this->contentHelper->getSuggested('article', 2, 1));

        $this->em->expects($this->at(0))->method('findBy')
            ->willReturn([ new Content([ 'pk_content' => 2 ]) ]);

        $this->assertEquals(
            [ new Content([ 'pk_content' => 2 ]) ],
            $this->contentHelper->getSuggested('article', 2, 1)
        );
    }

    /**
     * Tests getSummary.
     */
    public function testGetSummary()
    {
        $this->assertNull($this->contentHelper->getSummary($this->content));

        $this->content->summary = 'His ridens eu sed quod ignota.';
        $this->assertEquals('His ridens eu sed quod ignota.', $this->contentHelper->getSummary($this->content));

        $this->content->summary = 'Percipit "mollis" at scriptorem usu.';
        $this->assertEquals(
            'Percipit "mollis" at scriptorem usu.',
            $this->contentHelper->getSummary($this->content)
        );

        $this->content->content_type_name = 'opinion';
        $this->content->description       = 'Lorem ipsum, dolor sit amet consectetur';
        $this->assertEquals(
            'Lorem ipsum, dolor sit amet consectetur',
            $this->contentHelper->getSummary($this->content)
        );
    }

    /**
     * Tests getTags.
     */
    public function testGetTags()
    {
        $this->assertEquals([], $this->contentHelper->getTags($this->content));

        $this->content->tags = [];
        $this->assertEquals([], $this->contentHelper->getTags($this->content));

        $tags = [ new Tag([ 'id' => 917 ]), new Tag([ 'id' => 837 ]) ];

        $this->ts->expects($this->once())->method('getListByIds')
            ->with([ 971, 837 ])
            ->willReturn([ 'items' => $tags ]);

        $this->content->tags = [ 971, 837 ];
        $this->assertEquals($tags, $this->contentHelper->getTags($this->content));
    }

    /**
     * Tests getTags when there is an error.
     */
    public function testGetTagsWhenError()
    {
        $this->content->tags = [ new Tag([ 'id' => 917 ]), new Tag([ 'id' => 837 ]) ];

        $this->ts->expects($this->once())->method('getListByIds')
            ->with($this->content->tags)
            ->will($this->throwException(new \Exception()));

        $this->assertEquals([], $this->contentHelper->getTags($this->content));
    }

    /**
     * Tests getTitle.
     */
    public function testGetTitle()
    {
        $this->assertNull($this->contentHelper->getTitle($this->content));

        $this->content->title = 'His ridens eu sed quod ignota.';
        $this->assertEquals('His ridens eu sed quod ignota.', $this->contentHelper->getTitle($this->content));

        $this->content->title = 'Percipit "mollis" at scriptorem usu.';
        $this->assertEquals(
            'Percipit &quot;mollis&quot; at scriptorem usu.',
            $this->contentHelper->getTitle($this->content)
        );
    }

    /**
     * Tests getType.
     */
    public function testGetType()
    {
        $this->assertNull($this->contentHelper->getType(new Content([ 'flob' => 'wibble' ])));

        $this->content->content_type_name = 'article';
        $this->assertEquals('article', $this->contentHelper->getType($this->content));

        $this->content->content_type_name = 'static_page';
        $this->assertEquals('Static page', $this->contentHelper->getType($this->content, true));
    }

    /**
     * Tests hasBody.
     */
    public function testHasBody()
    {
        $this->helper->expects($this->at(0))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse($this->contentHelper->hasBody($this->content));

        $this->content->body = 'Percipit "mollis" at scriptorem usu.';

        $this->assertFalse($this->contentHelper->hasBody($this->content));
        $this->assertTrue($this->contentHelper->hasBody($this->content));
    }

    /**
     * Tests hasCaption for objects and for items as related content.
     */
    public function testHasCaption()
    {
        $item = new Content([
            'content_status' => 1,
            'starttime'      => new \Datetime('2000-01-01 00:00:00')
        ]);

        $this->assertFalse($this->contentHelper->hasCaption($item));
        $this->assertTrue($this->contentHelper->hasCaption([
            'item'     => $item,
            'caption'  => 'Suas sonet appellantur patrioque',
            'position' => 365
        ]));
    }

    /**
     * Tests hasCommentsEnabled.
     */
    public function testHasCommentsEnabled()
    {
        $item = new Content([
            'with_comment'   => true,
            'content_status' => 1,
            'starttime'      => new \Datetime('2000-01-01 00:00:00')
            ]);

        $this->assertTrue($this->contentHelper->hasCommentsEnabled($item));
    }

    /**
     * Tests hasDescription.
     */
    public function testHasDescription()
    {
        $this->assertFalse($this->contentHelper->hasDescription($this->content));

        $this->content->description = 'Percipit "mollis" at scriptorem usu.';
        $this->assertTrue($this->contentHelper->hasDescription($this->content));
    }

    /**
     * Tests hasPretitle.
     */
    public function testHasPretitle()
    {
        $this->helper->expects($this->at(0))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse($this->contentHelper->hasPretitle($this->content));

        $this->content->pretitle = 'Percipit "mollis" at scriptorem usu.';
        $this->assertFalse($this->contentHelper->hasPretitle($this->content));
        $this->assertTrue($this->contentHelper->hasPretitle($this->content));
    }

    /**
     * Tests hasSummary.
     */
    public function testHasSummary()
    {
        $this->helper->expects($this->at(0))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse($this->contentHelper->hasSummary($this->content));

        $this->content->summary = 'Percipit "mollis" at scriptorem usu.';
        $this->assertFalse($this->contentHelper->hasSummary($this->content));
        $this->assertTrue($this->contentHelper->hasSummary($this->content));
    }

    /**
     * Tests hasTags.
     */
    public function testHasTags()
    {
        $this->assertFalse($this->contentHelper->hasTags($this->content));

        $this->content->tags = [];
        $this->assertFalse($this->contentHelper->hasTags($this->content));

        $tags = [ new Tag([ 'id' => 917 ]), new Tag([ 'id' => 837 ]) ];

        $this->ts->expects($this->once())->method('getListByIds')
            ->with([ 971, 837 ])
            ->willReturn([ 'items' => $tags ]);

        $this->content->tags = [ 971, 837 ];
        $this->assertTrue($this->contentHelper->hasTags($this->content));
    }

    /**
     * Tests hasTitle.
     */
    public function testHasTitle()
    {
        $this->helper->expects($this->at(0))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse($this->contentHelper->hasTitle($this->content));

        $this->content->title = 'Percipit "mollis" at scriptorem usu.';
        $this->assertFalse($this->contentHelper->hasTitle($this->content));
        $this->assertTrue($this->contentHelper->hasTitle($this->content));
    }

    /**
     * Tests isSuggested.
     */
    public function testIsSuggested()
    {
        $this->content->frontpage = 1;
        $this->assertTrue($this->contentHelper->isSuggested($this->content));

        $this->content->frontpage = 0;
        $this->assertFalse($this->contentHelper->isSuggested($this->content));
    }

    /**
     * Tests isLiveBlog.
     */
    public function testIsLiveBlog()
    {
        $this->assertFalse($this->contentHelper->isLiveBlog($this->content));
    }

    /**
     * Tests getLastLiveUpdate.
     */
    public function testGetLastLiveUpdate()
    {
        $this->assertNull($this->contentHelper->getLastLiveUpdate($this->content));

        $timezone = $this->locale->getTimeZone();
        $now      = new \DateTime(null, $timezone);

        $this->content->coverage_start_time = $now;
        $this->content->coverage_end_time   = $now;
        $this->content->live_blog_posting   = 1;

        $this->content->live_blog_updates = [
            [
                'modified' => $now
            ]
        ];

        $this->assertEquals($now, $this->contentHelper->getLastLiveUpdate($this->content));
    }

    /**
     * Tests isLive.
     */
    public function testIsLive()
    {
        $this->assertFalse($this->contentHelper->isLive($this->content));

        $timezone = $this->locale->getTimeZone();
        $now      = new \DateTime(null, $timezone);

        $this->content->coverage_start_time = $now;

        $tomorrow = new \DateTime(null, $timezone);
        $tomorrow->setTimestamp(strtotime('+1 day', $now->getTimestamp()));

        $this->content->coverage_end_time = $tomorrow;

        $this->assertTrue($this->contentHelper->isLive($this->content));
    }

    /**
     * Tests hasLiveUpdates.
     */
    public function testHasliveUpdates()
    {
        $this->assertFalse($this->contentHelper->hasLiveUpdates($this->content));
    }
}
