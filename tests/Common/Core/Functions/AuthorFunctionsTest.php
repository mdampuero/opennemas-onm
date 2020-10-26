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
        $this->as = $this->getMockBuilder('Api\Service\V1\AuthorService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->fm = $this->getMockBuilder('Common\Data\Core\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->ugh = $this->getMockBuilder('Common\Component\Helper\UrlGenerator')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->fm->expects($this->any())->method('set')
            ->willReturn($this->fm);
        $this->fm->expects($this->any())->method('filter')
            ->willReturn($this->fm);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

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
            case 'api.service.author':
                return $this->as;

            case 'core.template.frontend':
                return $this->template;

            case 'data.manager.filter':
                return $this->fm;

            case 'core.helper.url_generator':
                return $this->ugh;

            case 'router':
                return $this->router;

            default:
                return null;
        }
    }

    /**
     * Tests get_author when a author is already provided as parameter.
     */
    public function testGetAuthorFromParameterWhenAuthor()
    {
        $author = new User([ 'id' => 20 ]);

        $this->assertEquals($author, get_author($author));
    }

    /**
     * Tests get_author when a content is provided as parameter.
     */
    public function testGetAuthorFromParameterWhenContent()
    {
        $author  = new User([ 'id' => 20 ]);
        $content = new \Content();

        $content->fk_author = 20;

        $this->as->expects($this->once())->method('getItem')
            ->with(20)->willReturn($author);

        $this->assertEquals($author, get_author($content));
    }

    /**
     * Tests get_author when an error is thrown while searching the author.
     */
    public function testGetAuthorFromParameterWhenError()
    {
        $content = new \Content();

        $content->fk_author = 20;

        $this->as->expects($this->once())->method('getItem')
            ->with(20)->will($this->throwException(new \Exception()));

        $this->assertNull(get_author($content));
    }

    /**
     * Tests get_author when no content is provided as parameter.
     */
    public function testGetAuthorFromParameterWhenNoContent()
    {
        $this->assertNull(get_author('corge'));
        $this->assertNull(get_author(709));
        $this->assertNull(get_author(null));
    }

    /**
     * Tests get_author when the item is extracted from template and it is a
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

        $this->assertEquals($author, get_author());
    }

    /**
     * Tests get_author when the item is extracted from template and it is
     * not a content.
     */
    public function testGetAuthorFromTemplateWhenNoContent()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('item')->willReturn(445);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('item')->willReturn('thud');

        $this->assertNull(get_author());
        $this->assertNull(get_author());
    }

    /**
     * Tests get_author_id.
     */
    public function testGetAuthorId()
    {
        $this->assertNull(get_author_id(131));
        $this->assertEquals(2, get_author_id(new User([
            'id'   => 2,
            'name' => 'Michelle Price'
        ])));
    }

    /**
     * Tests get_author_avatar.
     */
    public function testGetAuthorAvatar()
    {
        $this->assertNull(get_author_avatar(131));
        $this->assertEquals(593, get_author_avatar(new User([
            'name'          => 'Michelle Price',
            'avatar_img_id' => 593
        ])));
    }

    /**
     * Tests get_author_name.
     */
    public function testGetAuthorName()
    {
        $this->assertNull(get_author_name(131));

        $this->assertEquals('Michelle Price', get_author_name(new User([
            'name' => 'Michelle Price'
        ])));

        $this->assertEquals('Daniel Robertson', get_author_name(new Content([
            'agency' => 'Daniel Robertson'
        ])));

        $this->assertEquals('Daniel Robertson', get_author_name(new Content([
            'author_name' => 'Daniel Robertson'
        ])));
    }

    /**
     * Tests get_author_slug.
     */
    public function testGetAuthorSlug()
    {
        $this->assertNull(get_author_slug(131));
        $this->assertEquals('michelle-price', get_author_slug(new User([
            'slug' => 'michelle-price'
        ])));
    }

    /**
     * Tests get_author_url.
     */
    public function testGetAuthorUrlWhenAuthor()
    {
        $author = new User([
            'name' => 'gorp',
            'slug' => 'gorp',
        ]);

        $this->fm->expects($this->any())->method('get')
            ->willReturn('gorp');

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_author_frontpage', [ 'slug' => 'gorp' ])
            ->willReturn('/author/gorp');

        $this->assertNull(get_author_url(131));

        $this->assertEquals('/author/gorp', get_author_url($author));
    }

    /**
     * Tests get_author_url.
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

        $this->assertEquals('/foo/glork', get_author_url($content));
    }

    /**
     * Tests get_author_rss_url.
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

        $this->assertNull(get_author_rss_url(131));
        $this->assertEquals('rss/author/vera-willis', get_author_rss_url($author));
    }

    /**
     * Tests get_author_bio_summary.
     */
    public function testGetAuthorBioSummary()
    {
        $this->assertNull(get_author_bio_summary(131));
        $this->assertEquals('Journalist', get_author_bio_summary(new User([
            'name' => 'Michelle Price',
            'bio'  => 'Journalist'
        ])));
    }

    /**
     * Tests get_author_bio_body.
     */
    public function testGetAuthorBioBody()
    {
        $this->assertNull(get_author_bio_body(131));
        $this->assertEquals('Journalist', get_author_bio_body(new User([
            'name'            => 'Michelle Price',
            'bio_description' => 'Journalist'
        ])));
    }

    /**
     * Tests get_author_social_twitter_url.
     */
    public function testGetAuthorSocialTwitterUrl()
    {
        $this->assertNull(get_author_social_twitter_url(131));
        $this->assertEquals('https://www.twitter.com/@MichellePrice', get_author_social_twitter_url(new User([
            'name'    => 'Michelle Price',
            'twitter' => '@MichellePrice'
        ])));
    }

    /**
     * Tests get_author_social_facebook_url.
     */
    public function testGetAuthorSocialFacebookUrl()
    {
        $this->assertNull(get_author_social_facebook_url(131));
        $this->assertEquals('https://www.facebook.com/MichellePrice', get_author_social_facebook_url(new User([
            'name'    => 'Michelle Price',
            'facebook' => 'MichellePrice'
        ])));
    }

    /**
     * Tests is_blog.
     */
    public function testIsBlog()
    {
        $this->assertFalse(is_blog(131));
        $this->assertTrue(is_blog(new User([
            'name'    => 'Michelle Price',
            'is_blog' => 1
        ])));
    }

    /**
     * Tests has_author.
     */
    public function testHasAuthor()
    {
        $this->assertFalse(has_author(131));

        $this->assertTrue(has_author(new User([
            'name'          => 'Michelle Price',
            'avatar_img_id' => 593
        ])));

        $this->assertTrue(has_author(new Content([
            'agency' => 'Michelle Price',
        ])));
    }

    /**
     * Tests has_author_avatar.
     */
    public function testHasAuthorAvatar()
    {
        $this->assertFalse(has_author_avatar(131));
        $this->assertTrue(has_author_avatar(new User([
            'name'          => 'Michelle Price',
            'avatar_img_id' => 593
        ])));
    }

    /**
     * Tests has_author_slug.
     */
    public function testHasAuthorSlug()
    {
        $this->fm->expects($this->any())->method('get')
            ->willReturn('michelle-price');

        $this->assertFalse(has_author_slug(131));
        $this->assertTrue(has_author_slug(new User([
            'name' => 'Michelle Price',
            'slug' => 'michelle-price'
        ])));
    }

    /**
     * Tests has_author_url.
     */
    public function testHasAuthorUrl()
    {
        $author = new User([
            'name' => 'michelle price',
            'slug' => 'michelle-price',
        ]);

        $this->fm->expects($this->any())->method('get')
            ->willReturn('michelle-price');

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_author_frontpage', [ 'slug' => 'michelle-price' ])
            ->willReturn('/author/michelle-price');

        $this->assertFalse(has_author_url(131));
        $this->assertTrue(has_author_url($author));
    }

    /**
     * Tests has_author_rss_url.
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

        $this->assertFalse(has_author_rss_url(131));
        $this->assertTrue(has_author_rss_url($author));
    }

    /**
     * Tests has_author_bio_summary.
     */
    public function testHasAuthorBioSummary()
    {
        $this->assertFalse(has_author_bio_summary(131));
        $this->assertTrue(has_author_bio_summary(new User([
            'name' => 'Michelle Price',
            'bio'  => 'Journalist'
        ])));
    }

    /**
     * Tests has_author_bio_body.
     */
    public function testHasAuthorBioBody()
    {
        $this->assertFalse(has_author_bio_body(131));
        $this->assertTrue(has_author_bio_body(new User([
            'name'            => 'Michelle Price',
            'bio_description' => 'Journalist and writer'
        ])));
    }

    /**
     * Tests has_author_social_twitter_url.
     */
    public function testHasAuthorTwitterUrl()
    {
        $this->assertFalse(has_author_social_twitter_url(131));
        $this->assertTrue(has_author_social_twitter_url(new User([
            'name'    => 'Michelle Price',
            'twitter' => '@MichellePrice'
        ])));
    }

    /**
     * Tests has_author_social_facebook_url.
     */
    public function testHasAuthorFacebookUrl()
    {
        $this->assertFalse(has_author_social_facebook_url(131));
        $this->assertTrue(has_author_social_facebook_url(new User([
            'name'     => 'Michelle Price',
            'facebook' => 'MichellePrice'
        ])));
    }
}
