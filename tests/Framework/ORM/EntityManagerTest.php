<?php

namespace Framework\Tests\ORM;

use Framework\ORM\Braintree\BraintreeManager;
use Framework\ORM\Core\ChainElement;
use Framework\ORM\Database\DatabaseManager;
use Framework\ORM\Entity\Client;
use Framework\ORM\Entity\Invoice;
use Framework\ORM\Entity\Payment;
use Framework\ORM\EntityManager;
use Framework\ORM\FreshBooks\FreshBooksManager;

class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->dm = $this
            ->getMockBuilder('Framework\ORM\Database\DatabaseManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->bm = new BraintreeManager($factory);
        $this->fm = new FreshBooksManager(null, null);
        $this->em = new EntityManager($this->bm, $this->dm, $this->fm);
    }

    public function testConstructor()
    {
        $this->assertEquals($this->bm, $this->em->getBraintreeManager());
        $this->assertEquals($this->fm, $this->em->getFreshBooksManager());
        $this->assertEquals($this->fm, $this->em->getFreshBooksManager());
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidPersisterException
     */
    public function testGetPersisterInvalid()
    {
        $entity = $this->getMock('\Framework\ORM\Entity\Entity');

        $this->em->getPersister($entity);
    }

    public function testGetPersisterValid()
    {
        $entity = new Client();

        $persisters = $this->em->getPersister($entity, 'Braintree');

        $this->assertTrue(0 < count($persisters));
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryInvalid()
    {
        $this->em->getRepository('payment');
    }

    public function testGetRepositoryValid()
    {
        $persisters = $this->em->getRepository('client', 'Braintree');

        $this->assertTrue(0 < count($persisters));
    }

    public function testPersistWithExistingEntity()
    {
        $ftp = $this
            ->getMockBuilder('Framework\ORM\FreshBooks\Persister\FreshBooksPersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'create', 'update', 'remove' ])
            ->getMock();

        $ftp->expects($this->once())->method('update')->willReturn(false);

        $bm = $this->getMockBuilder('Framework\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->getMock();

        $dm = $this->getMockBuilder('Framework\ORM\Database\DatabaseManager')
            ->disableOriginalConstructor()
            ->getMock();

        $fm = $this->getMockBuilder('Framework\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($ftp);

        $em = new EntityManager($bm, $dm, $fm);

        $entity = new Invoice([ 'invoice_id' => 1 ], 'Freshbooks');

        $em->persist($entity);
    }

    public function testPersistWithUnexistingEntity()
    {
        $ftp = $this
            ->getMockBuilder('Framework\ORM\FreshBooks\Persister\FreshBooksPersister')
            ->disableOriginalConstructor()
            ->getMock();

        $ftp->expects($this->once())->method('create')->willReturn(false);

        $bm = $this->getMockBuilder('Framework\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->getMock();

        $dm = $this->getMockBuilder('Framework\ORM\Database\DatabaseManager')
            ->disableOriginalConstructor()
            ->getMock();

        $fm = $this->getMockBuilder('Framework\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($ftp);

        $em = new EntityManager($bm, $dm, $fm);

        $em->persist(new Invoice());
    }

    public function testRemoveWithExistingEntity()
    {
        $ftp = $this
            ->getMockBuilder('Framework\ORM\FreshBooks\Persister\FreshBooksPersister')
            ->disableOriginalConstructor()
            ->getMock();

        $ftp->expects($this->once())->method('remove')->willReturn(false);

        $bm = $this->getMockBuilder('Framework\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->getMock();

        $dm = $this->getMockBuilder('Framework\ORM\Database\DatabaseManager')
            ->disableOriginalConstructor()
            ->getMock();

        $fm = $this->getMockBuilder('Framework\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($ftp);

        $em = new EntityManager($bm, $dm, $fm);

        $em->remove(new Invoice([ 'invoice_id' => 1 ]));
    }

    /**
     * @expectedException \Framework\ORM\Exception\EntityNotFoundException
     */
    public function testRemoveWithUnexistingEntity()
    {
        $ftp = $this
            ->getMockBuilder('Framework\ORM\FreshBooks\Persister\FreshBooksPersister')
            ->disableOriginalConstructor()
            ->getMock();

        $ftp->expects($this->once())->method('remove')
            ->will(
                $this->throwException(new \Framework\ORM\Exception\EntityNotFoundException())
            );

        $bm = $this->getMockBuilder('Framework\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->getMock();

        $dm = $this->getMockBuilder('Framework\ORM\Database\DatabaseManager')
            ->disableOriginalConstructor()
            ->getMock();

        $fm = $this->getMockBuilder('Framework\ORM\FreshBooks\FreshBooksManager')
            ->disableOriginalConstructor()
            ->getMock();

        $fm->expects($this->once())->method('getPersister')->willReturn($ftp);

        $em = new EntityManager($bm, $dm, $fm);

        $em->remove(new Invoice());
    }

    public function testBuildChainWithoutElements()
    {
        $reflection = new \ReflectionClass(get_class($this->em));
        $method = $reflection->getMethod('buildChain');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->em, [ [] ]));
    }

    public function testBuildChainWithElements()
    {
        $a = new ChainElement();
        $b = new ChainElement();

        $elements = [ $a, $b ];

        $reflection = new \ReflectionClass(get_class($this->em));
        $method = $reflection->getMethod('buildChain');
        $method->setAccessible(true);

        $chain = $method->invokeArgs($this->em, [ $elements ]);
        $this->assertEquals($a, $chain);
        $this->assertEquals($b, $a->next());
        $this->assertFalse($b->hasNext());
    }
}
