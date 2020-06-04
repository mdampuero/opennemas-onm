<?php

namespace Tests\Framework\Monolog;

use Common\Model\Entity\Instance;
use Common\Model\Entity\User;
use Framework\Monolog\OnmFormatter;

class OnmFormatterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('Converter' . uniqid())
            ->setMethods([ 'objectify', 'responsify' ])
            ->getMock();

        $this->conn->user     = 'root';
        $this->conn->password = 'root';

        $this->headers = $this->getMockBuilder('HeaderBag')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->setMethods([ 'getClientIps', 'getUri'])
            ->getMock();

        $this->globals = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods(['getMessage', 'getContext', 'getInstance', 'getRequest', 'getUser' ])
            ->getMock();

        $this->request->headers = $this->headers;

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->formatter = new OnmFormatter($this->container);
    }

    /**
     * Returns the mock basing on the requested service.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.globals':
                return $this->globals;
            case 'orm.connection.instance':
                return $this->conn;
        }

        return null;
    }

    /**
     * Tests processRecord when there is a request in process.
     */
    public function testProcessRecordWhenRequest()
    {
        $this->headers->expects($this->once())->method('get')
            ->with('User-Agent')->willReturn('glork/plugh');
        $this->globals->expects($this->any())->method('getMessage')
            ->with('root')->willReturn('<censored>');
        $this->globals->expects($this->any())->method('getContext')
            ->with('root')->willReturn('<censored>');
        $this->globals->expects($this->any())->method('getInstance')
            ->willReturn(new Instance([ 'internal_name' => 'fred' ]));
        $this->globals->expects($this->any())->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->once())->method('getClientIps')
            ->willReturn([ '143.53.0.1', '128.0.134.43' ]);
        $this->request->expects($this->once())->method('getUri')
            ->willReturn('http://norf.org/qux');

        $record = $this->formatter->processRecord(['message' => 'Link root root',
            'context' => ['Link root root']]);

        $this->assertEquals('fred', $record['extra']['instance']);
        $this->assertEquals('anon.', $record['extra']['user']);
        $this->assertEquals('128.0.134.43', $record['extra']['client_ip']);
        $this->assertEquals('glork/plugh', $record['extra']['user_agent']);
        $this->assertEquals('http://norf.org/qux', $record['extra']['url']);
        $this->assertEquals('["Link <censored> <censored>"]', $record['extra']['context']);
        $this->assertEquals('Link <censored> <censored>', $record['message']);
    }

    /**
     * Tests processRecord when there is no request in process.
     */
    public function testProcessRecordWhenNoRequest()
    {
        $this->globals->expects($this->any())->method('getInstance')
            ->willReturn(new Instance([ 'internal_name' => 'fred' ]));
        $this->globals->expects($this->any())->method('getRequest')
            ->willReturn(null);

        $record = $this->formatter->processRecord(['message' => 'Link root root',
            'context' => ['Link root root']]);

        $this->assertEquals('fred', $record['extra']['instance']);
        $this->assertEquals('anon.', $record['extra']['user']);
        $this->assertArrayHasKey('client_ip', $record['extra']);
        $this->assertArrayHasKey('user_agent', $record['extra']);
        $this->assertArrayHasKey('url', $record['extra']);
        $this->assertEquals('["Link <censored> <censored>"]', $record['extra']['context']);
        $this->assertEquals('Link <censored> <censored>', $record['message']);
    }

    /**
     * Tests getClientIp when the list of client ips is not empty.
     */
    public function testGetClientIp()
    {
        $this->request->expects($this->once())->method('getClientIps')
            ->willReturn([ '143.53.0.1', '128.0.134.43' ]);

        $method = new \ReflectionMethod($this->formatter, 'getClientIp');
        $method->setAccessible(true);

        $this->assertEquals(
            '128.0.134.43',
            $method->invokeArgs($this->formatter, [ $this->request ])
        );
    }

     /**
     * Tests getClientIp when the list of client ips is empty.
     */
    public function testGetClientIpWhenNoIps()
    {
        $this->request->expects($this->once())->method('getClientIps')
            ->willReturn([]);

        $method = new \ReflectionMethod($this->formatter, 'getClientIp');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->formatter, [ $this->request ]));
    }

    /**
     * Tests getInstance when an instance is loaded.
     */
    public function testGetInstance()
    {
        $this->globals->expects($this->any())->method('getInstance')
            ->willReturn(new Instance([ 'internal_name' => 'fred' ]));

        $method = new \ReflectionMethod($this->formatter, 'getInstance');
        $method->setAccessible(true);

        $this->assertEquals('fred', $method->invokeArgs($this->formatter, []));
    }

    /**
     * Tests getInstance when no instance loaded
     */
    public function testGetInstanceWhenNoInstance()
    {
        $this->globals->expects($this->any())->method('getInstance')
            ->willReturn(null);

        $method = new \ReflectionMethod($this->formatter, 'getInstance');
        $method->setAccessible(true);

        $this->assertEquals('unknown', $method->invokeArgs($this->formatter, []));
    }

    /**
     * Tests getUser when an user is authenticated.
     */
    public function testGetUser()
    {
        $this->globals->expects($this->any())->method('getUser')
            ->willReturn(new User([ 'email' => 'quux@wubble.com' ]));

        $method = new \ReflectionMethod($this->formatter, 'getUser');
        $method->setAccessible(true);

        $this->assertEquals('quux@wubble.com', $method->invokeArgs($this->formatter, []));
    }

    /**
     * Tests getUser when no user authenticated
     */
    public function testGetUserWhenNoUser()
    {
        $this->globals->expects($this->any())->method('getUser')
            ->willReturn(null);

        $method = new \ReflectionMethod($this->formatter, 'getUser');
        $method->setAccessible(true);

        $this->assertEquals('anon.', $method->invokeArgs($this->formatter, []));
    }
}
