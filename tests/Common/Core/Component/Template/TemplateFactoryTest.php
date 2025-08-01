<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Template;

use Common\Core\Component\Template\TemplateFactory;
use Common\Model\Entity\Instance;
use Opennemas\Orm\Database\Repository\BaseRepository;

/**
 * Defines test cases for TemplateFactory class.
 */
class TemplateFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'settings' => [
            'TEMPLATE_USER' => 'frog'
        ] ]);


        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get', 'getParameter', 'hasParameter' ])
            ->getMock();

        $this->rs = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->globals = $this->getMockBuilder('GlobalVariables')
            ->setMethods([ 'getInstance', 'setInstance' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->repo = $this->getMockBuilder('Repository')
            ->setMethods([ 'findOneBy' ])
            ->getMock();

        $this->cache = $this->getMockBuilder('CacheManager')
            ->setMethods([ 'write', 'setPath' ])
            ->getMock();

        $this->watcher = $this->getMockBuilder('Stopwatch')
            ->setMethods([ 'start', 'stop' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->globals->expects($this->any())->method('getInstance')
            ->willReturn($this->instance);

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repo);

        $this->factory = new TemplateFactory($this->container, $this->watcher);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.globals':
                return $this->globals;
            case 'request_stack':
                return $this->rs;
            case 'core.template.cache':
                return $this->cache;
            case 'orm.manager':
                return $this->em;
        }

        return null;
    }

    /**
     * Tests constructor when not in development environment.
     */
    public function testConstructorWhenInDevelopment()
    {
        $this->container->expects($this->once())->method('getParameter')
            ->with('kernel.environment')->willReturn('dev');

        $factory = new TemplateFactory($this->container, $this->watcher);

        $property = new \ReflectionProperty($factory, 'watcher');
        $property->setAccessible(true);

        $this->assertEquals($this->watcher, $property->getValue($factory));
    }

    /**
     * Tests constructor when not in development environment.
     */
    public function testConstructorWhenNotDevelopment()
    {
        $property = new \ReflectionProperty($this->factory, 'watcher');
        $property->setAccessible(true);

        $this->assertEmpty($property->getValue($this->factory));
    }

    /**
     * Tests __call when in development mode.
     */
    public function testCallWhenInDevelopment()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->container->expects($this->once())->method('getParameter')
            ->with('kernel.environment')->willReturn('dev');

        $factory = $this->getMockBuilder('Common\Core\Component\Template\TemplateFactory')
            ->setConstructorArgs([ $this->container, $this->watcher ])
            ->setMethods([ 'get' ])
            ->getMock();

        $factory->expects($this->once())->method('get')
            ->willReturn($template);

        $template->expects($this->once())->method('fetch')
            ->with('flob.tpl', [ 'mumble' ])->willReturn('baz');

        $this->watcher->expects($this->once())->method('start')
            ->with('template (flob.tpl)');
        $this->watcher->expects($this->once())->method('stop')
            ->with('template (flob.tpl)');

        $this->assertEquals('baz', $factory->fetch('flob.tpl', [ 'mumble' ]));
    }

    /**
     * Tests get when a template already exists.
     */
    public function testGetWhenExists()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->getMock();

        $property = new \ReflectionProperty($this->factory, 'templates');
        $property->setAccessible(true);

        $property->setValue($this->factory, [ 'mumble' => $template ]);

        $this->assertEquals($template, $this->factory->get('mumble'));
    }

    /**
     * Tests get when a template does not exist and has to be created.
     */
    public function testGetWhenNotExists()
    {
        $template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getMockBuilder('Common\Core\Component\Template\TemplateFactory')
            ->setConstructorArgs([ $this->container, $this->watcher ])
            ->setMethods([ 'getTemplate' ])
            ->getMock();

        $factory->expects($this->once())->method('getTemplate')
            ->with('mumble')->willReturn($template);

        $this->assertEquals($template, $factory->get('mumble'));
    }

    /**
     * Tests getBundleName where there is no request in progress.
     */
    public function testGetBundleNameWhenNoRequest()
    {
        $method = new \ReflectionMethod($this->factory, 'getBundleName');
        $method->setAccessible(true);

        $this->assertEquals('frontend', $method->invokeArgs($this->factory, []));
    }

    /**
     * Tests getBundleName where there is a request in progress.
     */
    public function testGetBundleNameWhenRequest()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $method = new \ReflectionMethod($this->factory, 'getBundleName');
        $method->setAccessible(true);

        $this->container->expects($this->once())->method('get')
            ->with('request_stack')->willReturn($this->rs);

        $request->expects($this->once())->method('get')
            ->with('_controller')->willReturn('Frog\GarplyController');

        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($request);

        $this->assertEquals('frog', $method->invokeArgs($this->factory, []));
    }

    /**
     * Tests getInternalName for multiple frontend cases.
     */
    public function testGetInternalNameWhenInFrontend()
    {
        $method = new \ReflectionMethod($this->factory, 'getInternalName');
        $method->setAccessible(true);

        $this->container->expects($this->once())->method('get')
            ->with('request_stack')->willReturn($this->rs);

        $this->assertEquals('frontend', $method->invokeArgs($this->factory, [ 'frontend' ]));
        $this->assertEquals('frontend', $method->invokeArgs($this->factory, [ 'Frontend' ]));
        $this->assertEquals('frontend', $method->invokeArgs($this->factory, [ null ]));
    }

    /**
     * Tests getInternalName for multiple not-in-frontend cases.
     */
    public function testGetInternalNameWhenNotInFrontend()
    {
        $method = new \ReflectionMethod($this->factory, 'getInternalName');
        $method->setAccessible(true);

        $this->assertEquals('garply', $method->invokeArgs($this->factory, [ 'core.template.garply' ]));
        $this->assertEquals('admin', $method->invokeArgs($this->factory, [ 'backend' ]));
        $this->assertEquals('manager', $method->invokeArgs($this->factory, [ 'managerwebservice' ]));
    }

    /**
     * Tests getTemplate.
     */
    public function testGetTemplateWhenInFrontend()
    {
        $method = new \ReflectionMethod($this->factory, 'getTemplate');
        $method->setAccessible(true);

        $this->container->expects($this->at(1))->method('hasParameter')
            ->with('core.template.frontend.filters')->willReturn(true);
        $this->container->expects($this->at(2))->method('getParameter')
            ->with('core.template.frontend.filters')->willReturn([]);

        $this->em->expects($this->once())->method('getRepository')
            ->with('theme', 'file')->willReturn($this->repo);

        $this->repo->expects($this->once())->method('findOneBy')
            ->will($this->throwException(new \Exception()));

        $method->invokeArgs($this->factory, [ 'frontend' ]);
    }
}
