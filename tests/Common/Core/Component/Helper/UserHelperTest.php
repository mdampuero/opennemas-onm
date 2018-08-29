<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\ORM\Entity\User;
use Common\Core\Component\Helper\UserHelper;

/**
 * Defines test cases for UserHelper class.
 */
class UserHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->fm = $this->getMockBuilder('FilterManager')
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository')
            ->setMethods([ 'findBy' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->fm->expects($this->any())->method('set')
            ->willReturn($this->fm);
        $this->fm->expects($this->any())->method('filter')
            ->willReturn($this->fm);

        $this->helper = new UserHelper($this->container);
    }

    /**
     * Returns the mocked service basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'entity_repository':
                return $this->repository;

            case 'data.manager.filter':
                return $this->fm;

            default:
                return null;
        }
    }

    /**
     * Tests getPhotos when no users provided.
     */
    public function testGetPhotosWhenNoUsersProvided()
    {
        $this->assertEmpty($this->helper->getPhotos(''));
        $this->assertEmpty($this->helper->getPhotos(123));
        $this->assertEmpty($this->helper->getPhotos(null));
        $this->assertEmpty($this->helper->getPhotos([]));
    }

    /**
     * Tests getPhotos when a list of users is provided.
     */
    public function testGetPhotosWhenUsersProvided()
    {
        $this->repository->expects($this->once())->method('findBy')
            ->willReturn([ json_decode(json_encode([
                'pk_content' => 100,
                'title'      => 'flob'
            ])) ]);

        $this->fm->expects($this->once())->method('get')
            ->willReturn([ 100 => json_decode(json_encode([
                'pk_content' => 100,
                'title'      => 'flob'
            ])) ]);

        $photos = $this->helper->getPhotos([ new User([ 'avatar_img_id' => 100 ]) ]);

        $this->assertEquals('flob', $photos[100]->title);
    }
}
