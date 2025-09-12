<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\SendpulseHelper;

class SendpulseHelperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods(['get', 'getParameter'])
            ->getMock();

        $this->instance = $this->getMockBuilder('Common\\Model\\Entity\\Instance')
            ->disableOriginalConstructor()
            ->setMethods(['getMediaShortPath', 'getMainDomain'])
            ->getMock();

        $this->imageHelper = $this->getMockBuilder('Common\\Core\\Component\\Helper\\ImageHelper')
            ->disableOriginalConstructor()
            ->setMethods(['exists'])
            ->getMock();

        $this->urlGenerator = $this->getMockBuilder('Common\\Core\\Component\\Helper\\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods(['getUrl'])
            ->getMock();

        $this->featuredMediaHelper = $this->getMockBuilder('Common\\Core\\Component\\Helper\\FeaturedMediaHelper')
            ->disableOriginalConstructor()
            ->setMethods(['getFeaturedMedia'])
            ->getMock();

        $this->contentService = $this->getMockBuilder('Api\\Service\\V1\\ContentService')
            ->disableOriginalConstructor()
            ->setMethods(['getItem'])
            ->getMock();

        $this->orm = $this->getMockBuilder('Opennemas\\Orm\\Core\\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getDataSet'])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods(['get', 'set'])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $this->container->expects($this->any())->method('getParameter')
            ->with('core.paths.public')
            ->willReturn(sys_get_temp_dir());

        $this->instance->expects($this->any())->method('getMediaShortPath')
            ->willReturn('');
        $this->instance->expects($this->any())->method('getMainDomain')
            ->willReturn('example.com');

        $this->orm->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')
            ->willReturn($this->ds);

        $this->ds->expects($this->any())->method('get')
            ->will($this->returnCallback(function ($key) {
                if ($key === 'sendpulse_website_id') {
                    return 1;
                }
                return null;
            }));
        $this->ds->expects($this->any())->method('set')
            ->willReturn(null);

        $this->contentService->expects($this->any())->method('getItem')
            ->willReturn(null);

        $this->article = (object) [
            'title'       => 'Title',
            'description' => 'Description',
            'body'        => 'Body',
        ];

        $this->image = (object) [
            'path'         => '',
            'external_uri' => '',
            'size'         => 100,
        ];

        $this->featuredMediaHelper->expects($this->any())->method('getFeaturedMedia')
            ->willReturn($this->image);

        $this->urlGenerator->expects($this->any())->method('getUrl')
            ->willReturn('https://example.com');

        $this->helper = new SendpulseHelper($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;
            case 'core.helper.image':
                return $this->imageHelper;
            case 'core.helper.url_generator':
                return $this->urlGenerator;
            case 'core.helper.featured_media':
                return $this->featuredMediaHelper;
            case 'api.service.content':
                return $this->contentService;
            case 'orm.manager':
                return $this->orm;
            default:
                return null;
        }
    }

    public function testExternalImageIsEncoded()
    {
        $content = 'image content';
        $tmp = tempnam(sys_get_temp_dir(), 'img');
        $file = $tmp . '.jpg';
        rename($tmp, $file);
        file_put_contents($file, $content);

        $this->image->external_uri = 'file://' . $file;

        $data = $this->helper->getNotificationData($this->article);

        unlink($file);

        $this->assertArrayHasKey('image', $data);
        $this->assertEquals(basename($file), $data['image']['name']);
        $this->assertEquals(base64_encode($content), $data['image']['data']);
    }
}
