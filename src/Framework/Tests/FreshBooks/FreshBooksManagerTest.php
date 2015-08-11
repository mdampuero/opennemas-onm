<?php

namespace Framework\Tests\FreshBooks;

use Framework\FreshBooks\FreshBooksManager;
use Framework\FreshBooks\Repository\ClientRepository;
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
    public function testInvalidRepository()
    {
        $this->manager->getRepository('invalid_repository');
    }

    public function testValidRepository()
    {
        $rp = $this->manager->getRepository('client');
        $this->assertTrue($rp instanceof ClientRepository);
    }
}
