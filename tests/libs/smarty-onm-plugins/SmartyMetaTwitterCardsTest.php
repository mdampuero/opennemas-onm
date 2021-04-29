<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

use Common\Model\Entity\Instance;

/**
 * Defines test cases for SmartyMetaTwitterCardsTest class.
 */
class SmartyMetaTwitterCardsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.meta_twitter_cards.php';

        $this->instance = new Instance([ 'activated_modules' => [] ]);

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getValue' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->fm = $this->getMockBuilder('FilterManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'filter', 'set' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->photoHelper = $this->getMockBuilder('Common\Core\Component\Helper\PhotoHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPhotoPath' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('ContentMediaHelper')
            ->setMethods([ 'getMedia' ])
            ->getMock();

        $this->fm->expects($this->any())->method('set')
            ->willReturn($this->fm);
        $this->fm->expects($this->any())->method('filter')
            ->willReturn($this->fm);

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->smarty->expects($this->any())
            ->method('getContainer')
            ->willReturn($this->container);

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->container->expects($this->any())
            ->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->request->expects($this->any())
            ->method('getUri')
            ->willReturn('http://route/to/content.html');

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.content_media':
                return $this->helper;

            case 'core.helper.photo':
                return $this->photoHelper;

            case 'core.instance':
                return $this->instance;

            case 'data.manager.filter':
                return $this->fm;

            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->requestStack;
        }

        return null;
    }

    /**
     * Test smarty_function_meta_twitter_cards when no User
     */
    public function testMetaTwitterWhenContentNoUser()
    {
        $this->ds->expects($this->once())
            ->method('get')
            ->with('twitter_page')
            ->willReturn('https://twitter.com/');

        $this->assertEquals(
            '',
            smarty_function_meta_twitter_cards(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_meta_twitter_cards when content
     */
    public function testMetaTwitterWhenContent()
    {
        $content          = new \Content();
        $content->title   = 'This is the title';
        $content->summary = 'This is the summary';
        $content->body    = 'This is the body';

        $this->ds->expects($this->at(0))
            ->method('get')
            ->with('twitter_page')
            ->willReturn('https://twitter.com/twtuser');

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->at(1))->method('get')->with('site_title')
            ->willReturn('Site title');
        $this->ds->expects($this->at(2))->method('get')->with('site_description')
            ->willReturn('Site description');

        $this->helper->expects($this->once())->method('getMedia')
            ->willReturn(null);

        $output = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n"
            . "<meta name=\"twitter:title\" content=\"This is the title\">\n"
            . "<meta name=\"twitter:description\" content=\"This is the summary\">\n"
            . "<meta name=\"twitter:site\" content=\"@twtuser\">\n"
            . "<meta name=\"twitter:domain\" content=\"http://route/to/content.html\">";

        $this->assertEquals(
            $output,
            smarty_function_meta_twitter_cards(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_meta_twitter_cards when content and no summary
     */
    public function testMetaTwitterWhenContentNoSummary()
    {
        $content          = new \Content();
        $content->title   = 'This is the title';
        $content->summary = '';
        $content->body    = 'This is the body';

        $this->ds->expects($this->at(0))
            ->method('get')
            ->with('twitter_page')
            ->willReturn('https://twitter.com/twtuser');

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->at(1))->method('get')->with('site_title')
            ->willReturn('Site title');
        $this->ds->expects($this->at(2))->method('get')->with('site_description')
            ->willReturn('Site description');

        $this->helper->expects($this->once())->method('getMedia')
            ->willReturn(null);

        $output = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n"
            . "<meta name=\"twitter:title\" content=\"This is the title\">\n"
            . "<meta name=\"twitter:description\" content=\"This is the body...\">\n"
            . "<meta name=\"twitter:site\" content=\"@twtuser\">\n"
            . "<meta name=\"twitter:domain\" content=\"http://route/to/content.html\">";

        $this->assertEquals(
            $output,
            smarty_function_meta_twitter_cards(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_meta_twitter_cards when content and is video
     */
    public function testMetaTwitterWhenContentIsVideo()
    {
        $content                    = new \Content();
        $content->content_type      = 9;
        $content->content_type_name = 'video';
        $content->title             = 'This is the title';
        $content->summary           = 'This is the summary';
        $content->description       = 'This is the description';

        $this->ds->expects($this->at(0))
            ->method('get')
            ->with('twitter_page')
            ->willReturn('https://twitter.com/twtuser');

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->at(1))->method('get')->with('site_title')
            ->willReturn('Site title');
        $this->ds->expects($this->at(2))->method('get')->with('site_description')
            ->willReturn('Site description');

        $this->fm->expects($this->at(2))->method('get')
            ->willReturn('This is the title');
        $this->fm->expects($this->at(5))->method('get')
            ->willReturn('This is the description');

        $this->helper->expects($this->once())->method('getMedia')
            ->willReturn(null);

        $output = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n"
            . "<meta name=\"twitter:title\" content=\"This is the title\">\n"
            . "<meta name=\"twitter:description\" content=\"This is the description\">\n"
            . "<meta name=\"twitter:site\" content=\"@twtuser\">\n"
            . "<meta name=\"twitter:domain\" content=\"http://route/to/content.html\">";

        $this->assertEquals(
            $output,
            smarty_function_meta_twitter_cards(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_meta_twitter_cards when content and image
     */
    public function testMetaTwitterWhenContentAndImage()
    {
        $content          = new \Content();
        $content->title   = 'This is the title';
        $content->summary = 'This is the summary';
        $content->body    = 'This is the body';

        $this->ds->expects($this->at(0))
            ->method('get')
            ->with('twitter_page')
            ->willReturn('https://twitter.com/twtuser');

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->at(1))->method('get')->with('site_title')
            ->willReturn('Site title');
        $this->ds->expects($this->at(2))->method('get')->with('site_description')
            ->willReturn('Site description');

        // Photo object
        $photo         = new \Content();
        $photo->url    = 'http://route/to/file.name';
        $photo->path   = '/route/to/file.name';
        $photo->width  = 600;
        $photo->height = 400;

        $this->helper->expects($this->once())->method('getMedia')
            ->willReturn($photo);

        $this->photoHelper->expects($this->once())->method('getPhotoPath')
            ->with($photo, null, [], true)
            ->willReturn('http://route/to/file.name');

        $output = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n"
            . "<meta name=\"twitter:title\" content=\"This is the title\">\n"
            . "<meta name=\"twitter:description\" content=\"This is the summary\">\n"
            . "<meta name=\"twitter:site\" content=\"@twtuser\">\n"
            . "<meta name=\"twitter:domain\" content=\"http://route/to/content.html\">\n"
            . "<meta name=\"twitter:image\" content=\"http://route/to/file.name\">";

        $this->assertEquals(
            $output,
            smarty_function_meta_twitter_cards(null, $this->smarty)
        );
    }
}
