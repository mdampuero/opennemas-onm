<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Api\Validator\V1;

use Api\Validator\V1\TagValidator;
use Common\ORM\Core\Entity;

/**
 * Defines test cases for TagValidator class.
 */
class TagValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->tagService = $this->getMockBuilder('TagService')
            ->setMethods([ 'getItemBy' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->coreValidator = $this->getMockBuilder('CoreValidator')
            ->setMethods([ 'validate' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->validator = new TagValidator($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.tag':
                return $this->tagService;

            case 'core.validator':
                return $this->coreValidator;

            default:
                return null;
        }
    }

    /**
     * Tests validate when the provided information is valid.
     */
    public function testValidateWhenValidExistingTag()
    {
        $item = new Entity([ 'name' => 'flob', 'language_id' => 'es_ES' ]);
        $item->refresh();

        $this->coreValidator->expects($this->any())->method('validate')
            ->willReturn([]);

        $this->validator->validate($item);
    }

    /**
     * Tests validate when the provided information is valid.
     */
    public function testValidateWhenValidNewTag()
    {
        $this->coreValidator->expects($this->any())->method('validate')
            ->willReturn([]);

        $this->tagService->expects($this->at(0))->method('getItemBy')
            ->with('name = "flob" and language_id = "es_ES"')
            ->will($this->throwException(new \Exception()));

        $this->tagService->expects($this->at(1))->method('getItemBy')
            ->with('name = "plugh" and language_id = "es_ES"')
            ->willReturn(new Entity([ 'name' => 'Plugh' ]));


        $this->validator->validate(new Entity([
            'name'        => 'flob',
            'language_id' => 'es_ES'
        ]));

        $this->validator->validate(new Entity([
            'name'        => 'plugh',
            'language_id' => 'es_ES'
        ]));
    }

    /**
     * Tests validate when the provided information is not valid.
     *
     * @expectedException \Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenNameInvalid()
    {
        $item = new Entity([ 'name' => 'flob', 'language_id' => 'es_ES' ]);
        $item->refresh();

        $this->coreValidator->expects($this->once())->method('validate')
            ->willReturn([ 'error1' => 'plugh fred' ]);

        $this->validator->validate($item);
    }

    /**
     * Tests validate when the provided information is valid but there is an
     * existing tag with the same information.
     *
     * @expectedException \Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenTagAlreadyExists()
    {
        $this->coreValidator->expects($this->once())->method('validate')
            ->willReturn([]);

        $this->tagService->expects($this->once())->method('getItemBy')
            ->with('name = "baz" and language_id = "es_ES"')
            ->willReturn(new Entity([ 'name' => 'baz' ]));

        $this->validator->validate(new Entity([
            'name'        => 'baz',
            'language_id' => 'es_ES'
        ]));
    }
}
