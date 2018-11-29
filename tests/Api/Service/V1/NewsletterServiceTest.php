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

use Api\Service\V1\NewsletterService;
use Common\ORM\Core\Entity;

/**
 * Defines test cases for UserService class.
 */
class NewsletterServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->converter = $this->getMockBuilder('Converter' . uniqid())
            ->setMethods([ 'objectify', 'responsify' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('EventDispatcher')
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getConverter' ,'getMetadata', 'getRepository', 'persist',
                'remove'
            ])->getMock();

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getId', 'getIdKeys' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger' . uniqid())
            ->setMethods([ 'error' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'countBy', 'findBy', 'find', 'getEntities'])
            ->getMock();

        $this->newsletter = new Entity([
            'title'      => 'flob@garply.com',
            'id'         => 1,
            'contents'   => [],
            'recipients' => [],
            'created'    => new \Datetime(),
            'updated'    => new \Datetime(),
        ]);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);
        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);
        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);
        $this->metadata->expects($this->any())->method('getIdKeys')
            ->willReturn([ 'id' ]);

        $this->service = new NewsletterService($this->container, 'Common\ORM\Core\Entity');
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

            case 'orm.oql.fixer':
                return $this->fixer;
        }
    }

    /**
     * Tests createItem when no error.
     */
    public function testCreateItem()
    {
        $data = [
            'title'          => 'Newsletter',
            'contents'       => [],
            'html' => '<!DOCTYPE html>
            <html>
            <head>
                <title></title>
            </head>
            <body>

            </body>
            </html>',
            'updated' => new \Datetime(),
        ];

        $this->converter->expects($this->any())->method('objectify')
            ->willReturn($data);
        $this->em->expects($this->once())->method('persist');

        $this->metadata->expects($this->once())->method('getId')
            ->willReturn([ 'id' => 1 ]);

        $item = $this->service->createItem($data);

        $this->assertEquals($data['title'], $item->title);
    }

    /*
     * Tests getSentNewslettersSinceLastInvoice.
     */
    public function testGetSentNewslettersSinceLastInvoice()
    {
        $date = new \Datetime('2012-12-12 00:00:00');

        $itemA = new Entity([ 'title' => 'wubble', 'sent_items' => 5 ]);
        $itemB = new Entity([ 'title' => 'xyzzy', 'sent_items' => 10 ]);

        $this->repository->expects($this->once())->method('findBy')
            ->willReturn([ $itemA, $itemB ]);

        $this->assertEquals(15, $this->service->getSentNewslettersSinceLastInvoice($date));
    }
}
