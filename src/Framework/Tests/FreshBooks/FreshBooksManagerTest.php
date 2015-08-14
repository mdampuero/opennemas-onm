<?php

namespace Framework\Tests\FreshBooks;

use Framework\FreshBooks\FreshBooksManager;
use Framework\FreshBooks\Entity\Client;
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
     * @expectedException Framework\FreshBooks\Exception\InvalidRepositoryException
     */
    public function tesGetRepositoryInvalid()
    {
        $this->manager->getRepository('invalid_repository');
    }

    public function testGetRepositoryValid()
    {
        $cp = $this->manager->getRepository('client');
        $this->assertInstanceOf('Framework\FreshBooks\Repository\Repository', $cp);
    }

    public function testGetPersisterValid()
    {
        $entity = new Client();
        $cp = $this->manager->getPersister($entity);
        $this->assertInstanceOf('Framework\FreshBooks\Persister\Persister', $cp);
    }
}
