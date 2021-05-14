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

use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for SmartyMetaFacebookTagsTest class.
 */
class SmartyMetaFacebookTagsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.meta_facebook_tags.php';

        $this->instance = new Instance([ 'activated_modules' => [] ]);

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getValue' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->contentHelper = $this->getMockBuilder('ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getSummary' ])
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
            ->setMethods([ 'getPhotoPath', 'hasPhotoPath' ])
            ->getMock();

        $this->videoHelper = $this->getMockBuilder('Common\Core\Component\Helper\VideoHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getVideoThumbnail' ])
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

            case 'core.helper.content':
                return $this->contentHelper;

            case 'core.helper.photo':
                return $this->photoHelper;

            case 'core.helper.video':
                return $this->videoHelper;

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
     * Test smarty_function_meta_facebook_tags when no content
     */
    public function testMetaFacebookWhenNoContent()
    {
        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn(null);

        $this->ds->expects($this->at(0))->method('get')->with('site_title')
            ->willReturn('Site title');
        $this->ds->expects($this->at(1))->method('get')->with('site_description')
            ->willReturn('Site description');
        $this->ds->expects($this->at(2))->method('get')->with('site_name')
            ->willReturn('Site Name');

        $this->helper->expects($this->once())->method('getMedia')
            ->willReturn(null);

        $output = "<meta property=\"og:type\" content=\"website\" />\n"
            . "<meta property=\"og:title\" content=\"Site title\" />\n"
            . "<meta property=\"og:description\" content=\"Site description\" />\n"
            . "<meta property=\"og:url\" content=\"http://route/to/content.html\" />\n"
            . "<meta property=\"og:site_name\" content=\"Site Name\" />";

        $this->assertEquals(
            $output,
            smarty_function_meta_facebook_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_meta_facebook_tags when content
     */
    public function testMetaFacebookWhenContent()
    {
        $content          = new \Content();
        $content->title   = 'This is the title';
        $content->summary = 'This is the summary';
        $content->body    = 'This is the body';

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->at(0))->method('get')->with('site_title')
            ->willReturn('Site title');
        $this->ds->expects($this->at(1))->method('get')->with('site_description')
            ->willReturn('Site description');
        $this->ds->expects($this->at(2))->method('get')->with('site_name')
            ->willReturn('Site Name');

        $this->helper->expects($this->once())->method('getMedia')
            ->willReturn(null);

        $this->contentHelper->expects($this->once())->method('getSummary')
            ->with($content)
            ->willReturn($content->summary);

        $output = "<meta property=\"og:type\" content=\"website\" />\n"
            . "<meta property=\"og:title\" content=\"This is the title\" />\n"
            . "<meta property=\"og:description\" content=\"This is the summary\" />\n"
            . "<meta property=\"og:url\" content=\"http://route/to/content.html\" />\n"
            . "<meta property=\"og:site_name\" content=\"Site Name\" />";

        $this->assertEquals(
            $output,
            smarty_function_meta_facebook_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_meta_facebook_tags when content and no summary
     */
    public function testMetaFacebookWhenContentNoSummary()
    {
        $content        = new \Content();
        $content->title = 'This is the title';
        $content->body  = 'This is the body';

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->at(0))->method('get')->with('site_title')
            ->willReturn('Site title');
        $this->ds->expects($this->at(1))->method('get')->with('site_description')
            ->willReturn('Site description');
        $this->ds->expects($this->at(2))->method('get')->with('site_name')
            ->willReturn('Site Name');

        $this->helper->expects($this->once())->method('getMedia')
            ->willReturn(null);

        $output = "<meta property=\"og:type\" content=\"website\" />\n"
            . "<meta property=\"og:title\" content=\"This is the title\" />\n"
            . "<meta property=\"og:description\" content=\"This is the body...\" />\n"
            . "<meta property=\"og:url\" content=\"http://route/to/content.html\" />\n"
            . "<meta property=\"og:site_name\" content=\"Site Name\" />";

        $this->assertEquals(
            $output,
            smarty_function_meta_facebook_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_meta_facebook_tags when content and is video
     */
    public function testMetaFacebookWhenContentIsVideo()
    {
        $content                    = new Content();
        $content->content_type      = 9;
        $content->content_type_name = 'video';
        $content->title             = 'This is the title';
        $content->summary           = 'This is the summary';
        $content->description       = 'This is the description';

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->at(0))->method('get')->with('site_title')
            ->willReturn('Site title');
        $this->ds->expects($this->at(1))->method('get')->with('site_description')
            ->willReturn('Site description');
        $this->ds->expects($this->at(2))->method('get')->with('site_name')
            ->willReturn('Site Name');

        $this->contentHelper->expects($this->once())->method('getSummary')
            ->with($content)
            ->willReturn($content->description);

        $this->helper->expects($this->once())->method('getMedia')
            ->willReturn(null);

        $output = "<meta property=\"og:type\" content=\"website\" />\n"
            . "<meta property=\"og:title\" content=\"This is the title\" />\n"
            . "<meta property=\"og:description\" content=\"This is the description\" />\n"
            . "<meta property=\"og:url\" content=\"http://route/to/content.html\" />\n"
            . "<meta property=\"og:site_name\" content=\"Site Name\" />";

        $this->assertEquals(
            $output,
            smarty_function_meta_facebook_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_meta_facebook_tags when content and image
     */
    public function testMetaFacebookWhenContentAndImage()
    {
        $content          = new \Content();
        $content->title   = 'This is the title';
        $content->summary = 'This is the summary';
        $content->body    = 'This is the body';

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->at(0))->method('get')->with('site_title')
            ->willReturn('Site title');
        $this->ds->expects($this->at(1))->method('get')->with('site_description')
            ->willReturn('Site description');
        $this->ds->expects($this->at(2))->method('get')->with('site_name')
            ->willReturn('Site Name');

        // Photo object
        $photo         = new \Content();
        $photo->url    = 'http://route/to/file.name';
        $photo->path   = '/route/to/file.name';
        $photo->width  = 600;
        $photo->height = 400;

        $this->helper->expects($this->once())->method('getMedia')
            ->willReturn($photo);

        $this->contentHelper->expects($this->once())->method('getSummary')
            ->willReturn($content->summary);

        $this->photoHelper->expects($this->once())->method('hasPhotoPath')
            ->with($photo)
            ->willReturn(true);

        $this->photoHelper->expects($this->once())->method('getPhotoPath')
            ->with($photo, null, [], true)
            ->willReturn('http://route/to/file.name');

        $output = "<meta property=\"og:type\" content=\"website\" />\n"
            . "<meta property=\"og:title\" content=\"This is the title\" />\n"
            . "<meta property=\"og:description\" content=\"This is the summary\" />\n"
            . "<meta property=\"og:url\" content=\"http://route/to/content.html\" />\n"
            . "<meta property=\"og:site_name\" content=\"Site Name\" />\n"
            . "<meta property=\"og:image\" content=\"http://route/to/file.name\" />\n"
            . "<meta property=\"og:image:width\" content=\"600\"/>\n"
            . "<meta property=\"og:image:height\" content=\"400\"/>";

        $this->assertEquals(
            $output,
            smarty_function_meta_facebook_tags(null, $this->smarty)
        );
    }

    /**
     * Tests smarty_function_meta_facebook_tags when content and video
     */
    public function testMetaFacebookWhenContentAndVideo()
    {
        $content          = new \Content();
        $content->title   = 'This is the title';
        $content->summary = 'This is the summary';
        $content->body    = 'This is the body';

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->at(0))->method('get')->with('site_title')
            ->willReturn('Site title');
        $this->ds->expects($this->at(1))->method('get')->with('site_description')
            ->willReturn('Site description');
        $this->ds->expects($this->at(2))->method('get')->with('site_name')
            ->willReturn('Site Name');

        // Photo object
        $photo         = new \Content();
        $photo->width  = 600;
        $photo->height = 400;
        $photo->url    = 'http://route/to/file.name';

        $this->helper->expects($this->once())->method('getMedia')
            ->willReturn($photo);

        $this->contentHelper->expects($this->once())->method('getSummary')
            ->with($content)
            ->willReturn($content->summary);

        $this->photoHelper->expects($this->once())->method('hasPhotoPath')
            ->willReturn(true);

        $this->photoHelper->expects($this->once())->method('getPhotoPath')
            ->with($photo, null, [], true)
            ->willReturn('http://route/to/file.name');

        $output = "<meta property=\"og:type\" content=\"website\" />\n"
            . "<meta property=\"og:title\" content=\"This is the title\" />\n"
            . "<meta property=\"og:description\" content=\"This is the summary\" />\n"
            . "<meta property=\"og:url\" content=\"http://route/to/content.html\" />\n"
            . "<meta property=\"og:site_name\" content=\"Site Name\" />\n"
            . "<meta property=\"og:image\" content=\"http://route/to/file.name\" />\n"
            . "<meta property=\"og:image:width\" content=\"600\"/>\n"
            . "<meta property=\"og:image:height\" content=\"400\"/>";

        $this->assertEquals(
            $output,
            smarty_function_meta_facebook_tags(null, $this->smarty)
        );
    }
}
