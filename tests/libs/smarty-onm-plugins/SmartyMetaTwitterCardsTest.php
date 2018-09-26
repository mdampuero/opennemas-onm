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

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
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

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

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
        switch ($name) {
            case 'core.helper.content_media':
                return $this->helper;

            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->requestStack;
        }

        return null;
    }

    /**
     * Test smarty_function_meta_twitter_cards when no content
     */
    public function testMetaTwitterWhenNoContent()
    {
        $this->assertEquals(
            '',
            smarty_function_meta_twitter_cards(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_meta_twitter_cards when no User
     */
    public function testMetaTwitterWhenContentNoUser()
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

        $this->ds->expects($this->once())
            ->method('get')
            ->with('twitter_page')
            ->willReturn('https://twitter.com/twtuser');

        $this->helper->expects($this->once())->method('getContentMediaObject');

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

        $this->ds->expects($this->once())
            ->method('get')
            ->with('twitter_page')
            ->willReturn('https://twitter.com/twtuser');

        $this->helper->expects($this->once())->method('getContentMediaObject');

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

        $this->ds->expects($this->once())
            ->method('get')
            ->with('twitter_page')
            ->willReturn('https://twitter.com/twtuser');

        $this->helper->expects($this->once())->method('getContentMediaObject');

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

        $this->ds->expects($this->once())
            ->method('get')
            ->with('twitter_page')
            ->willReturn('https://twitter.com/twtuser');

        $this->helper->expects($this->once())->method('getContentMediaObject')
            ->willReturn(json_decode(json_encode([
                'url' => 'http://route/to/file.name',
            ]), false));

        $output = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n"
            . "<meta name=\"twitter:title\" content=\"This is the title\">\n"
            . "<meta name=\"twitter:description\" content=\"This is the description\">\n"
            . "<meta name=\"twitter:site\" content=\"@twtuser\">\n"
            . "<meta name=\"twitter:domain\" content=\"http://route/to/content.html\">\n"
            . "<meta name=\"twitter:image\" content=\"http://route/to/file.name\">";

        $this->assertEquals(
            $output,
            smarty_function_meta_twitter_cards(null, $this->smarty)
        );
    }
}
