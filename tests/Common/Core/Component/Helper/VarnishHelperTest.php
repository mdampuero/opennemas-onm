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
use Common\ORM\Entity\Instance;

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

        $this->varnish = $this->getMockBuilder('Onm\Varnish\MessageExchanger')
            ->setMethods([ 'addBanMessage' ])
            ->getMock();

        $this->helper = new VarnishHelper($this->uh, $this->varnish);
    }

    /**
     * Tests deleteFiles.
     */
    public function testDeleteFiles()
    {
        $itemA = new \Attachment();
        $itemB = new \Attachment();

        $this->uh->expects($this->at(0))->method('generate')
            ->with($itemA)->willReturn('/plugh/norf.wubble');
        $this->uh->expects($this->at(1))->method('generate')
            ->with($itemB)->willReturn('/norf/flob.garply');

        $this->varnish->expects($this->at(0))->method('addBanMessage')
            ->with('req.url ~ /plugh/norf.wubble');
        $this->varnish->expects($this->at(1))->method('addBanMessage')
            ->with('req.url ~ /norf/flob.garply');

        $this->helper->deleteFiles([ $itemA, $itemB ]);
    }

    /**
     * Tests deleteInstance.
     */
    public function testDeleteInstance()
    {
        $this->varnish->expects($this->once())->method('addBanMessage')
            ->with('obj.http.x-tags ~ instance-qux');

        $this->helper->deleteInstance(new Instance([ 'internal_name' => 'qux' ]));
    }
}
