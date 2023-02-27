<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\RelatedHelper;
use Common\Model\Entity\Content;

/**
 * Defines test cases for RelatedHelper class.
 */
class RelatedHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->content = new Content([
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods(['isReadyForPublish', 'getContent'])
            ->getMock();

        $this->subscriptionHelper = $this->getMockBuilder('Common\Core\Component\Helper\SubscriptionHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isHidden' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->helper = new RelatedHelper($this->contentHelper, $this->subscriptionHelper, $this->template);
    }

    /**
     * Tests getRelated method.
     */
    public function testGetRelated()
    {
        $articles = [ new Content([
            'id'             => 893,
            'content_status' => 1,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]), new Content([
            'id'             => 704,
            'content_status' => 1,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]) ];

        $this->assertEmpty($this->helper->getRelated($this->content, 'inner'));

        $this->content->related_contents = [ [
            'caption'           => 'Omnes possim dis mucius',
            'content_type_name' => 'article',
            'position'          => 1,
            'target_id'         => 893,
            'type'              => 'related_inner'
        ], [
            'caption'           => 'Ut erant arcu graeco',
            'content_type_name' => 'article',
            'position'          => 0,
            'target_id'         => 704,
            'type'              => 'related_inner'
        ] ];

        $this->assertEmpty($this->helper->getRelated($this->content, 'inner'));

        $this->contentHelper->expects($this->at(0))->method('getContent')
            ->with(704, 'article')
            ->willReturn($articles[1]);

        $this->contentHelper->expects($this->at(1))->method('getContent')
            ->with(893, 'article')
            ->willReturn($articles[0]);

        $related = $this->helper->getRelated($this->content, 'related_inner');

        $this->assertEquals($articles[1], $related[0]['item']);
        $this->assertEquals($articles[0], $related[1]['item']);
    }

    /**
     * Tests getRelated when for an external content
     */
    public function testGetRelatedForExternal()
    {
        $article                 = new \Content();
        $article->id             = 205;
        $article->content_status = 1;
        $article->starttime      = '2020-01-01 00:00:00';

        $this->template->expects($this->once())->method('getValue')
            ->with('related')->willReturn([ 205 => $article ]);

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->external          = 1;
        $content->related_contents  = [ [
            'content_type_name' => 'article',
            'target_id'         => 205,
            'type'              => 'related_inner',
        ] ];

        $this->assertEquals(
            [ $article ],
            $this->helper->getRelated($content, 'related_inner')
        );
    }

    /**
     * Tests getRelatedContents.
     */
    public function testGetRelatedContentsHere()
    {
        $article                 = new \Content();
        $article->id             = 205;
        $article->content_status = 1;
        $article->starttime      = '2020-01-01 00:00:00';

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->related_contents  = [ [
            'content_type_name' => 'article',
            'target_id'         => 205,
            'type'              => 'related_inner',
            'caption'           => null,
            'position'          => 2
        ] ];

        $this->contentHelper->expects($this->once())->method('getContent')
            ->willReturn($article);

        $this->assertEmpty($this->helper->getRelatedContents($content, 'mumble'));

        $this->assertEquals([ [
            'item'     => $article,
            'caption'  => null,
            'position' => 2
        ] ], $this->helper->getRelatedContents($content, 'inner'));
    }

    /**
     * Tests hasRelatedContents.
     */
    public function testHasRelatedContents()
    {
        $article                 = new \Content();
        $article->id             = 205;
        $article->content_status = 1;
        $article->starttime      = '2020-01-01 00:00:00';

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->related_contents  = [ [
            'content_type_name' => 'article',
            'target_id'         => 205,
            'type'              => 'related_inner',
            'caption'           => null,
            'position'          => 2
        ] ];

        $this->contentHelper->expects($this->at(0))->method('getContent')
            ->with(205, 'article')
            ->willReturn($article);

        $this->assertFalse($this->helper->hasRelatedContents($content, 'mumble'));
        $this->assertTrue($this->helper->hasRelatedContents($content, 'inner'));

        $this->contentHelper->expects($this->at(0))->method('getContent')
            ->willReturn($article);

        $this->subscriptionHelper->expects($this->at(0))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse($this->helper->hasRelatedContents($content, 'inner'));
    }

    /**
     * Tests HasRelatedContentsWithNoContent.
     */
    public function testHasRelatedContentsWithNoContent()
    {
        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->related_contents  = [ [
            'content_type_name' => 'article',
            'target_id'         => 205,
            'type'              => 'related_inner',
            'caption'           => null,
            'position'          => 2
        ] ];

        $this->contentHelper->expects($this->at(0))->method('getContent')
            ->willReturn(null);

        $this->assertFalse($this->helper->hasRelatedContents($content, 'mumble'));
        $this->assertFalse($this->helper->hasRelatedContents($content, 'inner'));

        $this->contentHelper->expects($this->at(0))->method('getContent')
            ->willReturn(null);

        $this->assertFalse($this->helper->hasRelatedContents($content, 'inner'));
    }

    /**
     * Tests hasRelatedContentswithRelatedObject.
     */
    public function testHasRelatedContentswithRelatedObject()
    {
        $photo = new \Content();

        $article                 = new \Content();
        $article->id             = 205;
        $article->content_status = 1;
        $article->starttime      = '2020-01-01 00:00:00';

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->externalRelated   = $photo;

        $this->assertTrue($this->helper->hasRelatedContents($content, 'mumble'));
        $this->assertTrue($this->helper->hasRelatedContents($content, 'inner'));

        $this->assertTrue($this->helper->hasRelatedContents($content, 'inner'));
    }
}
