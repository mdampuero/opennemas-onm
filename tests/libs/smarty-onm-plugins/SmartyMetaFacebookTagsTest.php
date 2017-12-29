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

/**
 * Defines test cases for SmartyMetaFacebookTagsTest class.
 */
class SmartyMetaFacebookTagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.meta_facebook_tags.php';

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('SettingManager')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('ContentMediaHelper')
            ->setMethods([ 'getContentMediaObject' ])
            ->getMock();

        $this->smarty->expects($this->any())
            ->method('getContainer')
            ->willReturn($this->container);

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->container->expects($this->any())
            ->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->repository->expects($this->any())
            ->method('get')
            ->with('site_name')
            ->willReturn('Site Name');

        $this->request->expects($this->any())
            ->method('getUri')
            ->willReturn('http://route/to/content.html');
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     */
    public function serviceContainerCallback($name)
    {
        if ($name === 'request_stack') {
            return $this->requestStack;
        }

        if ($name === 'setting_repository') {
            return $this->repository;
        }

        if ($name === 'core.helper.content_media') {
            return $this->helper;
        }

        return null;
    }

    /**
     * Test smarty_function_meta_facebook_tags when no content
     */
    public function testMetaFacebookWhenNoContent()
    {
        $this->assertEquals(
            '',
            smarty_function_meta_facebook_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_meta_facebook_tags when content
     */
    public function testMetaFacebookWhenContent()
    {
        $this->smarty->tpl_vars = [ 'content' => json_decode(
            json_encode([
                'value' => json_decode(
                    json_encode([
                        'pk_content'        => 145,
                        'title'             => 'This is the title',
                        'summary'           => 'This is the summary',
                        'body'              => 'This is the body',
                        'category_name'     => 'gorp',
                        'slug'              => 'foobar-thud',
                        'content_type_name' => 'article',
                        'created'           => '1999-12-31 23:59:59',
                    ]),
                    false
                )
            ]),
            false
        )];

        $this->helper->expects($this->once())->method('getContentMediaObject');

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
        $this->smarty->tpl_vars = [ 'content' => json_decode(
            json_encode([
                'value' => json_decode(
                    json_encode([
                        'pk_content'        => 145,
                        'title'             => 'This is the title',
                        'summary'           => '',
                        'body'              => 'This is the body',
                        'category_name'     => 'gorp',
                        'slug'              => 'foobar-thud',
                        'content_type_name' => 'article',
                        'created'           => '1999-12-31 23:59:59',
                    ]),
                    false
                )
            ]),
            false
        )];

        $this->helper->expects($this->once())->method('getContentMediaObject');

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
        $this->smarty->tpl_vars = [ 'content' => json_decode(
            json_encode([
                'value' => json_decode(
                    json_encode([
                        'pk_content'        => 145,
                        'title'             => 'This is the title',
                        'summary'           => 'This is the summary',
                        'body'              => 'This is the body',
                        'description'       => 'This is the description',
                        'category_name'     => 'gorp',
                        'slug'              => 'foobar-thud',
                        'content_type_name' => 'video',
                        'created'           => '1999-12-31 23:59:59',
                    ]),
                    false
                )
            ]),
            false
        )];

        $this->helper->expects($this->once())->method('getContentMediaObject');

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
        $this->smarty->tpl_vars = [ 'content' => json_decode(
            json_encode([
                'value' => json_decode(
                    json_encode([
                        'pk_content'        => 145,
                        'title'             => 'This is the title',
                        'summary'           => 'This is the summary',
                        'body'              => 'This is the body',
                        'description'       => 'This is the description',
                        'category_name'     => 'gorp',
                        'slug'              => 'foobar-thud',
                        'content_type_name' => 'video',
                        'created'           => '1999-12-31 23:59:59',
                    ]),
                    false
                )
            ]),
            false
        )];

        $this->helper->expects($this->once())->method('getContentMediaObject')
            ->willReturn(json_decode(json_encode([
                'url' => 'http://route/to/file.name',
                'width' => 600,
                'height' => 400,
            ]), false));

        $output = "<meta property=\"og:type\" content=\"website\" />\n"
            . "<meta property=\"og:title\" content=\"This is the title\" />\n"
            . "<meta property=\"og:description\" content=\"This is the description\" />\n"
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
