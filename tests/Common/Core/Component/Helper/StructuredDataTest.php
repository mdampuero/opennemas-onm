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

        $this->tpl = $this->getMockBuilder('TemplateAdmin')
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Api\Service\V1\TagService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getListByIds' ])
            ->getMock();

        $this->ds->expects($this->any())->method('get')
            ->with('site_name')
            ->willReturn('site name');

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
            case 'orm.manager':
                return $this->em;
            case 'api.service.tag':
                return $this->ts;
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
        $data                  = [];
        $data['content']       = new \Content();
        $data['content']->body = 'This is a test body';
        $data['content']->tags = [1,2,3,4];
        $data['video']         = new \Video();
        $data['video']->tags   = [1,2,3,4,5];

        $output                  = [];
        $output['content']       = new \Content();
        $output['video']         = new \Video();
        $output['content']->tags = [1,2,3,4];
        $output['video']->tags   = [1,2,3,4,5];
        $output['videokeywords'] = 'keywords,object,json,linking,data';
        $output['keywords']      = 'keywords,object,json,linking';
        $output['sitename']      = 'site name';
        $output['siteurl']       = 'http://console/';
        $output['content']->body = 'This is a test body';
        $output['wordCount']     = 5;

        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags' ])
            ->getMock();

        $object->expects($this->exactly(2))->method('getTags')
            ->with($this->logicalOr($data['content']->tags, $data['video']->tags))
            ->will($this->returnCallback(function ($arg) use ($data) {
                if ($arg == $data['content']->tags) {
                    return 'keywords,object,json,linking';
                } else {
                    return 'keywords,object,json,linking,data';
                }
            }));

        $this->ds->expects($this->once())->method('get');

        $this->assertEquals($output, $object->extractParamsFromData($data));
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::extractParamsFromData
     * without video tags
     */
    public function testExtractParamsFromDataWithoutVideoTags()
    {
        $data                  = [];
        $data['content']       = new \Content();
        $data['content']->body = 'This is a test body';
        $data['content']->tags = [1,2,3,4];
        $data['video']         = new \Video();
        $data['video']->tags   = [];

        $output                  = [];
        $output['content']       = new \Content();
        $output['video']         = new \Video();
        $output['content']->tags = [1,2,3,4];
        $output['video']->tags   = [];
        $output['videokeywords'] = '';
        $output['keywords']      = 'keywords,object,json,linking';
        $output['sitename']      = 'site name';
        $output['siteurl']       = 'http://console/';
        $output['content']->body = 'This is a test body';
        $output['wordCount']     = 5;

        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags' ])
            ->getMock();

        $object->expects($this->once())->method('getTags')
            ->with($data['content']->tags)
            ->willReturn('keywords,object,json,linking');

        $this->ds->expects($this->once())->method('get');

        $this->assertEquals($output, $object->extractParamsFromData($data));
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::extractParamsFromData
     * without content tags
     */
    public function testExtractParamsFromDataWithoutContentTags()
    {
        $data                  = [];
        $data['content']       = new \Content();
        $data['content']->body = 'This is a test body';
        $data['content']->tags = [];
        $data['video']         = new \Video();
        $data['video']->tags   = [1,2,3,4,5];

        $output                  = [];
        $output['content']       = new \Content();
        $output['video']         = new \Video();
        $output['content']->tags = [];
        $output['video']->tags   = [1,2,3,4,5];
        $output['videokeywords'] = 'keywords,object,json,linking,data';
        $output['keywords']      = '';
        $output['sitename']      = 'site name';
        $output['siteurl']       = 'http://console/';
        $output['content']->body = 'This is a test body';
        $output['wordCount']     = 5;

        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags' ])
            ->getMock();

        $object->expects($this->once())->method('getTags')
            ->with($data['video']->tags)
            ->willReturn('keywords,object,json,linking,data');

        $this->ds->expects($this->once())->method('get');

        $this->assertEquals($output, $object->extractParamsFromData($data));
    }

    /**
     * @covers \Common\Core\Component\Helper\StructuredData::extractParamsFromData
     * without tags
     */
    public function testExtractParamsFromDataWithoutTags()
    {
        $data                  = [];
        $data['content']       = new \Content();
        $data['content']->body = 'This is a test body';
        $data['content']->tags = [];
        $data['video']         = new \Video();
        $data['video']->tags   = [];

        $output                  = [];
        $output['content']       = new \Content();
        $output['video']         = new \Video();
        $output['content']->tags = [];
        $output['video']->tags   = [];
        $output['videokeywords'] = '';
        $output['keywords']      = '';
        $output['sitename']      = 'site name';
        $output['siteurl']       = 'http://console/';
        $output['content']->body = 'This is a test body';
        $output['wordCount']     = 5;

        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags' ])
            ->getMock();

        $object->expects($this->never())->method('getTags');

        $this->ds->expects($this->once())->method('get');

        $this->assertEquals($output, $object->extractParamsFromData($data));
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

        $this->tpl->expects($this->once())->method('fetch')
            ->with('common/helpers/structured_gallery_data.tpl', $data);

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
        $data['content']->content_type_name = 'article';
        $data['summary']                    = 'This is a test summary';
        $data['created']                    = '10-10-2010 00:00:00';
        $data['changed']                    = '10-10-2010 00:00:00';
        $data['url']                        = 'http://console/';
        $data['author']                     = 'John Doe';
        $data['video']                      = new \Video();


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags', 'extractParamsFromData' ])
            ->getMock();

        $object->expects($this->once())->method('extractParamsFromData')
            ->with($data)
            ->willReturn($data);

        $this->tpl->expects($this->once())->method('fetch')
            ->with('common/helpers/structured_video_data.tpl', $data);

        $object->generateJsonLDCode($data);
    }


    /**
     * @covers \Common\Core\Component\Helper\StructuredData::generateJsonLDCode
     * for content of type article
     */
    public function testGenerateJsonLDCodeWithArticle()
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


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags', 'extractParamsFromData' ])
            ->getMock();

        $object->expects($this->once())->method('extractParamsFromData')
            ->with($data)
            ->willReturn($data);

        $this->tpl->expects($this->once())->method('fetch')
            ->with('common/helpers/structured_article_data.tpl', $data);

        $object->generateJsonLDCode($data);
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
