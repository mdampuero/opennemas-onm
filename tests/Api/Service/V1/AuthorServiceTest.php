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

use Api\Service\V1\AuthorService;
use Opennemas\Orm\Core\Entity;

/**
 * Defines test cases for AuthorService class.
 */
class AuthorServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('Common\Core\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getMetadata', 'getRepository', 'persist',
                'remove'
            ])->getMock();

        $this->fixer = $this->getMockBuilder('Fixer' . uniqid())
            ->setMethods([ 'addCondition', 'fix', 'getOql' ])
            ->getMock();

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getIdKeys' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'countBy', 'findBy', 'findOneBy'])
            ->getMock();

        $this->user = new Entity([
            'email'    => 'flob@garply.com',
            'id'       => 1,
            'name'     => 'flob',
            'password' => 'quux',
            'type'     => 1
        ]);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);
        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);
        $this->metadata->expects($this->any())->method('getIdKeys')
            ->willReturn([ 'id' ]);

        $this->service = new AuthorService($this->container, 'Opennemas\Orm\Core\Entity');
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.user':
                return $this->user;

            case 'orm.manager':
                return $this->em;

            case 'orm.oql.fixer':
                return $this->fixer;

            case 'core.dispatcher':
                return $this->dispatcher;
        }

        return null;
    }

    /**
     * Tests getItem when no error.
     */
    public function testGetItem()
    {
        $item = new Entity([ 'type' => 2 ]);

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('id = 1 and type != 1 and user_group_id = 3')->willReturn($item);

        $this->assertEquals($item, $this->service->getItem(1));
    }

    /**
     * Tests getItem when the provided id is empty.
     *
     * @expectedException \Api\Exception\GetItemException
     */
    public function testGetItemWhemEmptyId()
    {
        $this->service->getItem(0);
    }

    /**
     * Tests getItem when the item has no author property to true.
     *
     * @expectedException \Api\Exception\GetItemException
     */
    public function testGetItemWhenErrorWhenNoAuthor()
    {
        $this->repository->expects($this->once())->method('findOneBy')
            ->with('id = 1 and type != 1 and user_group_id = 3')
            ->will($this->throwException(new \Exception()));

        $this->service->getItem(1);
    }

    /*
     * Tests getOqlForList.
     */
    public function testGetOqlForList()
    {
        $method = new \ReflectionMethod($this->service, 'getOqlForList');
        $method->setAccessible(true);

        $this->fixer->expects($this->once())->method('fix')
            ->willReturn($this->fixer);
        $this->fixer->expects($this->once())->method('addCondition')
            ->with('type != 1 and user_group_id = 3')->willReturn($this->fixer);
        $this->fixer->expects($this->once())->method('getOql');

        $method->invokeArgs($this->service, [ [ 1, 3, 5 ] ]);
    }
}
