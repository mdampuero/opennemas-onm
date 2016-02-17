<?php

namespace Framework\Tests\ORM\FreshBooks;

use Framework\ORM\FreshBooks\FreshBooksManager;
use Framework\ORM\Entity\Client;
use Framework\ORM\Entity\Entity;
use Framework\ORM\Entity\Payment;
use Freshbooks\FreshBooksApi;

class FreshBooksManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->manager = new FreshBooksManager(null, null);
    }

    /**
     * @expectedException Framework\ORM\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryInvalid()
    {
        $this->manager->getRepository('invalid_repository');
    }

    public function testGetRepositoryValid()
    {
        $cp = $this->manager->getRepository('client');
        $this->assertInstanceOf('Framework\ORM\FreshBooks\Repository\FreshBooksRepository', $cp);
    }

    /**
     * @expectedException Framework\ORM\Exception\InvalidPersisterException
     */
    public function testGetPersisterInvalid()
    {
        $entity = $this->getMock('Framework\ORM\Entity\Client');
        $cp = $this->manager->getPersister($entity);
        $this->assertInstanceOf('Framework\ORM\FreshBooks\Persister\FreshBooksPersister', $cp);
    }

    public function testGetPersisterValid()
    {
        $entity = new Client();
        $cp = $this->manager->getPersister($entity);
        $this->assertInstanceOf('Framework\ORM\FreshBooks\Persister\FreshBooksPersister', $cp);
    }

    public function testPersistWithExistingEntity()
    {
        $p = $this->getMockBuilder('Framework\ORM\FreshBooks\PersisterFreshBooksPersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'update', 'create' ])
            ->getMock();

        $p->expects($this->once())->method('update')->willReturn(true);

        $fm = $this
            ->getMockBuilder('Framework\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($p);

        $fm->persist(new Client([ 'client_id' => 1]));
    }

    public function testPersistWithUnexistingEntity()
    {
        $p = $this->getMockBuilder('Framework\ORM\FreshBooks\PersisterFreshBooksPersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'update', 'create' ])
            ->getMock();

        $p->expects($this->once())->method('create')->willReturn(true);

        $fm = $this
            ->getMockBuilder('Framework\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($p);

        $fm->persist(new Client());
    }

    public function testRemoveWithExistingEntity()
    {
        $p = $this->getMockBuilder('Framework\ORM\FreshBooks\PersisterFreshBooksPersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $p->method('remove')->willReturn(true);

        $fm = $this
            ->getMockBuilder('Framework\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($p);

        $fm->remove(new Client());
    }

    /**
     * @expectedException \Framework\ORM\Exception\EntityNotFoundException
     */
    public function testRemoveWithUnexistingEntity()
    {
        $p = $this->getMockBuilder('Framework\ORM\FreshBooks\PersisterFreshBooksPersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $p->method('remove')->will(
            $this->throwException(new \Framework\ORM\Exception\EntityNotFoundException())
        );

        $fm = $this
            ->getMockBuilder('Framework\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($p);

        $fm->remove(new Client());
    }
}
