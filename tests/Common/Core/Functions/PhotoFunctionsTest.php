<?php

namespace Tests\Common\Core\Components\Functions;

use Common\Model\Entity\Instance;
use Common\Model\Entity\Content;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Defines test cases for photo functions.
 */
class PhotoFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
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

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->content = new Content([
            'content_type'   => 'photo',
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);
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

            case 'router':
                return $this->router;

            case 'core.instance':
                return $this->instance;
            default:
                return null;
        }
    }

    /**
     * Tests get_photo_path when empty photo provided.
     */
    public function testGetPhotoPathWhenEmpty()
    {
        $this->assertNull(get_photo_path(null));
    }

    /**
     * Tests get_photo_path when no transform provided.
     */
    public function testGetPhotoPathWhenNoTransform()
    {
        $photo = new Content([
            'content_status' => 1,
            'starttime'      => new \Datetime('2000-01-01 00:00:00')
        ]);

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)->willReturn('/glorp/xyzzy/foobar.jpg');

        $this->assertEquals('/plugh', get_photo_path('/plugh'));
        $this->assertEquals('/glorp/xyzzy/foobar.jpg', get_photo_path($photo));
    }

    /**
     * Tests get_photo_path when a transform is provided.
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

        $this->assertEquals('/glorp/xyzzy/foobar.jpg', get_photo_path($photo, 'grault'));
    }

    /**
     * Tests get_photo_path when generating absolute URL.
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
            get_photo_path($photo, null, [], true)
        );
    }

    /**
     * Tests get_photo_path when generating absolute URL for an image with transform.
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

        $this->assertEquals('/glorp/xyzzy/foobar.jpg', get_photo_path($photo, 'grault', [], true));
    }

    /**
     * Tests get_size.
     */
    public function testGetPhotoSize()
    {
        $this->assertNull(get_photo_size($this->content));

        $this->content->size = '222';
        $this->assertEquals('222', get_photo_size($this->content));
    }

    /**
     * Tests get_width.
     */
    public function testGetPhotoWidth()
    {
        $this->assertNull(get_photo_width($this->content));

        $this->content->width = '222';
        $this->assertEquals('222', get_photo_width($this->content));
    }

    /**
     * Tests get_height.
     */
    public function testGetPhotoHeight()
    {
        $this->assertNull(get_photo_height($this->content));

        $this->content->height = '222';
        $this->assertEquals('222', get_photo_height($this->content));
    }

    /**
     * Tests get_photo_mime_type.
     */
    public function testGetPhotoMimeType()
    {
        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('https://glorp.com/pp.jpg');

        $this->content->path = '/glorp/xyzzy/foobar.jpg';
        $this->assertEquals('image/jpeg', get_photo_mime_type($this->content));
    }

    /**
     * Tests get_photo_mime_type when external photo.
     */
    public function testGetPhotoMimeTypeWhenExternal()
    {
        $this->content->path = 'https://glorp.com/glorp/xyzzy/foobar.jpg';
        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('http://glorp.com/ppp.jpg');
        $this->assertEquals('image/jpeg', get_photo_mime_type($this->content));
    }
}
