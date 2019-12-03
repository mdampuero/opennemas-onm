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

use Common\Core\Component\Helper\StructuredData;
use Common\Data\Core\FilterManager;
use Common\ORM\Entity\Tag;

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

        $this->instance = $this->getMockBuilder('Common\ORM\Entity\Instance')
            ->setMethods([
                'getBaseUrl', 'getImagesShortPath', 'hasMultilanguage'
            ])->getMock();

        $this->tpl = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods(['fetch'])->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContext' ])
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

        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('http://xyzzy.com');
        $this->instance->expects($this->any())->method('getImagesShortPath')
            ->willReturn('/media/images/');

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->fm = new FilterManager($this->container);

        $this->instance->activated_modules = [];

        $GLOBALS['kernel'] = $this->kernel;

        $this->data = [
            'content'  => new \Content(),
            'url'      => 'http://onm.com/20161013114032000674.html',
            'title'    => 'This is the object title',
            'author'   => 'John Doe',
            'created'  => '2016-10-13 11:40:32',
            'changed'  => '2016-10-13 11:40:32',
            'category' => new \StdClass(),
            'summary'  => '<p>This is the summary</p>',
            'tags'     => [1,2,3,4],
            'logo'     => [
                'url'    => 'http://onm.com/asset/logo.png',
                'width'  => 350,
                'height' => 60
            ],
            'image'    => new \Photo(),
            'video'    => new \Video(),
        ];

        $this->data['image']->url         = "http://image-url.com";
        $this->data['image']->width       = 700;
        $this->data['image']->height      = 450;
        $this->data['image']->description = "Image description/caption";

        $this->data['video']->title       = "This is the video title";
        $this->data['video']->description = "<p>Video description</p>";
        $this->data['video']->created     = "2016-10-13 11:40:32";
        $this->data['video']->thumb       = "http://video-thumb.com";
        $this->data['video']->tags        = [1,2,3,4,5];

        $this->data['category']->title = "Mundo";

        $this->data['content']->metadata = [1,2,3,4,5];
        $this->data['content']->body     = "This is the body text";

        $this->object = new StructuredData($this->instance, $this->em, $this->ts, $this->tpl);
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

    /**
     * This method tests extractParamsFromData method with tags in both video and content
     */
    public function testExtractParamsFromData()
    {
        $this->data['content']->tags = [1,2,3,4];
        $this->data['keywords']      = "keywords,object,json,linking";
        $this->data['videokeywords'] = "keywords,object,json,linking,data";
        $this->data['sitename']      = "Site name";
        $this->data['wordCount']     = 5;
        $this->data['siteurl']       = 'http://console/';

        $this->ts->expects($this->exactly(2))->method('getListByIds')
            ->with($this->logicalOr($this->data['content']->tags, $this->data['video']->tags))
            ->will($this->returnCallback(function ($arg) {
                if ($arg == $this->data['content']->tags) {
                    return [
                        'items' => [
                            new Tag([ 'name' => 'keywords' ]),
                            new Tag([ 'name' => 'object' ]),
                            new Tag([ 'name' => 'json' ]),
                            new Tag([ 'name' => 'linking' ]),
                        ]
                        ];
                    } else {
                    return [
                        'items' => [
                            new Tag([ 'name' => 'keywords' ]),
                            new Tag([ 'name' => 'object' ]),
                            new Tag([ 'name' => 'json' ]),
                            new Tag([ 'name' => 'linking' ]),
                            new Tag([ 'name' => 'data' ]),
                        ]
                        ];
                }
            }));

        $this->ds->expects($this->once())->method('get')
            ->with("site_name")
            ->willReturn("Site name");

        $this->assertEquals($this->data, $this->object->extractParamsFromData($this->data));
    }

    /**
     * This method tests extractParamsFromData method without video tags
     */
    public function testExtractParamsFromDataWithoutVideoTags()
    {
        $this->data['content']->tags = [1,2,3,4];
        $this->data['video']->tags   = null;
        $this->data['keywords']      = "keywords,object,json,linking";
        $this->data['videokeywords'] = "";
        $this->data['sitename']      = "Site name";
        $this->data['wordCount']     = 5;
        $this->data['siteurl']       = 'http://console/';

        $this->ts->expects($this->once())->method('getListByIds')
            ->with($this->data['content']->tags)
            ->willReturn([
                'items' => [
                    new Tag([ 'name' => 'keywords' ]),
                    new Tag([ 'name' => 'object' ]),
                    new Tag([ 'name' => 'json' ]),
                    new Tag([ 'name' => 'linking' ]),
                ]
                ]);

        $this->ds->expects($this->once())->method('get')
            ->with("site_name")
            ->willReturn("Site name");

        $this->assertEquals($this->data, $this->object->extractParamsFromData($this->data));
    }

    /**
     * This method tests extractParamsFromData method without content tags
     */
    public function testExtractParamsFromDataWithoutContentTags()
    {
        $this->data['content']->tags = null;
        $this->data['keywords']      = "";
        $this->data['videokeywords'] = "keywords,object,json,linking,data";
        $this->data['sitename']      = "Site name";
        $this->data['wordCount']     = 5;
        $this->data['siteurl']       = 'http://console/';

        $this->ts->expects($this->once())->method('getListByIds')
            ->with($this->data['video']->tags)
            ->willReturn([
                'items' => [
                    new Tag([ 'name' => 'keywords' ]),
                    new Tag([ 'name' => 'object' ]),
                    new Tag([ 'name' => 'json' ]),
                    new Tag([ 'name' => 'linking' ]),
                    new Tag([ 'name' => 'data' ]),
                ]
                ]);

        $this->ds->expects($this->once())->method('get')
            ->with("site_name")
            ->willReturn("Site name");

        $this->assertEquals($this->data, $this->object->extractParamsFromData($this->data));
    }

    /**
     * This method tests extractParamsFromData method without tags
     */
    public function testExtractParamsFromDataWithoutTags()
    {
        $this->data['content']->tags = null;
        $this->data['video']->tags   = null;
        $this->data['keywords']      = "";
        $this->data['videokeywords'] = "";
        $this->data['sitename']      = "Site name";
        $this->data['wordCount']     = 5;
        $this->data['siteurl']       = 'http://console/';

        $this->ts->expects($this->never())->method('getListByIds');

        $this->ds->expects($this->once())->method('get')
            ->with("site_name")
            ->willReturn("Site name");

        $this->assertEquals($this->data, $this->object->extractParamsFromData($this->data));
    }
}
