<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Component\Helper;

use Common\Model\Entity\Content;

/**
 * Defines test cases for StructuredData class.
 */
class StructuredDataTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');


        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('ContentMediaHelper')
            ->setMethods([ 'getMedia' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getMediaShortPath', 'getBaseUrl' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->tpl = $this->getMockBuilder('TemplateAdmin')
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->as = $this->getMockBuilder('Api\Service\V1\AuthorService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Api\Service\V1\TagService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getListByIds' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->getMock();
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.template.admin':
                return $this->tpl;

            case 'core.instance':
                return $this->instance;

            case 'orm.manager':
                return $this->em;

            case 'api.service.author':
                return $this->as;

            case 'api.service.tag':
                return $this->ts;

            case 'core.helper.content_media':
                return $this->helper;
        }

        return null;
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals($this->tpl, $this->object->tpl);
        $this->assertEquals($this->ts, $this->object->ts);
        $this->assertEquals($this->ds, $this->object->ds);
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::extractParamsFromData
     * with tags in both: content and video
     */
    public function testExtractParamsFromData()
    {
        $data                   = [];
        $data['content']        = new Content();
        $data['content']->tags  = [1,2,3,4];
        $data['content']->title = 'This is the title';
        $data['video']          = new Content();
        $data['video']->tags    = [1,2,3,4,5];

        $output                    = [];
        $output['content']         = new Content();
        $output['video']           = new Content();
        $output['content']->tags   = [1,2,3,4];
        $output['video']->tags     = [1,2,3,4,5];
        $output['videoKeywords']   = 'keywords,object,json,linking,data';
        $output['keywords']        = 'keywords,object,json,linking';
        $output['siteName']        = 'site name';
        $output['siteUrl']         = 'http://opennemas.com';
        $output['siteDescription'] = 'site description';
        $output['content']->title  = 'This is the title';
        $output['title']           = 'This is the title';
        $output['description']     = 'This is the description';
        $output['wordCount']       = 4;
        $output['logo']            = 'logo';
        $output['author']          = 'author';
        $output['image']           = null;


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags', 'getLogoData', 'getDescription', 'getAuthorData', 'getMediaData' ])
            ->getMock();

        $object->expects($this->at(0))->method('getLogoData')->willReturn('logo');
        $object->expects($this->at(1))->method('getDescription')->willReturn('This is the description');
        $object->expects($this->at(2))->method('getAuthorData')->willReturn('author');
        $object->expects($this->at(3))->method('getMediaData')
            ->willReturn([
                'image' => null,
                'video' => $data['video']
            ]);

        $object->expects($this->at(4))->method('getTags')
            ->with($data['content']->tags)
            ->willReturn('keywords,object,json,linking');
        $object->expects($this->at(5))->method('getTags')
            ->with($data['video']->tags)
            ->willReturn('keywords,object,json,linking,data');

        $this->instance->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn('http://opennemas.com');

        $this->ds->expects($this->at(0))->method('get')
            ->with('site_name')
            ->willReturn('site name');
        $this->ds->expects($this->at(1))->method('get')
            ->with('site_description')
            ->willReturn('site description');

        $this->assertEquals($output, $object->extractParamsFromData($data));
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::extractParamsFromData
     * without content
     */
    public function testExtractParamsFromDataWithoutContent()
    {
        $output['logo']            = 'logo';
        $output['siteName']        = 'site name';
        $output['siteUrl']         = 'http://opennemas.com';
        $output['siteDescription'] = 'site description';


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getLogoData'])
            ->getMock();

        $object->expects($this->at(0))->method('getLogoData')->willReturn('logo');

        $this->instance->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn('http://opennemas.com');

        $this->ds->expects($this->at(0))->method('get')
            ->with('site_name')
            ->willReturn('site name');
        $this->ds->expects($this->at(1))->method('get')
            ->with('site_description')
            ->willReturn('site description');

        $this->assertEquals($output, $object->extractParamsFromData([]));
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::generateJsonLDCode
     * for content of type album
     */
    public function testGenerateJsonLDCodeWithAlbum()
    {
        $data                               = [];
        $data['content']                    = new \Content();
        $data['content']->tags              = [1,2,3,4];
        $data['content']->content_type_name = 'album';
        $data['summary']                    = 'This is a test summary';
        $data['created']                    = '10-10-2010 00:00:00';
        $data['changed']                    = '10-10-2010 00:00:00';
        $data['url']                        = 'http://console/';
        $data['author']                     = 'John Doe';


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags', 'extractParamsFromData' ])
            ->getMock();

        $object->expects($this->once())->method('extractParamsFromData')
            ->with($data)
            ->willReturn($data);

        $this->tpl->expects($this->at(0))->method('fetch')
            ->with('common/helpers/structured_album_data.tpl', $data);

        $object->generateJsonLDCode($data);
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::generateJsonLDCode
     * for content of type article with video
     */
    public function testGenerateJsonLDCodeArticleWithVideo()
    {
        $data                               = [];
        $data['content']                    = new \Content();
        $data['content']->tags              = [1,2,3,4];
        $data['content']->content_type_name = 'article';
        $data['summary']                    = 'This is a test summary';
        $data['created']                    = '10-10-2010 00:00:00';
        $data['changed']                    = '10-10-2010 00:00:00';
        $data['url']                        = 'http://console/';
        $data['author']                     = 'John Doe';
        $data['video']                      = new Content();


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags', 'extractParamsFromData' ])
            ->getMock();

        $object->expects($this->once())->method('extractParamsFromData')
            ->with($data)
            ->willReturn($data);

        $this->tpl->expects($this->at(0))->method('fetch')
            ->with('common/helpers/structured_content_data.tpl', $data);

        $object->generateJsonLDCode($data);
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::generateJsonLDCode
     * for content of type video
     */
    public function testGenerateJsonLDCodeWithVideo()
    {
        $data                               = [];
        $data['content']                    = new \Content();
        $data['content']->tags              = [1,2,3,4];
        $data['content']->content_type_name = 'video';
        $data['summary']                    = 'This is a test summary';
        $data['created']                    = '10-10-2010 00:00:00';
        $data['changed']                    = '10-10-2010 00:00:00';
        $data['url']                        = 'http://console/';
        $data['author']                     = 'John Doe';
        $data['video']                      = new Content();


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags', 'extractParamsFromData' ])
            ->getMock();

        $object->expects($this->once())->method('extractParamsFromData')
            ->with($data)
            ->willReturn($data);

        $this->tpl->expects($this->at(0))->method('fetch')
            ->with('common/helpers/structured_video_data.tpl', $data);

        $object->generateJsonLDCode($data);
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::getAuthorData
     */
    public function testGetAuthorData()
    {
        $method = new \ReflectionMethod($this->object, 'getAuthorData');
        $method->setAccessible(true);

        $content = new \Content();

        $this->as->expects($this->once())
            ->method('getItem')
            ->willReturn(json_decode(json_encode([ 'name' => 'John Doe' ])));

        $method->invokeArgs($this->object, [ $content ]);
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::getAuthorData
     */
    public function testGetAuthorDataWithoutAuthor()
    {
        $method = new \ReflectionMethod($this->object, 'getAuthorData');
        $method->setAccessible(true);

        $content = new \Content();

        $this->as->expects($this->once())
            ->method('getItem')
            ->willReturn(null);

        $this->ds->expects($this->once())->method('get')
            ->with('site_name')
            ->willReturn('site name');

        $method->invokeArgs($this->object, [ $content ]);
    }


    /**
     * @covers \Common\Core\Component\Helper\StructuredData::getDescription
     */
    public function testGetDescription()
    {
        $method = new \ReflectionMethod($this->object, 'getDescription');
        $method->setAccessible(true);

        $content              = new \Content();
        $content->description = 'Foo bar baz';

        $this->assertEquals(
            $content->description,
            $method->invokeArgs($this->object, [ $content ])
        );

        $content->description = '';

        $this->ds->expects($this->once())->method('get')
            ->with('site_description')
            ->willReturn('Site description');

        $this->assertEquals(
            'Site description',
            $method->invokeArgs($this->object, [ $content ])
        );
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::getLogoData
     */
    public function testGetLogoData()
    {
        $method = new \ReflectionMethod($this->object, 'getLogoData');
        $method->setAccessible(true);

        $this->ds->expects($this->at(0))
            ->method('get')
            ->with('site_logo')
            ->willReturn('logo.png');

        $this->instance->expects($this->once())
            ->method('getMediaShortPath')
            ->willReturn('/media/opennemas');

        $method->invokeArgs($this->object, []);
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::getMediaData
     */
    public function testGetMediaDataWithPhoto()
    {
        $method = new \ReflectionMethod($this->object, 'getMediaData');
        $method->setAccessible(true);

        $content = new \Content();
        $this->helper->expects($this->once())
            ->method('getMedia')
            ->willReturn(new \Content());

        $method->invokeArgs($this->object, [ $content ]);
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::getMediaData
     */
    public function testGetMediaDataWithVideo()
    {
        $method = new \ReflectionMethod($this->object, 'getMediaData');
        $method->setAccessible(true);

        $content = new \Content();
        $this->helper->expects($this->once())
            ->method('getMedia')
            ->willReturn(new Content());

        $method->invokeArgs($this->object, [ $content ]);
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::getTags
     */
    public function testGetTags()
    {
        $method = new \ReflectionMethod($this->object, 'getTags');
        $method->setAccessible(true);

        $ids = [1];

        $tag       = new \Content();
        $tag->name = 'keywords';

        $this->ts->expects($this->once())->method('getListByIds')
            ->willReturn([ 'items' => [ $tag ]]);

        $method->invokeArgs($this->object, [ $ids ]);
    }
}
