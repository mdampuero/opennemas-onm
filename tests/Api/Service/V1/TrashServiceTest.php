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

use Api\Service\V1\TrashService;
use Common\ORM\Entity\Content;

/**
 * Defines test cases for TrashService class.
 */
class TrashServiceTest extends \PHPUnit\Framework\TestCase
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

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'countBy', 'findBy' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->service = $this->getMockBuilder('Api\Service\V1\TrashService')
            ->setConstructorArgs([ $this->container, 'Common\ORM\Entity\Content' ])
            ->setMethods([ 'deleteList', 'getList' ])
            ->getMock();
    }

    /**
     * Returns a mocked service basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'orm.manager':
                return $this->em;

            default:
                return null;
        }
    }

    /**
     * Tests emptyTrash.
     */
    public function testEmptyList()
    {
        $this->service->expects($this->once())->method('getList')
            ->with('in_litter = 1')->willReturn([
                'items' => [
                    new Content([ 'pk_content' => 1909 ]),
                    new Content([ 'pk_content' => 11991 ])
                ], 'total' => 2 ]);

        $this->service->expects($this->once())->method('deleteList')
            ->with([ 1909, 11991 ]);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('content.emptyTrash');

        $this->service->emptyTrash();
    }

    /**
     * Tests emptyTrash when the list is already empty.
     *
     * @expectedException Api\Exception\ApiException
     */
    public function testEmptyListWhenEmpty()
    {
        $this->service->expects($this->once())->method('getList')
            ->willReturn([ 'items' => [], 'total' => 0 ]);

        $this->service->emptyTrash();
    }

    /**
     * Tests emptyTrash when the list is already empty.
     *
     * @expectedException Api\Exception\ApiException
     */
    public function testEmptyListWhenError()
    {
        $this->service->expects($this->once())->method('getList')
            ->with('in_litter = 1')
            ->will($this->throwException(new \Exception()));

        $this->service->emptyTrash();
    }
}
