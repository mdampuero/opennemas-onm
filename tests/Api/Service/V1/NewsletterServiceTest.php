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
use Common\ORM\Core\Exception\EntityNotFoundException;

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

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getConverter' ,'getMetadata', 'getRepository', 'persist',
                'remove'
            ])->getMock();

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getIdKeys' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger' . uniqid())
            ->setMethods([ 'error' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'countBy', 'findBy', 'find'])
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
            'generated_html' => '<!DOCTYPE html>
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

        $item = $this->service->createItem($data);

        $this->assertEquals($data['title'], $item->title);
    }

    /**
     * Tests deleteList when no error.
     */
    public function testDeleteList()
    {
        $itemA = new Entity([ 'title' => 'wubble']);
        $itemB = new Entity([ 'title' => 'xyzzy' ]);

        $this->repository->expects($this->once())->method('findBy')
            ->with('id in [1,2]')
            ->willReturn([ $itemA, $itemB ]);
        $this->em->expects($this->exactly(2))->method('remove');

        $this->assertEquals(2, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when invalid list of ids provided.
     *
     * @expectedException Api\Exception\DeleteListException
     */
    public function testDeleteListWhenInvalidIds()
    {
        $this->service->deleteList('xyzzy');
    }

    /**
     * Tests deleteList when one error happens while removing.
     */
    public function testDeleteListWhenOneErrorWhileRemoving()
    {
        $itemA = new Entity([ 'title' => 'wubble']);
        $itemB = new Entity([ 'title' => 'xyzzy' ]);

        $this->repository->expects($this->once())->method('findBy')
            ->with('id in [1,2]')
            ->willReturn([ $itemA, $itemB ]);
        $this->em->expects($this->at(2))->method('remove');
        $this->em->expects($this->at(3))->method('remove')
            ->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->assertEquals(1, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when an error happens while searching.
     *
     * @expectedException Api\Exception\DeleteListException
    */
    public function testDeleteListWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('findBy')
            ->with('id in [1,2]')
            ->will($this->throwException(new \Exception()));

        $this->logger->expects($this->exactly(2))->method('error');

        $this->service->deleteList([ 1, 2 ]);
    }

    /**
     * Tests getItem when no error.
     */
    public function testGetItem()
    {
        $item = new Entity([ 'type' => 2 ]);

        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);

        $this->assertEquals($item, $this->service->getItem(1));
    }

    /**
     * Tests getItem when the item has no user property to true.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItemWhenErrorWhenNoUser()
    {
        $this->repository->expects($this->once())->method('find')
            ->with(1)
            ->will($this->throwException(new \Exception()));

        $this->service->getItem(1);
    }

    /**
     * Tests responsify with an item.
     */
    public function testResponsifyWithItem()
    {
        $entity = $this->getMockBuilder('Entity' . uniqid())
            ->setMethods([ 'eraseCredentials' ])
            ->getMock();

        $service = new NewsletterService($this->container, get_class($entity));

        $service->responsify($entity);
    }

    /**
     * Tests responsify with a list of items.
     */
    public function testResponsifyWithList()
    {
        $entity = $this->getMockBuilder('Entity' . uniqid())
            ->setMethods([ 'eraseCredentials' ])
            ->getMock();

        $service = new NewsletterService($this->container, get_class($entity));

        $service->responsify([ $entity, $entity ]);
    }

    /**
     * Tests responsify with a value that can not be responsified.
     */
    public function testResponsifyWithInvalidValues()
    {
        $this->assertEquals(null, $this->service->responsify(null));
        $this->assertEquals(1, $this->service->responsify(1));
        $this->assertEquals('glork', $this->service->responsify('glork'));
    }

    /*
     * Tests getOqlForList.
     */
    public function testGetSentNewslettersSinceLastInvoice()
    {
        $date = new \Datetime('2012-12-12 00:00:00');

        $itemA = new Entity([ 'title' => 'wubble', 'sends' => 5 ]);
        $itemB = new Entity([ 'title' => 'xyzzy', 'sends' => 10 ]);

        $this->repository->expects($this->once())->method('findBy')
            ->willReturn([ $itemA, $itemB ]);

        $this->assertEquals(15, $this->service->getSentNewslettersSinceLastInvoice($date));
    }
}
