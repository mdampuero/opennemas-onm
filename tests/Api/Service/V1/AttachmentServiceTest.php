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

use Api\Exception\CreateItemException;
use Api\Exception\UpdateItemException;
use Api\Exception\DeleteItemException;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Content;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AttachmentServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'flob' ]);

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->converter = $this->getMockBuilder('Converter' . uniqid())
            ->setMethods([ 'objectify', 'responsify' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('Common\Core\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([ 'getConverter', 'getMetadata', 'getRepository', 'persist', 'remove'])
            ->getMock();

        $this->fm = $this->getMockBuilder('Opennemas\Data\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods(['filter', 'get', 'set'])
            ->getMock();

        $this->il = $this->getMockBuilder('Common\Core\Component\Loader\InstanceLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'getInstance' ])
            ->getMock();

        $this->ah = $this->getMockBuilder('Common\Core\Component\Helper\AttachmentHelper')
            ->setConstructorArgs([ $this->il, '/wibble/flob' ])
            ->setMethods([ 'generatePath', 'getRelativePath', 'move', 'remove', 'exists' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $this->il->expects($this->any())->method('getInstance')
            ->willReturn($this->instance);

        $this->security = $this->getMockBuilder('Sercurity')
            ->setMethods([ 'hasPermission' ])
            ->getMock();

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getId', 'getIdKeys', 'getL10nKeys' ])
            ->getMock();

        $this->file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);

        $this->service = $this->getMockBuilder('Api\Service\V1\AttachmentService')
            ->setConstructorArgs([ $this->container, 'Common\Model\Entity\Content' ])
            ->setMethods([ 'getItem', 'assignUser' ])
            ->getMock();
    }

    /**
     * Returns a mocked service basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'orm.manager':
                return $this->em;

            case 'core.helper.attachment':
                return $this->ah;

            case 'data.manager.filter':
                return $this->fm;

            case 'core.security':
                return $this->security;

            default:
                return null;
        }
    }

    /**
     * Tests createItem when no file provided
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItemWhenNoFile()
    {
        $this->service->createItem([ 'title' => 'waldo' ]);
    }

    /**
     * Tests createItem when file already exists.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItemWhenFileAlreadyExists()
    {
        $this->ah->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(true);

        $this->service->createItem([ 'title' => 'waldo' ], $this->file);
    }

    /**
     * Tests createItem when file already exists.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItemWhenErrorWithFile()
    {
        $this->ah->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(false);

        $this->security->expects($this->any())->method('hasPermission')
            ->willReturn(true);

        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);

        $this->ah->expects($this->once())->method('move')
            ->willReturn(new CreateItemException());

        $this->service->createItem([ 'title' => 'waldo' ], $this->file);
    }

    /**
     * Tests createItem when successful upload.
     */
    public function testCreateItemWhenSuccessfulUpload()
    {
        $this->service->expects($this->any())->method('assignUser')
            ->willReturn([ 'name' => 'flob', 'changed' => 'foo', 'bar' => 1 ]);

        $this->ah->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ah->expects($this->once())->method('getRelativePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(false);

        $this->security->expects($this->any())->method('hasPermission')
            ->willReturn(true);

        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);

        $this->converter->expects($this->any())->method('objectify')
            ->with($this->arrayHasKey('changed'))
            ->will($this->returnArgument(0));

        $this->metadata->expects($this->once())->method('getId')
            ->willReturn([ 'id' => 1 ]);

        $this->service->createItem([ 'title' => 'waldo' ], $this->file);
    }

    /**
     * Tests updateItem when an error while moving the file is thrown.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenErrorWithFile()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_content_type' => 'attachment'
        ]);

        $this->service->expects($this->any())->method('getItem')
            ->willReturn($content);

        $this->ah->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(false);

        $this->security->expects($this->any())->method('hasPermission')
            ->willReturn(true);

        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);

        $this->ah->expects($this->once())->method('move')
            ->willReturn(new UpdateItemException());

        $this->service->updateItem(1, [ 'title' => 'waldo' ], $this->file);
    }

    /**
     * Tests updateItem when file already exists.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenFileAlreadyExists()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_content_type' => 'attachment'
        ]);

        $this->service->expects($this->any())->method('getItem')
            ->willReturn($content);

        $this->ah->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(true);

        $this->service->updateItem(1, [ 'title' => 'waldo' ], $this->file);
    }

    /**
     * Tests updateItem when not empty relative path.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenNotEmptyRelativePath()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_content_type' => 'attachment'
        ]);

        $this->service->expects($this->any())->method('getItem')
            ->willReturn($content);

        $this->ah->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(false);

        $this->security->expects($this->any())->method('hasPermission')
            ->willReturn(true);

        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);

        $this->ah->expects($this->any())->method('remove')
            ->willReturn(new UpdateItemException());

        $this->service->updateItem(1, [ 'title' => 'waldo' ], $this->file);
    }

    /**
     * Tests updateItem when successful update.
     */
    public function testUpdateItemWhenSuccessfulUpdate()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_content_type' => 'attachment',
            'path' => '/2010/01/01/plugh.mumble'
        ]);

        $this->service->expects($this->any())->method('getItem')
            ->willReturn($content);

        $this->converter->expects($this->any())->method('objectify')
            ->with($this->arrayHasKey('changed'))
            ->will($this->returnArgument(0));

        $this->service->expects($this->any())->method('assignUser')
            ->willReturn([ 'name' => 'mumble', 'changed' => 'foo', 'bar' => 1 ]);

        $this->ah->expects($this->once())->method('generatePath')
            ->willReturn('/2010/01/01/plugh.mumble');

        $this->ah->expects($this->once())->method('exists')
            ->willReturn(false);

        $this->security->expects($this->any())->method('hasPermission')
            ->willReturn(true);

        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);

        $this->service->updateItem(1, [ 'title' => 'waldo' ], $this->file);
    }

    /**
     * Tests updateItem when no file nor existing path provided.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenNoFileNorPath()
    {
        $this->service->updateItem(1, [ 'title' => 'waldo' ]);
    }


    /**
     * Tests deleteItem when no error.
     */
    public function testDeleteItem()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_content_type' => 'attachment',
            'path' => '/2010/01/01/plugh.mumble'
        ]);

        $this->service->expects($this->any())->method('getItem')
            ->willReturn($content);

        $this->em->expects($this->once())->method('remove')
            ->with($content);

        $this->service->deleteItem(1);
    }

    /**
     * Tests deleteItem when error.
     *
     * @expectedException Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenError()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_content_type' => 'attachment',
            'path' => '/2010/01/01/plugh.mumble'
        ]);

        $this->service->expects($this->any())->method('getItem')
            ->willReturn($content);

        $this->em->expects($this->any())->method('remove')
            ->with($content->path)
            ->willReturn(new DeleteItemException());

        $this->service->deleteItem(1);
    }
}
