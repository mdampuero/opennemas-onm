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
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->tpl = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods(['fetch'])->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
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
    }

    public function serviceContainerCallback($name)
    {
        if ($name === 'data.manager.filter') {
            return $this->fm;
        }

        if ($name === 'core.locale') {
            return $this->locale;
        }

        if ($name === 'core.instance') {
            return $this->instance;
        }

        return null;
    }

    // /**
    //  * @covers \Common\Core\Component\Helper\StructuredData::__construct
    //  */
    // public function testConstruct()
    // {
    //     $this->assertEquals($this->tpl, $this->object->tpl);
    //     $this->assertEquals($this->ds, $this->object->ds);
    //     $this->assertEquals($this->ts, $this->object->ts);
    // }

    // /**
    //  * This method tests extractParamsFromData method with tags in both video and content
    //  */
    // public function testExtractParamsFromData()
    // {
    //     $data                  = [];
    //     $data['content']       = new \Content();
    //     $data['content']->tags = [1,2,3,4];
    //     $data['content']->body = 'Body';
    //     $data['video']         = new \Video();
    //     $data['video']->tags   = [1,2,3,4,5];

    //     $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
    //         ->disableOriginalConstructor()
    //         ->setMethods([ 'getTags' ])
    //         ->getMock();

    //     $object->expects($this->exactly(2))->method('getTags')
    //         ->with($this->logicalOr($data['content']->tags, $data['video']->tags))
    //         ->will($this->returnCallback(function ($arg) use ($data) {
    //             if ($arg == $data['content']->tags) {
    //                 return 'keywords,object,json,linking';
    //             } else {
    //                 return 'keywords,object,json,linking,data';
    //             }
    //         }));

    //     $this->ds->expects($this->any())->method('get')
    //         ->with('site_name')
    //         ->willReturn('Site name');

    //     $output                  = [];
    //     $output['keywords']      = "keywords,object,json,linking";
    //     $output['videokeywords'] = "keywords,object,json,linking,data";
    //     $output['sitename']      = "Site name";
    //     $output['wordCount']     = 4;
    //     $output['siteurl']       = 'http://console/';

    //     $output = array_merge($output, $data);

    //     $this->assertEquals($output, $this->object->extractParamsFromData($data));
    // }

    // /**
    //  * This method tests extractParamsFromData method without video tags
    //  */
    // public function testExtractParamsFromDataWithoutVideoTags()
    // {
    //     $this->data['content']->tags = [1,2,3,4];
    //     $this->data['video']->tags   = null;
    //     $this->data['keywords']      = "keywords,object,json,linking";
    //     $this->data['videokeywords'] = "";
    //     $this->data['sitename']      = "Site name";
    //     $this->data['wordCount']     = 5;
    //     $this->data['siteurl']       = 'http://console/';

    //     $this->ts->expects($this->once())->method('getListByIds')
    //         ->with($this->data['content']->tags)
    //         ->willReturn([
    //             'items' => [
    //                 new Tag([ 'name' => 'keywords' ]),
    //                 new Tag([ 'name' => 'object' ]),
    //                 new Tag([ 'name' => 'json' ]),
    //                 new Tag([ 'name' => 'linking' ]),
    //             ]
    //             ]);

    //     $this->ds->expects($this->once())->method('get')
    //         ->with("site_name")
    //         ->willReturn("Site name");

    //     $this->assertEquals($this->data, $this->object->extractParamsFromData($this->data));
    // }

    // /**
    //  * This method tests extractParamsFromData method without content tags
    //  */
    // public function testExtractParamsFromDataWithoutContentTags()
    // {
    //     $this->data['content']->tags = null;
    //     $this->data['keywords']      = "";
    //     $this->data['videokeywords'] = "keywords,object,json,linking,data";
    //     $this->data['sitename']      = "Site name";
    //     $this->data['wordCount']     = 5;
    //     $this->data['siteurl']       = 'http://console/';

    //     $this->ts->expects($this->once())->method('getListByIds')
    //         ->with($this->data['video']->tags)
    //         ->willReturn([
    //             'items' => [
    //                 new Tag([ 'name' => 'keywords' ]),
    //                 new Tag([ 'name' => 'object' ]),
    //                 new Tag([ 'name' => 'json' ]),
    //                 new Tag([ 'name' => 'linking' ]),
    //                 new Tag([ 'name' => 'data' ]),
    //             ]
    //             ]);

    //     $this->ds->expects($this->once())->method('get')
    //         ->with("site_name")
    //         ->willReturn("Site name");

    //     $this->assertEquals($this->data, $this->object->extractParamsFromData($this->data));
    // }

    // /**
    //  * This method tests extractParamsFromData method without tags
    //  */
    // public function testExtractParamsFromDataWithoutTags()
    // {
    //     $this->data['content']->tags = null;
    //     $this->data['video']->tags   = null;
    //     $this->data['keywords']      = "";
    //     $this->data['videokeywords'] = "";
    //     $this->data['sitename']      = "Site name";
    //     $this->data['wordCount']     = 5;
    //     $this->data['siteurl']       = 'http://console/';

    //     $this->ts->expects($this->never())->method('getListByIds');

    //     $this->ds->expects($this->once())->method('get')
    //         ->with("site_name")
    //         ->willReturn("Site name");

    //     $this->assertEquals($this->data, $this->object->extractParamsFromData($this->data));
    // }
}
