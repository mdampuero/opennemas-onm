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
            ->setMethods([ 'countBy', 'removeContentsInTrash' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->service = new TrashService($this->container, 'Common\ORM\Entity\Content');
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
        $this->repository->expects($this->once())->method('countBy')
            ->with('in_litter = 1')->willReturn(10);

        $this->repository->expects($this->once())->method('removeContentsInTrash');

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
        $this->repository->expects($this->once())->method('countBy')
            ->with('in_litter = 1')->willReturn(0);

        $this->service->emptyTrash();
    }

    /**
     * Tests emptyTrash when the list is already empty.
     *
     * @expectedException Api\Exception\ApiException
     */
    public function testEmptyListWhenError()
    {
        $this->repository->expects($this->once())->method('countBy')
            ->with('in_litter = 1')->willReturn(10);

        $this->repository->expects($this->once())->method('removeContentsInTrash')
            ->will($this->throwException(new \Exception()));

        $this->service->emptyTrash();
    }

    public function testIsTrashEmpty()
    {
        $this->repository->expects($this->at(0))->method('countBy')
            ->with('in_litter = 1')->willReturn(0);
        $this->repository->expects($this->at(1))->method('countBy')
            ->with('in_litter = 1')->willReturn(10);


        $method = new \ReflectionMethod($this->service, 'isTrashEmpty');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->service, []));
        $this->assertFalse($method->invokeArgs($this->service, []));
    }

    /**
     * Tests isTrashEmpty when an error is thrown.
     *
     * @expectedException Api\Exception\ApiException
     */
    public function testIsTrashEmptyWhenError()
    {
        $this->repository->expects($this->once())->method('countBy')
            ->will($this->throwException(new \Exception()));

        $method = new \ReflectionMethod($this->service, 'isTrashEmpty');
        $method->setAccessible(true);

        $method->invokeArgs($this->service, []);
    }
}
