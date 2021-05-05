<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\AuthorHelper;
use Common\Model\Entity\Content;
use Common\Model\Entity\User;

/**
 * Defines test cases for ContentMediaHelper class.
 */
class AuthorHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->as = $this->getMockBuilder('Api\Service\V1\AuthorService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->ugh = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->helper = new AuthorHelper($this->as, $this->router, $this->template, $this->ugh);
    }


    /**
     * Tests getAuthor when a author is already provided as parameter.
     */
    public function testGetAuthorFromParameterWhenAuthor()
    {
        $author = new User([ 'id' => 20 ]);

        $this->assertEquals($author, $this->helper->getAuthor($author));
    }

    /**
     * Tests getAuthor when a content is provided as parameter.
     */
    public function testGetAuthorFromParameterWhenContent()
    {
        $author  = new User([ 'id' => 20 ]);
        $content = new \Content();

        $content->fk_author = 20;

        $this->as->expects($this->once())->method('getItem')
            ->with(20)->willReturn($author);

        $this->assertEquals($author, $this->helper->getAuthor($content));
    }

    /**
     * Tests getAuthor when an error is thrown while searching the author.
     */
    public function testGetAuthorFromParameterWhenError()
    {
        $content = new \Content();

        $content->fk_author = 20;

        $this->as->expects($this->once())->method('getItem')
            ->with(20)->will($this->throwException(new \Exception()));

        $this->assertNull($this->helper->getAuthor($content));
    }

    /**
     * Tests getAuthor when no content is provided as parameter.
     */
    public function testGetAuthorFromParameterWhenNoContent()
    {
        $this->assertNull($this->helper->getAuthor('corge'));
        $this->assertNull($this->helper->getAuthor(709));
        $this->assertNull($this->helper->getAuthor(null));
    }

    /**
     * Tests getAuthor when the item is extracted from template and it is a
     * content.
     */
    public function testGetAuthorFromTemplateWhenContent()
    {
        $author  = new User([ 'id' => 20 ]);
        $content = new Content([ 'fk_author' => 20 ]);

        $this->template->expects($this->once())->method('getValue')
            ->with('item')->willReturn($content);

        $this->as->expects($this->once())->method('getItem')
            ->with(20)->willReturn($author);

        $this->assertEquals($author, $this->helper->getAuthor());
    }

    /**
     * Tests getAuthor when the item is extracted from template and it is
     * not a content.
     */
    public function testGetAuthorFromTemplateWhenNoContent()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('item')->willReturn(445);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('item')->willReturn('thud');

        $this->assertNull($this->helper->getAuthor());
        $this->assertNull($this->helper->getAuthor());
    }

    /**
     * Tests getAuthorId.
     */
    public function testGetAuthorId()
    {
        $this->assertNull($this->helper->getAuthorId(131));
        $this->assertEquals(2, $this->helper->getAuthorId(new User([
            'id'   => 2,
            'name' => 'Michelle Price'
        ])));
    }

    /**
     * Tests getAuthorAvatar.
     */
    public function testGetAuthorAvatar()
    {
        $this->assertNull($this->helper->getAuthorAvatar(131));
        $this->assertEquals(593, $this->helper->getAuthorAvatar(new User([
            'name'          => 'Michelle Price',
            'avatar_img_id' => 593
        ])));
    }

    /**
     * Tests getAuthorName.
     */
    public function testGetAuthorName()
    {
        $this->assertNull($this->helper->getAuthorName(131));

        $this->assertEquals('Michelle Price', $this->helper->getAuthorName(new User([
            'name' => 'Michelle Price'
        ])));

        $this->assertEquals('Daniel Robertson', $this->helper->getAuthorName(new Content([
            'agency' => 'Daniel Robertson'
        ])));

        $this->assertEquals('Daniel Robertson', $this->helper->getAuthorName(new Content([
            'author_name' => 'Daniel Robertson'
        ])));
    }

    /**
     * Tests getAuthorSlug.
     */
    public function testGetAuthorSlug()
    {
        $this->assertNull($this->helper->getAuthorSlug(131));
        $this->assertEquals('michelle-price', $this->helper->getAuthorSlug(new User([
            'slug' => 'michelle-price'
        ])));
    }

    /**
     * Tests getAuthorUrl.
     */
    public function testGetAuthorUrlWhenAuthor()
    {
        $author = new User([
            'name' => 'gorp',
            'slug' => 'gorp',
        ]);

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_author_frontpage', [ 'slug' => 'gorp' ])
            ->willReturn('/author/gorp');

        $this->assertNull($this->helper->getAuthorUrl(131));

        $this->assertEquals('/author/gorp', $this->helper->getAuthorUrl($author));
    }

    /**
     * Tests getAuthorUrl.
     */
    public function testGetAuthorUrlWhenContent()
    {
        $content = new Content([
            'content_type_name' => 'opinion',
            'fk_author' => 20,
            'is_blog'   => 1
        ]);

        $author = new User([
            'id'   => 20,
            'name' => 'glork',
            'slug' => 'glork',
        ]);

        $this->as->expects($this->once())->method('getItem')
            ->with(20)->willReturn($author);

        $this->ugh->expects($this->once())->method('generate')
            ->with($author)->willReturn('/foo/glork');

        $this->assertEquals('/foo/glork', $this->helper->getAuthorUrl($content));
    }

    /**
     * Tests getAuthorRssUrl.
     */
    public function testGetAuthorRssUrl()
    {
        $author = new User([
            'id'    => 20,
            'name'  => 'Vera Willis',
            'slug'  => 'vera-willis',
            'inrss' => 1,
        ]);

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_rss_author', [ 'author_slug' => 'vera-willis' ])
            ->willReturn('rss/author/vera-willis');

        $this->assertNull($this->helper->getAuthorRssUrl(131));
        $this->assertEquals('rss/author/vera-willis', $this->helper->getAuthorRssUrl($author));
    }

    /**
     * Tests getAuthorBioSummary.
     */
    public function testGetAuthorBioSummary()
    {
        $this->assertNull($this->helper->getAuthorBioSummary(131));
        $this->assertEquals('Journalist', $this->helper->getAuthorBioSummary(new User([
            'name' => 'Michelle Price',
            'bio'  => 'Journalist'
        ])));
    }

    /**
     * Tests getAuthorBioBody.
     */
    public function testGetAuthorBioBody()
    {
        $this->assertNull($this->helper->getAuthorBioBody(131));
        $this->assertEquals('Journalist', $this->helper->getAuthorBioBody(new User([
            'name'            => 'Michelle Price',
            'bio_description' => 'Journalist'
        ])));
    }

    /**
     * Tests getAuthorSocialTwitterUrl.
     */
    public function testGetAuthorSocialTwitterUrl()
    {
        $user = new User([ 'name'    => 'Michelle Price', 'twitter' => '@MichellePrice']);

        $this->assertNull($this->helper->getAuthorSocialTwitterUrl(131));
        $this->assertEquals('https://www.twitter.com/@MichellePrice', $this->helper->getAuthorSocialTwitterUrl($user));
    }

    /**
     * Tests getAuthorSocialFacebookUrl.
     */
    public function testGetAuthorSocialFacebookUrl()
    {
        $user = new User([
            'name'    => 'Michelle Price',
            'facebook' => 'MichellePrice'
        ]);

        $this->assertNull($this->helper->getAuthorSocialTwitterUrl(131));
        $this->assertEquals('https://www.facebook.com/MichellePrice', $this->helper->getAuthorSocialFacebookUrl($user));
    }

    /**
     * Tests isBlog.
     */
    public function testIsBlog()
    {
        $this->assertFalse($this->helper->isBlog(131));
        $this->assertTrue($this->helper->isBlog(new User([
            'name'    => 'Michelle Price',
            'is_blog' => 1
        ])));
    }

    /**
     * Tests hasAuthor.
     */
    public function testHasAuthor()
    {
        $this->assertFalse($this->helper->hasAuthor(131));

        $author = new User([
            'name'          => 'Michelle Price',
            'avatar_img_id' => 593
        ]);

        $this->assertTrue($this->helper->hasAuthor($author));

        $item = new Content([
            'fk_author' => 1
        ]);

        $this->as->expects($this->once())->method('getItem')
            ->with($item->fk_author)
            ->willReturn($author);

        $this->assertTrue($this->helper->hasAuthor($item));
    }

    /**
     * Tests hasAuthorAvatar.
     */
    public function testHasAuthorAvatar()
    {
        $this->assertFalse($this->helper->hasAuthorAvatar(131));
        $this->assertTrue($this->helper->hasAuthorAvatar(new User([
            'name'          => 'Michelle Price',
            'avatar_img_id' => 593
        ])));
    }

    /**
     * Tests hasAuthorSlugs.
     */
    public function testHasAuthorSlug()
    {
        $this->assertFalse($this->helper->hasAuthorSlug(131));
        $this->assertTrue($this->helper->hasAuthorSlug(new User([
            'name' => 'Michelle Price',
            'slug' => 'michelle-price'
        ])));
    }

    /**
     * Tests hasAuthorUrl.
     */
    public function testHasAuthorUrl()
    {
        $author = new User([
            'name' => 'michelle price',
            'slug' => 'michelle-price',
        ]);

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_author_frontpage', [ 'slug' => 'michelle-price' ])
            ->willReturn('/author/michelle-price');

        $this->assertFalse($this->helper->hasAuthorUrl(131));
        $this->assertTrue($this->helper->hasAuthorUrl($author));
    }

    /**
     * Tests hasAuthorRssUrl.
     */
    public function testHasAuthorRssUrl()
    {
        $author = new User([
            'id'    => 20,
            'name'  => 'Michelle Price',
            'slug'  => 'michelle-price',
            'inrss' => 1,
        ]);

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_rss_author', [ 'author_slug' => 'michelle-price' ])
            ->willReturn('rss/author/michelle-price');

        $this->assertFalse($this->helper->hasAuthorRssUrl(131));
        $this->assertTrue($this->helper->hasAuthorRssUrl($author));
    }

    /**
     * Tests hasAuthorBioSummary.
     */
    public function testHasAuthorBioSummary()
    {
        $this->assertFalse($this->helper->hasAuthorBioSummary(131));
        $this->assertTrue($this->helper->hasAuthorBioSummary(new User([
            'name' => 'Michelle Price',
            'bio'  => 'Journalist'
        ])));
    }

    /**
     * Tests hasAuthorBioBody.
     */
    public function testHasAuthorBioBody()
    {
        $this->assertFalse($this->helper->hasAuthorBioBody(131));
        $this->assertTrue($this->helper->hasAuthorBioBody(new User([
            'name'            => 'Michelle Price',
            'bio_description' => 'Journalist and writer'
        ])));
    }

    /**
     * Tests hasAuthorTwitterUrl.
     */
    public function testHasAuthorTwitterUrl()
    {
        $this->assertFalse($this->helper->hasAuthorSocialTwitterUrl(131));
        $this->assertTrue($this->helper->hasAuthorSocialTwitterUrl(new User([
            'name'    => 'Michelle Price',
            'twitter' => '@MichellePrice'
        ])));
    }

    /**
     * Tests hasAuthorSocialFacebookUrl.
     */
    public function testHasAuthorFacebookUrl()
    {
        $this->assertFalse($this->helper->hasAuthorSocialFacebookUrl(131));
        $this->assertTrue($this->helper->hasAuthorSocialFacebookUrl(new User([
            'name'     => 'Michelle Price',
            'facebook' => 'MichellePrice'
        ])));
    }
}
