<?php

namespace Tests\Api\Service\V1;

use Api\Exception\GetListException;
use Api\Service\V1\OpinionService;
use Common\Model\Entity\User;

/**
 * Defines test cases for the OpinionService class.
 */
class OpinionServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->authorService = $this->getMockBuilder('Api\Service\V1\AuthorService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getList' ])
            ->getMock();

        $this->oqlFixer = $this->getMockBuilder('Opennemas\Orm\Core\Oql\Fixer')
            ->disableOriginalConstructor()
            ->setMethods([ 'fix', 'addCondition', 'getOql' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->service = new OpinionService($this->container, 'Common\Model\Entity\Content');

        $this->method = new \ReflectionMethod($this->service, 'getOqlForList');
        $this->method->setAccessible(true);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.author':
                return $this->authorService;

            case 'orm.oql.fixer':
                return $this->oqlFixer;
        }

        return null;
    }

    /**
     * Tests getOqlForList when there are no matches.
     */
    public function testGetOqlForListWhenEmptyMatches()
    {
        $this->assertEquals(
            'glorp = 1',
            $this->method->invokeArgs($this->service, [ 'glorp = 1' ])
        );
    }

    /**
     * Tests getOqlForList when there is an error in the api service query.
     */
    public function testGetOqlForListWhenError()
    {
        $this->authorService->expects($this->once())->method('getList')
            ->with('is_blog = 1')
            ->will($this->throwException(new GetListException()));

        $this->assertEquals(
            'glorp = 1 and blog = 0',
            $this->method->invokeArgs($this->service, [ 'glorp = 1 and blog = 0' ])
        );
    }

    /**
     * Tests getOqlForList when there are no bloggers.
     */
    public function testGetOqlForListWhenNoBloggers()
    {
        $this->authorService->expects($this->once())->method('getList')
            ->with('is_blog = 1')
            ->willReturn([ 'items' => [] ]);

        $this->assertEquals(
            'glorp = 1 and blog = 1',
            $this->method->invokeArgs($this->service, [ 'glorp = 1 and blog = 1' ])
        );
    }

    /**
     * Tests getOqlForList when the oql is fixed correctly.
     */
    public function testGetOqlForListWhenFixed()
    {
        $this->authorService->expects($this->once())->method('getList')
            ->with('is_blog = 1')
            ->willReturn([ 'items' => [ new User([ 'id' => 1 ]) ]]);

        $this->oqlFixer->expects($this->once())->method('fix')
            ->with('glorp = 1 ')
            ->willReturn($this->oqlFixer);

        $this->oqlFixer->expects($this->once())->method('addCondition')
            ->with('fk_author in [1]')
            ->willReturn($this->oqlFixer);

        $this->oqlFixer->expects($this->once())->method('getOql')
            ->willReturn('glorp = 1 and fk_author in [1]');

        $this->assertEquals(
            'glorp = 1 and fk_author in [1]',
            $this->method->invokeArgs($this->service, [ 'glorp = 1 and blog = 1' ])
        );
    }
}
