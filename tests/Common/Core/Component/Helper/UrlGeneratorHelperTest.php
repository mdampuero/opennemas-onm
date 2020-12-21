<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\UrlGeneratorHelper;
use Common\Model\Entity\Category;
use Common\Model\Entity\Content;
use Common\Model\Entity\Tag;
use Common\Model\Entity\User;

class UrlGeneratorHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    protected function setUp()
    {
        $this->cs = $this->getMockBuilder('Api\Service\V1\CategoryService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->um = $this->getMockBuilder('UserManager')
            ->setMethods([ 'find' ])
            ->getMock();

        $this->fm = $this->getMockBuilder('Opennemas\Data\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getBaseUrl', 'getMainDomain', 'hasMultilanguage' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'getContext', 'setContext' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->authorService = $this->getMockBuilder('AuthorService')
            ->setMethods([ 'getItem' ])
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

        $this->instance->internal_name = 'opennemas';

        if (!defined('INSTANCE_UNIQUE_NAME')) {
            define('INSTANCE_UNIQUE_NAME', 'opennemas');
        }

        $this->urlGenerator = new UrlGeneratorHelper($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.author':
                return $this->authorService;

            case 'api.service.category':
                return $this->cs;

            case 'data.manager.filter':
                return $this->fm;

            case 'user_repository':
                return $this->um;

            case 'core.instance':
                return $this->instance;

            case 'core.locale':
                return $this->locale;

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
            ->with('category_frontpage', [ 'category_slug' => 'garply' ])
            ->willReturn('blog/section/garply');

        $this->assertEquals(
            '/blog/section/garply',
            $this->urlGenerator->generate($category)
        );
    }

    /**
     * Tests generate when the content provided has an external URI.
     */
    public function testGenerateForArticleWithAmp()
    {
        $content = new \Article();

        $content->id                = 252;
        $content->category_slug     = 'actualidad';
        $content->category_id       = 28618;
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'article';
        $content->slug              = 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena';

        $this->cs->expects($this->once())->method('getItem')
            ->with(28618)->willReturn(new Category([ 'name' => 'actualidad' ]));

        $this->fm->expects($this->any(2))->method('get')
            ->willReturn('alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena');

        $this->assertEquals(
            '/articulo/actualidad/alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena/20150114234940000252.amp.html',
            $this->urlGenerator->generate($content, ['_format' => 'amp'])
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

        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('https://thud.opennemas.com');

        $this->instance->expects($this->any())->method('getMainDomain')
            ->willReturn('thud.opennemas.com');

        $helper->expects($this->any())->method('getUriForContent')
            ->with($content)->willReturn('wibble/fred');

        $this->assertEquals(
            '/wibble/fred',
            $helper->generate($content, [ 'absolute' => false  ])
        );

        $this->assertEquals(
            'https://thud.opennemas.com/wibble/fred',
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

        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('https://quux.com');
        $this->instance->expects($this->any())->method('getMainDomain')
            ->willReturn('quux.com');

        $helper->expects($this->any())->method('getUriForContent')
            ->with($content)->willReturn('wibble/fred');

        $this->assertEquals(
            '/wibble/fred',
            $helper->generate($content, [ 'absolute' => false ])
        );

        $this->assertEquals(
            'https://quux.com/wibble/fred',
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
        $content->category_slug     = 'actualidad';
        $content->category_id       = 24845;
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'article';
        $content->slug              = 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForArticle');
        $method->setAccessible(true);

        $this->cs->expects($this->once())->method('getItem')
            ->with(24845)->willReturn(new Category([ 'name' => 'actualidad' ]));

        $this->fm->expects($this->any(2))->method('get')
            ->willReturn('alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena');
        $this->assertEquals(
            'articulo/actualidad/alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
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
        $content->category_slug     = 'actualidad';
        $content->category_id       = 6458;
        $content->created           = $date;
        $content->content_type_name = 'video';
        $content->slug              = 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForContent');
        $method->setAccessible(true);

        $this->cs->expects($this->once())->method('getItem')
            ->with(6458)->willReturn(new Category([ 'name' => 'actualidad' ]));

        $this->fm->expects($this->any(2))->method('get')
            ->willReturn('alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena');

        $this->assertEquals(
            'video/actualidad/alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena/' .
                $date->format('YmdHis') . '000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForContent when the content uses the new ORM and categories
     * are defined as an array of ids.
     */
    public function testGetUriForContentWhenCategoriesAsArray()
    {
        $date = new \DateTime();

        $content = new Content([
            'pk_content'        => 252,
            'category_slug'     => 'actualidad',
            'categories'        => [ 6458 ],
            'created'           => $date,
            'content_type_name' => 'video',
            'slug'              => 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena'
        ]);

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForContent');
        $method->setAccessible(true);

        $this->cs->expects($this->once())->method('getItem')
            ->with(6458)->willReturn(new Category([ 'name' => 'actualidad' ]));

        $this->assertEquals(
            'video/actualidad/alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena/' .
                $date->format('YmdHis') . '000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForContent when the content has no body link property.
     */
    public function testGetUriForContentWhithValidCustomContentTypeName()
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
        $content                    = new \Opinion();
        $content->id                = 252;
        $content->fk_author         = 1;
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'opinion';
        $content->slug              = 'opinion-author-slug';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForOpinion');
        $method->setAccessible(true);

        $this->authorService->expects($this->once())->method('getItem')
            ->with(1)->will($this->throwException(new \Exception()));

        $this->fm->expects($this->any(2))->method('get')
            ->willReturn('opinion-author-slug');

        $this->assertEquals(
            'opinion/author/opinion-author-slug/20150114234940000252.html',
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
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'opinion';
        $content->slug              = 'opinion-author-slug';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForOpinion');
        $method->setAccessible(true);

        $author       = new User();
        $author->name = 'Author name';

        $this->authorService->expects($this->any())->method('getItem')
            ->willReturn($author);

        $this->fm->expects($this->at(2))->method('get')
            ->willReturn('author-name');
        $this->fm->expects($this->any(5))->method('get')
            ->willReturn('opinion-author-slug');

        $this->assertEquals(
            'opinion/author-name/opinion-author-slug/20150114234940000252.html',
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
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'opinion';
        $content->slug              = 'opinion-author-slug';

        $author          = new User();
        $author->name    = 'Author name';
        $author->is_blog = 1;

        $this->authorService->expects($this->any())->method('getItem')
            ->willReturn($author);

        $this->fm->expects($this->at(2))->method('get')
            ->willReturn('author-name');
        $this->fm->expects($this->any(5))->method('get')
            ->willReturn('opinion-author-slug');

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForOpinion');
        $method->setAccessible(true);

        $this->assertEquals(
            'blog/author-name/opinion-author-slug/20150114234940000252.html',
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
        $content->author            = 'My author';
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'opinion';
        $content->slug              = 'opinion-author-slug';

        $this->fm->expects($this->once())->method('get')
            ->willReturn('opinion-author-slug');

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
        $content = new \Content();

        $content->content_type_name = 'photo';
        $content->path              = 'images/route/to/photo.file.name';

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
        $content->category_slug     = 'actualidad';
        $content->category_id       = 28618;
        $content->created           = '2015-01-14 23:49:40';
        $content->content_type_name = 'video';
        $content->slug              = 'alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena';

        $this->cs->expects($this->once())->method('getItem')
            ->with(28618)->willReturn(new Category([ 'name' => 'actualidad' ]));
        $this->fm->expects($this->once())->method('get')
            ->willReturn('alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena');

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForContent');
        $method->setAccessible(true);

        $this->assertEquals(
            'video/actualidad/alerta-aeropuerto-roma-amenaza-bomba-vuelo-viena/20150114234940000252.html',
            $method->invokeArgs($this->urlGenerator, [ $content ])
        );
    }

    /**
     * Tests getUriForTag when the content provided is a video.
     */
    public function testGetUriForTag()
    {
        $user = new Tag([ 'id' => 13608, 'name' => 'Flob', 'slug' => 'flob' ]);

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForTag');
        $method->setAccessible(true);

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_tag_frontpage', [
                'slug' => 'flob'
            ])->willReturn('tag/flob');

        $this->assertEquals(
            'tag/flob',
            $method->invokeArgs($this->urlGenerator, [ $user ])
        );
    }

    /**
     * Tests getUriForUser when the content provided is a video.
     */
    public function testGetUriForUser()
    {
        $user = new User();

        $user->id   = 252;
        $user->name = 'Karl Woods';
        $user->slug = 'karl-woods';

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForUser');
        $method->setAccessible(true);

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_opinion_author_frontpage', [
                'author_slug' => $user->slug,
                'author_id'   => $user->id
            ])->willReturn('opinion/autor/252/karl-woods');

        $this->assertEquals(
            'opinion/autor/252/karl-woods',
            $method->invokeArgs($this->urlGenerator, [ $user ])
        );
    }

    /**
     * Tests getUriForUser when the content provided is an blogger.
     */
    public function testGetUriForUserBlogger()
    {
        $user = new User();

        $user->id      = 252;
        $user->slug    = 'olga-gilbert';
        $user->is_blog = 1;

        $method = new \ReflectionMethod($this->urlGenerator, 'getUriForUser');
        $method->setAccessible(true);

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_blog_author_frontpage', [ 'author_slug' => $user->slug ])
            ->willReturn('opinion/autor/252/olga-gilbert');

        $this->assertEquals(
            'opinion/autor/252/olga-gilbert',
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
