<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Frontend\Renderer\Content;

use Api\Exception\GetItemException;
use Exception;
use PHPUnit\Framework\TestCase;
use Frontend\Renderer\Content\ContentRenderer;
use Common\Model\Entity\User;

class ContentRendererTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger')
            ->setMethods([ 'error' ])
            ->getMock();

        $this->renderer = $this->getMockBuilder('Frontend\Renderer\Content\ContentRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTemplate' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->as = $this->getMockBuilder('Api\Service\V1\AuthorService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->content = new \Content();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
    }


    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.template':
                return $this->template;
            case 'error.log':
                return $this->logger;
            case 'api.service.author':
                return $this->as;
        }

        return null;
    }

    /**
     * Tests render when template exists.
     */
    public function testRenderWhenTemplate()
    {
        $this->renderer->expects($this->at(0))->method('getTemplate')
            ->with([ 'item' => $this->content ])
            ->willReturn('template.tpl');

        $this->template->expects($this->at(0))->method('fetch')
            ->with('template.tpl');

        $this->renderer->render($this->content, []);
    }

    /**
     * Tests render when template doesn't exists.
     */
    public function testRenderWhenNoTemplate()
    {
        $this->renderer->expects($this->at(0))->method('getTemplate')
            ->with([ 'item' => $this->content ])
            ->willReturn('template.tpl');

        $this->template->expects($this->at(0))->method('fetch')
            ->with('template.tpl')
            ->will($this->throwException(new Exception()));

        $this->assertEquals(_('Content not available'), $this->renderer->render($this->content, []));
    }

    /**
     * Tests getTemplate when content is an article.
     */
    public function testGetTemplateWhenArticle()
    {
        $article        = new \Article();
        $params['item'] = $article;
        $params['tpl']  = 'frontpage/contents/_custom_article.tpl';

        $renderer = new ContentRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getTemplate');
        $method->setAccessible(true);

        $this->assertEquals('frontpage/contents/_custom_article.tpl', $method->invokeArgs($renderer, [ &$params ]));
    }

    /**
     * Tests getTemplate when content is an opinion.
     */
    public function testGetTemplateWhenOpinion()
    {
        $opinion        = new \Opinion();
        $author         = new User();
        $author->meta   = [ 'is_blog' => 0 ];
        $params['item'] = $opinion;
        $tpl            = 'frontpage/contents/_opinion.tpl';

        $renderer = new ContentRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getTemplate');
        $method->setAccessible(true);

        $this->as->expects($this->once())->method('getItem')
            ->willReturn($author);

        $this->assertEquals($tpl, $method->invokeArgs($renderer, [ &$params ]));
    }

    /**
     * Tests getTemplate when content is an opinion with invalid author.
     */
    public function testGetTemplateWhenInvalidOpinion()
    {
        $opinion            = new \Opinion();
        $opinion->fk_author = 1;
        $params             = [ 'item' => $opinion ];
        $tpl                = 'frontpage/contents/_opinion.tpl';

        $renderer = new ContentRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getTemplate');
        $method->setAccessible(true);

        $this->as->expects($this->once())->method('getItem')
            ->will($this->throwException(new GetItemException()));

        $this->assertEquals($tpl, $method->invokeArgs($renderer, [ &$params ]));
    }

    /**
     * Tests getTemplate when content is a blog.
     */
    public function testGetTemplateWhenBlog()
    {
        $opinion         = new \Opinion();
        $author          = new User();
        $author->is_blog = 1;
        $params['item']  = $opinion;
        $tpl             = 'frontpage/contents/_blog.tpl';

        $renderer = new ContentRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getTemplate');
        $method->setAccessible(true);

        $this->as->expects($this->once())->method('getItem')
            ->willReturn($author);

        $this->assertEquals($tpl, $method->invokeArgs($renderer, [ &$params ]));
    }

    /**
     * Test getTemplate when content is a letter.
     */
    public function testGetTemplateWhenLetter()
    {
        $letter         = new \Letter();
        $params['item'] = $letter;
        $tpl            = 'frontpage/contents/_content.tpl';

        $renderer = new ContentRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getTemplate');
        $method->setAccessible(true);

        $this->assertEquals($tpl, $method->invokeArgs($renderer, [ &$params ]));
    }
}
