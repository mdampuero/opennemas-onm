<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Components\Functions;

use Common\Model\Entity\Content;

/**
 * Defines test cases for content functions.
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

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
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
        $photo = new Content();

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
        $photo = new Content();

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)->willReturn('/glorp/xyzzy/foobar.jpg');

        $this->router->expects($this->once())->method('generate')
            ->with('asset_image', [
                'params' => 'grault',
                'path'   => '/glorp/xyzzy/foobar.jpg'
            ])->willReturn('/glorp/xyzzy/foobar.jpg');

        $this->assertEquals('/glorp/xyzzy/foobar.jpg', get_photo_path($photo, 'grault'));
    }
}
