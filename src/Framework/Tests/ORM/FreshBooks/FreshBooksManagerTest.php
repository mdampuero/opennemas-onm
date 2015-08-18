<?php

namespace Framework\Tests\ORM\FreshBooks;

use Framework\ORM\FreshBooks\FreshBooksManager;
use Framework\ORM\Entity\Client;
use Freshbooks\FreshBooksApi;

class FreshBooksManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->manager = new FreshBooksManager(null, null);
    }

    public function testContructor()
    {
        $this->assertTrue($this->manager->getApi() instanceof FreshBooksApi);
    }

    /**
     * @expectedException Framework\ORM\Exception\InvalidRepositoryException
     */
    public function tesGetRepositoryInvalid()
    {
        $this->manager->getRepository('invalid_repository');
    }

    public function testGetRepositoryValid()
    {
        $cp = $this->manager->getRepository('client');
        $this->assertInstanceOf('Framework\ORM\FreshBooks\Repository\FreshBooksRepository', $cp);
    }

    public function testGetPersisterValid()
    {
        $entity = new Client();
        $cp = $this->manager->getPersister($entity);
        $this->assertInstanceOf('Framework\ORM\FreshBooks\Persister\FreshBooksPersister', $cp);
    }
}
