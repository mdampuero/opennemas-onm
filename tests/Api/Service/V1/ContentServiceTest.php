<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Api\Service\V1;

use Api\Service\V1\ContentService;
use Common\ORM\Entity\Content;

/**
 * Defines test cases for CategoryService class.
 */
class ContentServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('EventDispatcher')
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([ 'getRepository' ])->getMock();

        $this->logger = $this->getMockBuilder('Logger')
            ->setMethods([ 'error' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([
                'countContents', 'moveContents', 'removeContents'
            ])->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->service = $this->getMockBuilder('Api\Service\V1\ContentService')
            ->setMethods([ 'getItem', 'getItemBy', 'getListByIds' ])
            ->setConstructorArgs([ $this->container, 'Common\ORM\Entity\Content' ])
            ->getMock();
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'error.log':
                return $this->logger;

            case 'orm.manager':
                return $this->em;
        }

        return null;
    }

    /**
     * Tests emptyItem when the item was not found.
     */
    public function testGetItemBySlug()
    {
        $content = new Content([
            'pk_content' => 1,
        ]);

        $this->service->expects($this->once())->method('getItemBy')
            ->with('slug regexp "(.+\"|^)content_slug(\".+|$)"')
            ->willReturn($content);

        $this->assertEquals($this->service->getItemBySlug('content_slug'), $content);
    }

    /**
     * Tests emptyItem when the item was not found.
     */
    public function testGetItemBySlugAndContentType()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_content_type' => 'opinion'
        ]);

        $this->service->expects($this->once())->method('getItemBy')
            ->with('slug regexp "(.+\"|^)content_slug(\".+|$)" and fk_content_type=2')
            ->willReturn($content);

        $this->assertEquals($this->service->getItemBySlugAndContentType('content_slug', 2), $content);
    }
}
