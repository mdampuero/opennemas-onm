<?php

namespace Tests\Common\Core\Components\Functions;

use Common\Core\Component\Helper\ContentHelper;
use Common\Core\Component\Helper\PhotoHelper;
use Common\Model\Entity\Content;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Defines test cases for photo helper.
 */
class PhotoHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods(['isReadyForPublish'])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->frontend = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->ugh = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getBaseUrl' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->contentHelper->expects($this->any())->method('isReadyForPublish')
            ->willReturn(true);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->content = new Content([
            'content_type'   => 'photo',
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->helper = new PhotoHelper($this->contentHelper, $this->instance, $this->router, $this->ugh);
    }

    /**
     * Returns a mocked service based on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.url_generator':
                return $this->ugh;

            case 'core.helper.content':
                return $this->contentHelper;

            case 'router':
                return $this->router;

            case 'core.instance':
                return $this->instance;

            case 'core.template.frontend':
                return $this->frontend;
            default:
                return null;
        }
    }

    /**
     * Tests getPhotoPath when empty photo provided.
     */
    public function testGetPhotoPathWhenEmptyHere()
    {
        $this->assertNull($this->helper->getPhotoPath(null));
    }

    /**
     * Tests getPhotoPath when no transform provided.
     */
    public function testGetPhotoPathWhenNoTransform()
    {
        $photo = new Content([
            'content_status' => 1,
            'starttime'      => new \Datetime('2000-01-01 00:00:00')
        ]);

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)->willReturn('/glorp/xyzzy/foobar.jpg');

        $this->assertEquals('/plugh', $this->helper->getPhotoPath('/plugh'));
        $this->assertEquals('/glorp/xyzzy/foobar.jpg', $this->helper->getPhotoPath($photo));
    }

    /**
     * Tests getPhotoPath when a transform is provided.
     */
    public function testGetPhotoPathWhenTransform()
    {
        $photo = new Content([
            'content_status' => 1,
            'starttime'      => new \Datetime('2000-01-01 00:00:00')
        ]);

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)->willReturn('/glorp/xyzzy/foobar.jpg');

        $this->router->expects($this->once())->method('generate')
            ->with('asset_image', [
                'params' => 'grault',
                'path'   => '/glorp/xyzzy/foobar.jpg'
            ])->willReturn('/glorp/xyzzy/foobar.jpg');

        $this->assertEquals('/glorp/xyzzy/foobar.jpg', $this->helper->getPhotoPath($photo, 'grault'));
    }

    /**
     * Tests getPhotoPath when generating absolute URL.
     */
    public function testGetPhotoPathWhenAbsolute()
    {
        $photo = new Content([
            'content_status' => 1,
            'starttime'      => new \Datetime('2000-01-01 00:00:00')
        ]);

        $this->ugh->expects($this->at(0))->method('generate')
            ->with($photo)
            ->willReturn('/glorp/xyzzy/foobar.jpg');

        $this->ugh->expects($this->at(1))->method('generate')
            ->with($photo, [ 'absolute' => true ])
            ->willReturn('http://foo.bar/glorp/xyzzy/foobar.jpg');

        $this->assertEquals(
            'http://foo.bar/glorp/xyzzy/foobar.jpg',
            $this->helper->getPhotoPath($photo, null, [], true)
        );
    }

    /**
     * Tests getPhotoPath when generating absolute URL for an image with transform.
     */
    public function testGetPhotoPathWhenAbsoluteAndTransform()
    {
        $photo = new Content([
            'content_status' => 1,
            'starttime'      => new \Datetime('2000-01-01 00:00:00')
        ]);

        $this->ugh->expects($this->at(0))->method('generate')
            ->with($photo)
            ->willReturn('/glorp/xyzzy/foobar.jpg');

        $this->router->expects($this->once())->method('generate')
            ->with('asset_image', [
                'params' => 'grault',
                'path'   => '/glorp/xyzzy/foobar.jpg'
            ], UrlGeneratorInterface::ABSOLUTE_URL)->willReturn(
                '/glorp/xyzzy/foobar.jpg'
            );

        $this->assertEquals('/glorp/xyzzy/foobar.jpg', $this->helper->getPhotoPath($photo, 'grault', [], true));
    }

    /**
     * Tests getSize.
     */
    public function testGetPhotoSize()
    {
        $this->assertNull($this->helper->getPhotoSize($this->content));

        $this->content->size = '222';
        $this->assertEquals('222', $this->helper->getPhotoSize($this->content));
    }

    /**
     * Tests getWidth.
     */
    public function testGetPhotoWidth()
    {
        $this->assertNull($this->helper->getPhotoWidth($this->content));

        $this->content->width = '222';
        $this->assertEquals('222', $this->helper->getPhotoWidth($this->content));
    }

    /**
     * Tests getHeight.
     */
    public function testGetPhotoHeight()
    {
        $this->assertNull($this->helper->getPhotoHeight($this->content));

        $this->content->height = '222';
        $this->assertEquals('222', $this->helper->getPhotoHeight($this->content));
    }

    /**
     * Tests getPhotoMimeType.
     */
    public function testGetPhotoMimeType()
    {
        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('https://glorp.com/pp.jpg');

        $this->content->path = '/glorp/xyzzy/foobar.jpg';
        $this->assertEquals('image/jpeg', $this->helper->getPhotoMimeType($this->content));
    }

    /**
     * Tests getPhotoMimeType when external photo.
     */
    public function testGetPhotoMimeTypeWhenExternal()
    {
        $this->content->path = 'https://glorp.com/glorp/xyzzy/foobar.jpg';
        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('http://glorp.com/ppp.jpg');
        $this->assertEquals('image/jpeg', $this->helper->getPhotoMimeType($this->content));
    }

    /**
     * Tests the method hasPhotoPath.
     */
    public function testHasPhotoPath()
    {
        $this->assertTrue($this->helper->hasPhotoPath('foo/glorp/path'));
        $this->assertFalse($this->helper->hasPhotoPath(null));
    }

    /**
     * Tests the method hasPhotoSize.
     */
    public function testHasPhotoSize()
    {
        $photo = new Content(
            [
                'content_status' => 1,
                'starttime' => new \Datetime(),
                'size' => 128
            ]
        );

        $externalThumbnail = 'https://glorp.com/glorp/xyzzy/foobar.jpg';

        $this->assertTrue($this->helper->hasPhotoSize($photo));

        $contentHelper = new ContentHelper($this->container);
        $photoHelper   = new PhotoHelper($contentHelper, $this->instance, $this->router, $this->ugh);

        $this->assertFalse($photoHelper->hasPhotoSize($externalThumbnail));
    }
}
