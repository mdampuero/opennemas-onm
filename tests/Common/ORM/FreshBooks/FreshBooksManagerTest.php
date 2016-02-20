<?php

namespace Framework\Tests\ORM\FreshBooks;

use Common\ORM\FreshBooks\FreshBooksManager;
use Common\ORM\Entity\Client;
use Common\ORM\Entity\Payment;
use Freshbooks\FreshBooksApi;

class FreshBooksManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->manager = new FreshBooksManager(null, null);
    }

    public function testContructor()
    {
        $this->assertInstanceOf('Freshbooks\FreshBooksApi', $this->manager->getApi());
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryInvalid()
    {
        $this->manager->getRepository('invalid_repository');
    }

    public function testGetRepositoryValid()
    {
        $cp = $this->manager->getRepository('client');
        $this->assertInstanceOf('Common\ORM\FreshBooks\Repository\FreshBooksRepository', $cp);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidPersisterException
     */
    public function testGetPersisterInvalid()
    {
        $entity = new Payment();
        $cp = $this->manager->getPersister($entity);
        $this->assertInstanceOf('Common\ORM\FreshBooks\Persister\FreshBooksPersister', $cp);
    }

    public function testGetPersisterValid()
    {
        $entity = new Client();
        $cp = $this->manager->getPersister($entity);
        $this->assertInstanceOf('Common\ORM\FreshBooks\Persister\FreshBooksPersister', $cp);
    }

    public function testPersistWithExistingEntity()
    {
        $p = $this->getMockBuilder('Common\ORM\FreshBooks\PersisterFreshBooksPersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'update', 'create' ])
            ->getMock();

        $p->expects($this->once())->method('update')->willReturn(true);

        $fm = $this
            ->getMockBuilder('Common\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($p);

        $fm->persist(new Client([ 'client_id' => 1]));
    }

    public function testPersistWithUnexistingEntity()
    {
        $p = $this->getMockBuilder('Common\ORM\FreshBooks\PersisterFreshBooksPersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'update', 'create' ])
            ->getMock();

        $p->expects($this->once())->method('create')->willReturn(true);

        $fm = $this
            ->getMockBuilder('Common\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($p);

        $fm->persist(new Client());
    }

    public function testRemoveWithExistingEntity()
    {
        $p = $this->getMockBuilder('Common\ORM\FreshBooks\PersisterFreshBooksPersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $p->method('remove')->willReturn(true);

        $fm = $this
            ->getMockBuilder('Common\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($p);

        $fm->remove(new Client());
    }

    /**
     * @expectedException \Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testRemoveWithUnexistingEntity()
    {
        $p = $this->getMockBuilder('Common\ORM\FreshBooks\PersisterFreshBooksPersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $p->method('remove')->will(
            $this->throwException(new \Common\ORM\Core\Exception\EntityNotFoundException('Client', 1, ''))
        );

        $fm = $this
            ->getMockBuilder('Common\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($p);

        $fm->remove(new Client());
    }
}
