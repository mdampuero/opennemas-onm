<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Functions;

use Common\Model\Entity\Content;
use Common\Model\Entity\User;

/**
 * Defines test cases for categories functions.
 */
class AuthorFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->item = new Content([
            'fk_author' => 1
        ]);

        $this->author = new User(
            [
                'id'            => 1,
                'avatar_img_id' => 2,
                'bio_body'      => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quod, atque.',
                'bio_summary'   => 'Lorem, ipsum.',
                'name'          => 'Baz Glorp',
                'slug'          => 'baz-glorp'
            ]
        );

        $this->helper = $this->getMockBuilder('Common\Core\Component\Helper\AuthorHelper')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getAuthor',
                    'getAuthorAvatar',
                    'getAuthorBioBody',
                    'getAuthorBioSummary',
                    'getAuthorId',
                    'getAuthorName',
                    'getAuthorRssUrl',
                    'getAuthorSlug',
                    'getAuthorSocialFacebookUrl',
                    'getAuthorSocialTwitterUrl',
                    'getAuthorUrl',
                    'hasAuthor',
                    'hasAuthorAvatar',
                    'hasAuthorBioBody',
                    'hasAuthorBioSummary',
                    'hasAuthorRssUrl',
                    'hasAuthorSlug',
                    'hasAuthorSocialFacebookUrl',
                    'hasAuthorSocialTwitterUrl',
                    'hasAuthorUrl',
                    'isBlog'
                ]
            )
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->with('core.helper.author')
            ->willReturn($this->helper);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Tests get_author.
     */
    public function testGetAuthor()
    {
        $this->helper->expects($this->once())->method('getAuthor')
            ->with($this->item)
            ->willReturn($this->author);

        $this->assertEquals($this->author, get_author($this->item));
    }

    /**
     * Tests get_author_avatar.
     */
    public function testGetAuthorAvatar()
    {
        $this->helper->expects($this->once())->method('getAuthorAvatar')
            ->with($this->item)
            ->willReturn($this->author->avatar_img_id);

        $this->assertEquals(2, get_author_avatar($this->item));
    }

    /**
     * Tests get_author_bio_body.
     */
    public function testGetAuthorBioBody()
    {
        $this->helper->expects($this->once())->method('getAuthorBioBody')
            ->with($this->item)
            ->willReturn($this->author->bio_body);

        $this->assertEquals(
            'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quod, atque.',
            get_author_bio_body($this->item)
        );
    }

    /**
     * Tests get_author_bio_summary.
     */
    public function testGetAuthorBioSummary()
    {
        $this->helper->expects($this->once())->method('getAuthorBioSummary')
            ->with($this->item)
            ->willReturn($this->author->bio_summary);

        $this->assertEquals('Lorem, ipsum.', get_author_bio_summary($this->item));
    }

    /**
     * Tests get_author_id.
     */
    public function testGetAuthorId()
    {
        $this->helper->expects($this->once())->method('getAuthorId')
            ->with($this->item)
            ->willReturn($this->author->id);

        $this->assertEquals(1, get_author_id($this->item));
    }

    /**
     * Tests get_author_name.
     */
    public function testGetAuthorName()
    {
        $this->helper->expects($this->once())->method('getAuthorName')
            ->with($this->item)
            ->willReturn($this->author->name);

        $this->assertEquals('Baz Glorp', get_author_name($this->item));
    }

    /**
     * Tests get_author_rss_url.
     */
    public function testGetAuthorRssUrl()
    {
        $this->helper->expects($this->once())->method('getAuthorRssUrl')
            ->with($this->item)
            ->willReturn('/rss/author/baz-glorp');

        $this->assertEquals('/rss/author/baz-glorp', get_author_rss_url($this->item));
    }

    /**
     * Tests get_author_slug.
     */
    public function testGetAuthorSlug()
    {
        $this->helper->expects($this->once())->method('getAuthorSlug')
            ->with($this->item)
            ->willReturn($this->author->slug);

        $this->assertEquals($this->author->slug, get_author_slug($this->item));
    }

    /**
     * Tests get_author_social_facebook_url.
     */
    public function testGetAuthorSocialFacebookUrl()
    {
        $this->helper->expects($this->once())->method('getAuthorSocialFacebookUrl')
            ->with($this->item)
            ->willReturn('https://facebook.com/baz-glorp');

        $this->assertEquals('https://facebook.com/baz-glorp', get_author_social_facebook_url($this->item));
    }

    /**
     * Tests get_author_twitter_url.
     */
    public function testGetAuthorTwitterUrl()
    {
        $this->helper->expects($this->once())->method('getAuthorSocialTwitterUrl')
            ->with($this->item)
            ->willReturn('https://twitter.com/baz-glorp');

        $this->assertEquals('https://twitter.com/baz-glorp', get_author_social_twitter_url($this->item));
    }

    /**
     * Tests get_author_url.
     */
    public function testGetAuthorUrl()
    {
        $this->helper->expects($this->once())->method('getAuthorUrl')
            ->with($this->item)
            ->willReturn('https://opennemas.com/baz-glorp');

        $this->assertEquals('https://opennemas.com/baz-glorp', get_author_url($this->item));
    }

    /**
     * Tests has_author.
     */
    public function testHasAuthor()
    {
        $this->helper->expects($this->once())->method('hasAuthor')
            ->with($this->item)
            ->willReturn(true);

        $this->assertTrue(has_author($this->item));
    }

    /**
     * Tests has_author_avatar.
     */
    public function testHasAuthorAvatar()
    {
        $this->helper->expects($this->once())->method('hasAuthorAvatar')
            ->with($this->item)
            ->willReturn(true);

        $this->assertTrue(has_author_avatar($this->item));
    }

    /**
     * Tests has_author_bio_body.
     */
    public function testHasAuthorBioBody()
    {
        $this->helper->expects($this->once())->method('hasAuthorBioBody')
            ->with($this->item)
            ->willReturn(true);

        $this->assertTrue(has_author_bio_body($this->item));
    }

    /**
     * Tests has_author_bio_summary.
     */
    public function testHasAuthorBioSummary()
    {
        $this->helper->expects($this->once())->method('hasAuthorBioSummary')
            ->with($this->item)
            ->willReturn(true);

        $this->assertTrue(has_author_bio_summary($this->item));
    }

    /**
     * Tests has_author_rss_url.
     */
    public function testHasAuthorRssUrl()
    {
        $this->helper->expects($this->once())->method('hasAuthorRssUrl')
            ->with($this->item)
            ->willReturn(true);

        $this->assertTrue(has_author_rss_url($this->item));
    }

    /**
     * Tests has_author_slug.
     */
    public function testHasAuthorSlug()
    {
        $this->helper->expects($this->once())->method('hasAuthorSlug')
            ->with($this->item)
            ->willReturn(true);

        $this->assertTrue(has_author_slug($this->item));
    }

    /**
     * Tests has_author_social_facebook_url.
     */
    public function testHasAuthorSocialFacebookUrl()
    {
        $this->helper->expects($this->once())->method('hasAuthorSocialFacebookUrl')
            ->with($this->item)
            ->willReturn(true);

        $this->assertTrue(has_author_social_facebook_url($this->item));
    }

    /**
     * Tests has_author_social_twitter_url.
     */
    public function testHasAuthorSocialTwitterUrl()
    {
        $this->helper->expects($this->once())->method('hasAuthorSocialTwitterUrl')
            ->with($this->item)
            ->willReturn(true);

        $this->assertTrue(has_author_social_twitter_url($this->item));
    }

    /**
     * Tests has_author_url.
     */
    public function testHasAuthorUrl()
    {
        $this->helper->expects($this->once())->method('hasAuthorUrl')
            ->with($this->item)
            ->willReturn(true);

        $this->assertTrue(has_author_url($this->item));
    }

    /**
     * Tests is_blog.
     */
    public function testIsBlog()
    {
        $this->helper->expects($this->once())->method('isBlog')
            ->with($this->item)
            ->willReturn(true);

        $this->assertTrue(is_blog($this->item));
    }
}
