<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\UrlGeneratorHelper;
use Common\Data\Core\FilterManager;
use Common\ORM\Entity\Category;

class UrlGeneratorHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    protected function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->um = $this->getMockBuilder('UserManager')
            ->setMethods([ 'find' ])
            ->getMock();

        $this->fm = new FilterManager($this->container);

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getMainDomain', 'hasMultilanguage' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'getContext', 'setContext' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods(['getSchemeAndHttpHost'])->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods(['getCurrentRequest'])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->instance->internal_name = 'opennemas';

        if (!defined('INSTANCE_UNIQUE_NAME')) {
            define('INSTANCE_UNIQUE_NAME', 'opennemas');
        }

        $this->urlGenerator = new UrlGeneratorHelper($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'data.manager.filter':
                return $this->fm;

            case 'user_repository':
                return $this->um;

            case 'core.instance':
                return $this->instance;

            case 'core.locale':
                return $this->locale;

            case 'request_stack':
                return $this->requestStack;

            case 'router':
                return $this->router;
        }

        return null;
    }

    /**
     * @covers \Common\Core\Component\Helper\UrlGeneratorHelper::__construct
     */
    public function testConstructor()
    {
        $property = new \ReflectionProperty($this->urlGenerator, 'container');
        $property->setAccessible(true);

        $this->assertEquals($this->container, $property->getValue($this->urlGenerator));
    }

    /**
     * Tests generate when the content provided has an external URI.
     */
    public function testGenerateForCategory()
    {
        $category = new Category([ 'name' => 'garply' ]);

        $this->router->expects($this->once())->method('generate')
            ->with('category_frontpage', [ 'category_name' => 'garply' ])
            ->willReturn('blog/section/garply');

        $this->assertEquals(
            '/blog/section/garply',
            $this->urlGenerator->generate($category)
        );
    }

    /**
     * Tests generate when the content provided has an external URI.
     */
    public function testGenerateForExternal()
    {
        $content = new \Content();

        $content->externalUri = 'http://baz.wobble/waldo/fred';

        $this->assertEquals(
            $content->externalUri,
            $this->urlGenerator->generate($content)
        );
    }

    /**
     * Tests generate when generating relative and absolute URLs basing on the
     * current instance.
     */
    public function testGenerateForInstance()
    {
        $content = new \Content();

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->setMethods([ 'getUriForContent' ])
            ->setConstructorArgs([ $this->container ])
            ->getMock();

        $this->instance->expects($this->any())->method('getMainDomain')
            ->willReturn('thud.opennemas.com');

        $this->requestStack->expects($this->any())->method('getCurrentRequest')
            ->willReturn(null);

        $helper->expects($this->any())->method('getUriForContent')
            ->with($content)->willReturn('wibble/fred');

        $this->assertEquals(
            '/wibble/fred',
            $helper->generate($content, [ 'absolute' => false  ])
        );

        $this->assertEquals(
            '//thud.opennemas.com/wibble/fred',
            $helper->generate($content, [ 'absolute' => true ])
        );

        $this->assertEquals(
            '/wibble/fred',
            $helper->forceHttp(true)->generate($content, [ 'absolute' => false  ])
        );

        $this->assertEquals(
            'http://thud.opennemas.com/wibble/fred',
            $helper->forceHttp(true)->generate($content, [ 'absolute' => true ])
        );
    }

    /**
     * Tests generate when generating relative and absolute URLs basing on the
     * current request.
     */
    public function testGenerateForRequest()
    {
        $content = new \Content();

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->setMethods([ 'getUriForContent' ])
            ->setConstructorArgs([ $this->container ])
            ->getMock();

        $this->instance->expects($this->any())->method('getMainDomain')
            ->willReturn('thud.opennemas.com');
        $this->requestStack->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);
        $this->request->expects($this->once())->method('getSchemeAndHttpHost')
            ->willReturn('http://quux.com');

        $helper->expects($this->at(0))->method('getUriForContent')
            ->with($content)->willReturn('wibble/fred');
        $helper->expects($this->at(1))->method('getUriForContent')
            ->with($content)->willReturn('wibble/fred');

        $this->assertEquals(
            '/wibble/fred',
            $helper->generate($content, ['absolute' => false  ])
        );

        $this->assertEquals(
            'http://quux.com/wibble/fred',
            $helper->generate($content, [ 'absolute' => true ])
        );
    }

    /**
     * Tests isValid.
     */
    public function testIsValid()
    {
        $item = null;

        $urlGenerator = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $urlGenerator->expects($this->any())->method('generate')
            ->with($item)->willReturn('norf/bar');

        $this->assertTrue($urlGenerator->isValid($item, 'norf/bar'));
        $this->assertFalse($urlGenerator->isValid($item, 'corge/garply'));
    }

    /**
     * Tests setInstance.
     */
    public function testSetInstance()
    {
        $this->assertEquals(
            $this->urlGenerator,
            $this->urlGenerator->setInstance('foobar')
        );
    }

    /**
     * tests getUriForAttachment.
     */
    public function testGetUriForAttachment()
    {
        $content = new \Attachment();

        $content->content_type_name = 'attachment';
        $content->id                = 252;
        $content->path              = 'route/to/file.name';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForAttachment');
        $method->setAccessible(true);

        $this->assertEquals(
            'media/opennemas/files/route/to/file.name',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * tests getUriForArticle.
     */
    public function testGetUriForArticle()
    {
        $content = new \Article();

        $content->id                = 252;
        $content->category_name     = 'actualidad';
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'article';
        $content->slug              = 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForArticle');
        $method->setAccessible(true);

        $this->assertEquals(
            'articulo/actualidad/alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForContent when the content has no body link property.
     */
    public function testGetUriForContentWhenBodyLink()
    {
        $content = new \Content();

        $content->content_type_name = 'glorp';
        $content->params            = [
            'bodyLink' => 'http://fred.flob/foobar/norf'
        ];

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->setMethods([ 'getUriForGlorp' ])
            ->setConstructorArgs([ $this->container ])
            ->getMock();

        $method = new \ReflectionMethod($helper, 'getUriForContent');
        $method->setAccessible(true);

        // Test relative url generation for article
        $this->assertEquals(
            'redirect?to=' . urlencode('http://fred.flob/foobar/norf'),
            $method->invokeArgs($helper, [ $content ])
        );
    }

    /**
     * Tests getUriForContent when created property is a DateTime object.
     */
    public function testGetUriForContentWhenCreatedAsObject()
    {
        $content = new \Video();
        $date    = new \DateTime();

        $content->id                = 252;
        $content->category_name     = 'actualidad';
        $content->created           = $date;
        $content->content_type_name = 'video';
        $content->slug              = 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForContent');
        $method->setAccessible(true);

        $this->assertEquals(
            'video/actualidad/alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena/' .
                $date->format('YmdHis') . '000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForContent when the content has no body link property.
     */
    public function testGetUriForContentWhenNoBodyLink()
    {
        $content = new \Content();

        $content->content_type_name = 'glorp';

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->setMethods([ 'getUriForGlorp' ])
            ->setConstructorArgs([ $this->container ])
            ->getMock();

        $helper->expects($this->once())->method('getUriForGlorp')
            ->with($content)->willReturn('/glorp/waldo/corge');

        $method = new \ReflectionMethod($helper, 'getUriForContent');
        $method->setAccessible(true);

        // Test relative url generation for article
        $this->assertEquals(
            '/glorp/waldo/corge',
            $method->invokeArgs($helper, [ $content ])
        );
    }

    /**
     * Tests getUriForLetter.
     */
    public function testGetUriForLetter()
    {
        $content = new \Letter();

        $content->id                = 252;
        $content->author            = 'My author';
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'letter';
        $content->slug              = 'letter-slug';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForLetter');
        $method->setAccessible(true);

        $this->assertEquals(
            'cartas-al-director/my-author/letter-slug/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForLetter when created property is a DateTime object.
     */
    public function testGetUriForLetterWhenCreatedAsObject()
    {
        $content = new \Letter();
        $date    = new \DateTime();

        $content->id                = 252;
        $content->author            = 'My author';
        $content->created           = $date;
        $content->content_type_name = 'letter';
        $content->slug              = 'letter-slug';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForLetter');
        $method->setAccessible(true);

        $this->assertEquals(
            'cartas-al-director/my-author/letter-slug/'
                . $date->format('YmdHis')
                . '000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForOpinion when the opinion has an author.
     */
    public function testGetUriForOpinionWhenAuthorNotPresent()
    {
        $author = new \User();

        $author->name = 'name';
        $author->slug = 'opinion-author-slug';

        $content = new \Opinion();

        $content->id                = 252;
        $content->fk_author         = 1;
        $content->type_opinion      = 0;
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'opinion';
        $content->slug              = 'opinion-author-slug';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForOpinion');
        $method->setAccessible(true);

        $this->um->expects($this->once())->method('find')
            ->with(1)->willReturn($author);

        $this->assertEquals(
            'opinion/name/opinion-author-slug/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForOpinion when the opinion has an author.
     */
    public function testGetUriForOpinionWhenAuthorPresent()
    {
        $content = new \Opinion();

        $content->id                = 252;
        $content->fk_author         = 1;
        $content->type_opinion      = 0;
        $content->author            = new \User();
        $content->author->name      = 'Name';
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'opinion';
        $content->slug              = 'opinion-author-slug';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForOpinion');
        $method->setAccessible(true);

        $this->assertEquals(
            'opinion/name/opinion-author-slug/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForOpinion when the opinion author is a blogger.
     */
    public function testGetUriForOpinionWhenBlog()
    {
        $content = new \Opinion();

        $content->id                = 252;
        $content->fk_author         = 1;
        $content->type_opinion      = 0;
        $content->author            = new \User();
        $content->author->name      = 'Name';
        $content->author->meta      = ['is_blog' => 1];
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'opinion';
        $content->slug              = 'opinion-author-slug';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForOpinion');
        $method->setAccessible(true);

        $this->assertEquals(
            'blog/name/opinion-author-slug/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * tests getUriForOpinion when the opinion type is director.
     */
    public function testGetUriForOpinionWhenDirector()
    {
        $content = new \Opinion();

        $content->id                = 252;
        $content->fk_author         = 0;
        $content->type_opinion      = 2;
        $content->author            = 'My author';
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'opinion';
        $content->slug              = 'opinion-director-slug';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForOpinion');
        $method->setAccessible(true);

        $this->assertEquals(
            'opinion/director/opinion-director-slug/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * tests getUriForOpinion when the opinion type is editorial.
     */
    public function testGetUriForOpinionWhenEditorial()
    {
        $content = new \Opinion();

        $content->id                = 252;
        $content->fk_author         = 0;
        $content->type_opinion      = 1;
        $content->author            = 'My author';
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'opinion';
        $content->slug              = 'opinion-editorial-slug';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForOpinion');
        $method->setAccessible(true);

        $this->assertEquals(
            'opinion/editorial/opinion-editorial-slug/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForOpinion when the opinion has no author.
     */
    public function testGetUriForOpinionWhenNoAuthor()
    {
        $content = new \Opinion();

        $content->id                = 252;
        $content->fk_author         = 0;
        $content->type_opinion      = 0;
        $content->author            = 'My author';
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'opinion';
        $content->slug              = 'opinion-author-slug';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForOpinion');
        $method->setAccessible(true);

        $this->assertEquals(
            'opinion/author/opinion-author-slug/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForPhoto.
     */
    public function testGetUriForPhoto()
    {
        $content = new \Photo();

        $content->content_type_name = 'photo';
        $content->path_file         = 'route/to';
        $content->name              = 'photo.file.name';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForPhoto');
        $method->setAccessible(true);

        $this->assertEquals(
            'media/opennemas/images/route/to/photo.file.name',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForContent when the content provided is a video.
     */
    public function testGetUriForVideo()
    {
        $content = new \Video();

        $content->id                = 252;
        $content->category_name     = 'actualidad';
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'video';
        $content->slug              = 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForContent');
        $method->setAccessible(true);

        $this->assertEquals(
            'video/actualidad/alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForUser when the content provided is a video.
     */
    public function testGetUriForUser()
    {
        $user = new \Common\ORM\Entity\User();

        $user->id   = 252;
        $user->name = 'pepito';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForUser');
        $method->setAccessible(true);

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_opinion_author_frontpage', [ 'author_slug' => $user->name, 'author_id' => $user->id ])
            ->willReturn('opinion/autor/252/pepito');

        $this->assertEquals(
            'opinion/autor/252/pepito',
            $method->invokeArgs($this->urlGenerator, [ $user ])
        );
    }

    /**
     * Tests getUriForUser when the content provided is an blogger.
     */
    public function testGetUriForUserBlogger()
    {
        $user = new \Common\ORM\Entity\User();

        $user->id   = 252;
        $user->name = 'pepito';
        $user->is_blog = 1;

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForUser');
        $method->setAccessible(true);

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_blog_author_frontpage', [ 'author_slug' => $user->name ])
            ->willReturn('opinion/autor/252/pepito');

        $this->assertEquals(
            'opinion/autor/252/pepito',
            $method->invokeArgs($this->urlGenerator, [ $user ])
        );
    }

    /**
     * Tests generateUriFromConfig for valid and invalid content types.
     */
    public function testGenerateUriFromConfig()
    {
        $method = new \ReflectionMethod($this->urlGenerator, 'generateUriFromConfig');
        $method->setAccessible(true);

        $this->assertEquals(
            $method->invokeArgs($this->urlGenerator, [ 'article', [
                'id'       => sprintf('%06d', 252),
                'category' => 'actualidad',
                'slug'     => 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena',
                'date'     => date('YmdHis', strtotime('2015-01-14 23:49:40')),
            ]]),
            'articulo/actualidad/alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena/20150114234940000252.html'
        );

        $this->assertEquals(
            $method->invokeArgs($this->urlGenerator, [ 'not-valid', [
                'id'       => sprintf('%06d', 252),
                'category' => 'actualidad',
                'slug'     => 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena',
                'date'     => date('YmdHis', strtotime('2015-01-14 23:49:40')),
            ]]),
            ''
        );

        $this->assertEquals(
            $method->invokeArgs($this->urlGenerator, [ null, [
                'id'       => sprintf('%06d', 252),
                'category' => 'actualidad',
                'slug'     => 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena',
                'date'     => date('YmdHis', strtotime('2015-01-14 23:49:40')),
            ]]),
            ''
        );
    }

    /**
     * Tests getConfig.
     */
    public function testGetConfig()
    {
        $method = new \ReflectionMethod($this->urlGenerator, 'getConfig');
        $method->setAccessible(true);

        $this->assertArrayHasKey('article', $method->invokeArgs($this->urlGenerator, []));
    }
}
