<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\InstanceHelper;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for InstanceHelper class.
 */
class InstanceHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->created  = new \DateTime('2010-10-10 10:10:10');
        $this->instance = new Instance([
            'created'       => $this->created,
            'internal_name' => 'thud',
            'settings'      => [ 'BD_DATABASE' => 3441 ]
        ]);

        $this->client = $this->getMockBuilder('GuzzleHttp\Client')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('Opennemas\Orm\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll', 'fetchAssoc', 'selectDatabase' ])
            ->getMock();

        $this->helper = new InstanceHelper($this->conn, '/corge/glorp', [
            'url'   => 'http://flob.com',
            'token' => 'corgebazfrog',
        ]);
    }

    /**
     * Tests countContents when no error thrown.
     */
    public function testCountContentsWhenError()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);
        $this->conn->expects($this->once())->method('fetchAll')
            ->will($this->throwException(new \Exception()));

        $this->assertEmpty($this->helper->countContents($this->instance));
    }

    /**
     * Tests countContents when error thrown.
     */
    public function testCountContentsWhenNoError()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);
        $this->conn->expects($this->once())->method('fetchAll')
            ->willReturn([
                [ 'content_type_name' => 'article', 'total' => 27529 ],
                [ 'content_type_name' => 'opinion', 'total' => 24102 ],
            ]);

        $this->assertEquals([
            'article' => 27529,
            'opinion' => 24102
        ], $this->helper->countContents($this->instance));
    }

    /**
     * Tests countUsers when no error thrown.
     */
    public function testCountUsersWhenError()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);

        $this->conn->expects($this->once())->method('fetchAssoc')
            ->will($this->throwException(new \Exception()));

        $this->assertEmpty($this->helper->countUsers($this->instance));
    }

    /**
     * Tests countUsers when error thrown.
     */
    public function testCountUsersWhenNoError()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);

        $this->conn->expects($this->once())->method('fetchAssoc')
            ->willReturn([ 'total' => 19526 ]);

        $this->assertEquals(19526, $this->helper->countUsers($this->instance));
    }

    /**
     * Tests getCreated when no error thrown.
     */
    public function testGetCreatedWhenError()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);

        $this->conn->expects($this->once())->method('fetchAssoc')
            ->will($this->throwException(new \Exception()));

        $this->assertEquals($this->created, $this->helper->getCreated($this->instance));
    }

    /**
     * Tests getCreated when error thrown.
     */
    public function testGetCreatedWhenNoError()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);

        $this->conn->expects($this->once())->method('fetchAssoc')
            ->willReturn([ 'value' => '2014-01-01 10:00:00' ]);

        $this->assertEquals(
            new \DateTime('2014-01-01 10:00:00'),
            $this->helper->getCreated($this->instance)
        );
    }

    /**
     * Tests getLastActivity.
     */
    public function testGetLastActivity()
    {
        $helper = $this->getMockBuilder('Common\Core\Component\Helper\InstanceHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getLastAuthentication', 'getLastCreated' ])
            ->getMock();

        $helper->expects($this->once())->method('getLastAuthentication')
            ->with($this->instance)
            ->willReturn(new \DateTime('2018-02-11 17:18:19'));

        $helper->expects($this->once())->method('getLastCreated')
            ->with($this->instance)
            ->willReturn(new \DateTime('2019-10-11 17:00:00'));

        $this->assertEquals(
            new \DateTime('2019-10-11 17:00:00'),
            $helper->getLastActivity($this->instance)
        );
    }

    /**
     * Tests getLastAuthentication when empty.
     */
    public function testGetLastAuthenticationWhenEmpty()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);
        $this->conn->expects($this->once())->method('fetchAssoc')
            ->with('select value from settings where name = "last_login"')
            ->willReturn([ 'value' => serialize('') ]);

        $method = new \ReflectionMethod($this->helper, 'getLastAuthentication');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->helper, [ $this->instance ]));
    }

    /**
     * Tests getLastAuthentication when error.
     */
    public function testGetLastAuthenticationWhenError()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);
        $this->conn->expects($this->once())->method('fetchAssoc')
            ->with('select value from settings where name = "last_login"')
            ->will($this->throwException(new \Exception));

        $method = new \ReflectionMethod($this->helper, 'getLastAuthentication');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->helper, [ $this->instance ]));
    }

    /**
     * Tests getLastAuthentication when no error.
     */
    public function testGetLastAuthenticationWhenNoError()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);
        $this->conn->expects($this->once())->method('fetchAssoc')
            ->with('select value from settings where name = "last_login"')
            ->willReturn([ 'value' => serialize('2010-10-10 10:00:00') ]);

        $method = new \ReflectionMethod($this->helper, 'getLastAuthentication');
        $method->setAccessible(true);

        $this->assertEquals(
            new \DateTime('2010-10-10 10:00:00'),
            $method->invokeArgs($this->helper, [ $this->instance ])
        );
    }

    /**
     * Tests getLastCreated when error.
     */
    public function testGetLastCreatedWhenError()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);
        $this->conn->expects($this->once())->method('fetchAssoc')
            ->with('select created from contents order by created desc limit 1')
            ->will($this->throwException(new \Exception));

        $method = new \ReflectionMethod($this->helper, 'getLastCreated');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->helper, [ $this->instance ]));
    }

    /**
     * Tests getLastCreated when no error.
     */
    public function testGetLastCreatedWhenNoError()
    {
        $this->conn->expects($this->once())->method('selectDatabase')
            ->with(3441);
        $this->conn->expects($this->once())->method('fetchAssoc')
            ->with('select created from contents order by created desc limit 1')
            ->willReturn([ 'created' => serialize('2010-10-10 10:00:00') ]);

        $method = new \ReflectionMethod($this->helper, 'getLastCreated');
        $method->setAccessible(true);

        $this->assertEquals(
            new \DateTime('2010-10-10 10:00:00'),
            $method->invokeArgs($this->helper, [ $this->instance ])
        );
    }
}
