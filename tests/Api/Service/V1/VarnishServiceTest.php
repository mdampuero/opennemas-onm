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

use Api\Service\V1\VarnishService;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for VarnishService class.
 */
class VarnishServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'frog' ]);

        $this->varnish = $this->getMockBuilder('Common\Core\Component\Varnish\Varnish')
            ->disableOriginalConstructor()
            ->setMethods([ 'ban' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('Common\Core\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->tq = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->setMethods([ 'push' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->service = new VarnishService($this->container);
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
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'core.instance':
                return $this->instance;

            case 'core.varnish':
                return $this->varnish;

            case 'task.service.queue':
                return $this->tq;

            default:
                return null;
        }
    }

    /**
     * Tests createItem.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItem()
    {
        $this->service->createItem([ 16373 ]);
    }

    /**
     * Tests deleteItem when an error is thrown.
     *
     * @expectedException Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenError()
    {
        $this->varnish->expects($this->once())->method('ban')
            ->with('obj.http.x-tags ~ ^instance-frog.*,glorp.*')
            ->will($this->throwException(new \Exception()));

        $this->service->deleteItem('glorp');
    }

    /**
     * Tests deleteByPattern when no error is thrown and the input is not a
     * pattern.
     */
    public function testDeleteItemWhenNoError()
    {
        $this->varnish->expects($this->once())->method('ban')
            ->with('obj.http.x-tags ~ ^instance-frog.*,glorp.*');

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('varnish.deleteItem', [
                'action' => 'Api\Service\V1\VarnishService::deleteItem',
                'id' => 'obj.http.x-tags ~ ^instance-frog.*,glorp.*'
            ]);

        $this->service->deleteItem('glorp');
    }

    /**
     * Tests deleteList when an error is thrown.
     *
     * @expectedException Api\Exception\DeleteListException
     */
    public function testDeleteListWhenError()
    {
        $this->tq->expects($this->once())->method('push')
            ->will($this->throwException(new \Exception()));

        $this->service->deleteList([]);
    }

    /**
     * Tests deleteList when no error thrown.
     */
    public function testDeleteListWhenNoError()
    {
        $this->tq->expects($this->once())->method('push');

        $this->service->deleteList([]);
    }

    /**
     * Tests getConfig.
     *
     * @expectedException Api\Exception\ApiException
     */
    public function testGetConfig()
    {
        $this->service->getConfig();
    }

    /**
     * Tests getItem.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItem()
    {
        $this->service->getItem('norf');
    }

    /**
     * Tests getList.
     *
     * @expectedException Api\Exception\GetListException
     */
    public function testGetList()
    {
        $this->service->getList();
    }

    /**
     * Tests getListByIds.
     *
     * @expectedException Api\Exception\GetListException
     */
    public function testGetListByIds()
    {
        $this->service->getListByIds([ 1028 ]);
    }

    /**
     * Tests isPattern.
     */
    public function testIsPattern()
    {
        $this->assertFalse($this->service->isPattern('baz'));
    }

    /**
     * Tests patchItem.
     *
     * @expectedException Api\Exception\PatchItemException
     */
    public function testPatchItem()
    {
        $this->service->patchItem('fred', []);
    }

    /**
     * Tests patchList.
     *
     * @expectedException Api\Exception\PatchListException
     */
    public function testPatchList()
    {
        $this->service->patchList('fred', []);
    }

    /**
     * Tests responsify.
     */
    public function testResponsify()
    {
        $this->assertEquals('fubar', $this->service->responsify('fubar'));
    }

    /**
     * Tests updateConfig.
     *
     * @expectedException Api\Exception\ApiException
     */
    public function testUpdateConfig()
    {
        $this->service->updateConfig([]);
    }

    /**
     * Tests updateItem.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItem()
    {
        $this->service->updateItem('fred', []);
    }

    /**
     * Tests getEventName.
     */
    public function testGetEventName()
    {
        $method = new \ReflectionMethod($this->service, 'getEventName');
        $method->setAccessible(true);

        $this->assertEquals('varnish.waldo', $method->invokeArgs($this->service, [ 'waldo' ]));
    }
}
