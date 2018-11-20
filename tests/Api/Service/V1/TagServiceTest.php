<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Api\Service\V1;

use Api\Service\V1\TagService;
use Common\ORM\Entity\Tag;

/**
 * Defines test cases for TagService class.
 */
class TagServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->converter = $this->getMockBuilder('Converter' . uniqid())
            ->setMethods([ 'objectify', 'responsify' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('EventDispatcher')
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getConverter', 'getMetadata', 'getRepository', 'persist'
            ])->getMock();

        $this->fm = $this->getMockBuilder('FilterManager')
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'getLocale' ])
            ->getMock();

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getId', 'getIdKeys' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'find' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);
        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);
        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->fm->expects($this->any())->method('filter')
            ->willReturn($this->fm);

        $this->fm->expects($this->any())->method('set')
            ->willReturn($this->fm);

        $this->service = new TagService($this->container, 'Common\ORM\Entity\Tag');
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'core.locale':
                return $this->locale;

            case 'data.manager.filter':
                return $this->fm;

            case 'orm.manager':
                return $this->em;
        }

        return null;
    }

    /**
     * Tests createItem.
     */
    public function testCreateItem()
    {
        $data = [ 'name' => 'Plugh' ];

        $this->converter->expects($this->any())->method('objectify')
            ->with(array_merge($data, [
                'slug'        => 'plugh',
                'language_id' => 'es_ES'
            ]))->willReturn(array_merge($data, [
                'slug'        => 'plugh',
                'language_id' => 'es_ES'
            ]));

        $this->locale->expects($this->once())->method('getLocale')
            ->with('frontend')->willReturn('es_ES');

        $this->em->expects($this->once())->method('persist');

        $this->fm->expects($this->once())->method('get')
            ->with()->willReturn('plugh');

        $item = $this->service->createItem($data);

        $this->assertEquals('Plugh', $item->name);
        $this->assertEquals('plugh', $item->slug);
    }

    /**
     * Tests updateItem.
     */
    public function testUpdateItem()
    {
        $data = [ 'name' => 'Plugh' ];
        $item = new Tag($data);

        $data = [ 'name' => 'Wibble' ];

        $this->converter->expects($this->any())->method('objectify')
            ->with(array_merge($data, [
                'slug'        => 'wibble',
                'language_id' => 'es_ES'
            ]))->willReturn(array_merge($data, [
                'slug'        => 'wibble',
                'language_id' => 'es_ES'
            ]));

        $this->locale->expects($this->once())->method('getLocale')
            ->with('frontend')->willReturn('es_ES');

        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);
        $this->em->expects($this->once())->method('persist');

        $this->fm->expects($this->once())->method('get')
            ->with()->willReturn('wibble');

        $this->service->updateItem(1, $data);

        $this->assertEquals('Wibble', $item->name);
        $this->assertEquals('wibble', $item->slug);
    }
}
