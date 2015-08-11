<?php

namespace Framework\Tests\FreshBooks;

use Framework\FreshBooks\Repository\Repository;
use Freshbooks\FreshBooksApi;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->api        = $this->getMock('FreshBooksApi');
        $this->repository = new Repository($this->api);
    }

    public function testContructor()
    {
        $this->assertEquals($this->repository->getApi(), $this->api);
    }
}
