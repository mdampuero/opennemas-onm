<?php

namespace Tests\Api\Helper\Cache;

use Api\Helper\Cache\CacheHelper;
use Common\Model\Entity\Instance;
use Opennemas\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for CacheHelper class.
 */
class CacheHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'bar' ]);

        $this->queue = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->disableOriginalConstructor()
            ->setMethods([ 'push' ])
            ->getMock();

        $this->helper = new CacheHelper($this->instance, $this->queue);
    }

    /**
     * Tests deleteDynamicCss.
     */
    public function testDeleteDynamicCss()
    {
        $this->queue->expects($this->at(0))->method('push')->with(new ServiceTask(
            'core.service.assetic.dynamic_css',
            'deleteTimestamp',
            [ '%global%' ]
        ));

        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.template.cache', 'delete', [
                'css', 'global'
            ]));

        $this->assertEquals($this->helper, $this->helper->deleteDynamicCss());
    }

    /**
     * Tests deleteInstance.
     */
    public function testDeleteInstance()
    {
        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.template.cache', 'deleteAll', []));

        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'obj.http.x-tags ~ ^instance-bar'
            ]));

        $this->assertEquals($this->helper, $this->helper->deleteInstance());
    }
}
