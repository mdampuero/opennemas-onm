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

use Api\Service\V1\PollService;
use Common\Model\Entity\Content;

/**
 * Defines test cases for TagService class.
 */
class PollServiceTest extends \PHPUnit\Framework\TestCase
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
            ->setMethods([
                'getRepository', 'getMetaData'
            ])->getMock();

        $this->fm = $this->getMockBuilder('Opennemas\Data\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods(['filter', 'get', 'set'])
            ->getMock();

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getL10nKeys' ])
            ->getMock();

        $this->ph = $this->getMockBuilder('Common\Core\Component\Helper\PollHelper')
            ->setMethods([ 'getTotalVotes' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'find' ])->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);
        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);

        $this->items = [
            [
                'pk_item' => 1,
                'votes'   => 5,
                'item'    => 'Item1'
            ],
            [
                'pk_item' => 2,
                'votes'   => 2,
                'item'    => 'Item2'
            ],
            [
                'pk_item' => 3,
                'votes'   => 8,
                'item'    => 'Item3'
            ],
        ];

        $this->item = new Content([
            'pk_content'        => 1,
            'content_type_name' => 'poll',
            'items'             => $this->items,
            'closetime'         => date('Y-m-d H:i:s', strtotime('2021-08-30'))
        ]);

        $this->service = new PollService($this->container, 'Common\Model\Entity\Content');
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'core.helper.poll':
                return $this->ph;

            case 'orm.manager':
                return $this->em;

            case 'data.manager.filter':
                return $this->fm;
        }

        return null;
    }

    public function testPollGetItem()
    {
        $itemsPercent = [
            [
                'pk_item' => 1,
                'votes'   => 5,
                'item'    => 'Item1',
                'percent' => 33.33
            ],
            [
                'pk_item' => 2,
                'votes'   => 2,
                'item'    => 'Item2',
                'percent' => 13.33
            ],
            [
                'pk_item' => 3,
                'votes'   => 8,
                'item'    => 'Item3',
                'percent' => 53.33
            ],
        ];

        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($this->item);

        $this->fm->expects($this->at(0))->method('set')
            ->with($this->item->items)
            ->willReturn($this->fm);

        $this->fm->expects($this->at(1))->method('filter')
            ->willReturn($this->fm);

        $this->fm->expects($this->at(2))->method('get')
            ->willReturn($this->item->items);

        $this->ph->expects($this->once())->method('getTotalVotes')
            ->with($this->item)->willReturn([ '1' => 15 ]);

        $response = $this->service->getItem(1);

        $this->assertEquals($itemsPercent, $response->items);
    }
}
