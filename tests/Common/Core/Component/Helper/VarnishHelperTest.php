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

use Common\Core\Component\Helper\VarnishHelper;
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;
use Opennemas\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for VarnishHelper class.
 */
class VarnishHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->uh = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->queue = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->setMethods([ 'push' ])
            ->getMock();

        $this->helper = new VarnishHelper($this->uh, $this->queue);
    }

    /**
     * Tests deleteContents.
     */
    public function testDeleteContents()
    {
        $itemA = new \Attachment();
        $itemB = new \Attachment();

        $this->uh->expects($this->at(0))->method('generate')
            ->with($itemA)->willReturn('/plugh/norf.wubble');
        $this->uh->expects($this->at(1))->method('generate')
            ->with($itemB)->willReturn('/norf/flob.garply');

        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'req.url ~ /plugh/norf.wubble'
            ]));

        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'req.url ~ /norf/flob.garply'
            ]));

        $this->helper->deleteContents([ $itemA, $itemB ]);
    }

    /**
     * Tests deleteNewsstands.
     */
    public function testDeletNewsstands()
    {
        $itemA = new Content([
            'pk_content' => 10605,
            'path'       => 'plugh/norf.wubble'
        ]);

        $itemB = new Content([
            'pk_content' => 10883,
            'path'       => 'norf/flob.garply'
        ]);

        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'obj.http.x-tags ~ 10605'
            ]));

        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'req.url ~ plugh/norf.wubble'
            ]));

        $this->queue->expects($this->at(2))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'obj.http.x-tags ~ 10883'
            ]));

        $this->queue->expects($this->at(3))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'req.url ~ norf/flob.garply'
            ]));

        $this->helper->deleteNewsstands([ $itemA, $itemB ]);
    }

    /**
     * Tests deleteInstance.
     */
    public function testDeleteInstance()
    {
        $this->queue->expects($this->once())->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'obj.http.x-tags ~ instance-qux'
            ]));

        $this->helper->deleteInstance(new Instance([ 'internal_name' => 'qux' ]));
    }
}
