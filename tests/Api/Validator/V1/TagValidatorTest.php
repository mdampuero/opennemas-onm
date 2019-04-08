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

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getAvailableLocales' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->locale->expects($this->any())->method('getAvailableLocales')
            ->with('frontend')->willReturn([
                'es_ES' => 'Spanish',
                'gl_ES' => 'Galician'
            ]);

        $this->validator = new TagValidator($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.tag':
                return $this->tagService;

            case 'core.locale':
                return $this->locale;

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
        $item = new Entity([ 'name' => 'flob', 'locale' => 'es_ES' ]);
        $item->refresh();

        $this->coreValidator->expects($this->any())->method('validate')
            ->willReturn([]);

        $this->addToAssertionCount(1);

        $this->validator->validate($item);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validate when the provided information is valid.
     */
    public function testValidateWhenValidNewTag()
    {
        $this->coreValidator->expects($this->any())->method('validate')
            ->willReturn([]);

        $this->tagService->expects($this->at(0))->method('getItemBy')
            ->with('name = "flob" and locale is null')
            ->will($this->throwException(new \Exception()));

        $this->tagService->expects($this->at(1))->method('getItemBy')
            ->with('name = "plugh" and locale = "es_ES"')
            ->willReturn(new Entity([ 'name' => 'Plugh' ]));

        $this->validator->validate(new Entity([
            'name' => 'flob',
        ]));

        $this->validator->validate(new Entity([
            'name'   => 'plugh',
            'locale' => 'es_ES'
        ]));
    }

    /**
     * Tests validate when the provided locale is not valid.
     *
     * @expectedException \Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenLocaleInvalid()
    {
        $this->coreValidator->expects($this->once())->method('validate')
            ->willReturn([]);

        $this->validator->validate(new Entity([
            'name'   => 'baz',
            'locale' => 'en_US'
        ]));
    }

    /**
     * Tests validate when the provided information is not valid.
     *
     * @expectedException \Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenNameInvalid()
    {
        $item = new Entity([ 'name' => 'flob', 'locale' => 'es_ES' ]);
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
            ->with('name = "baz" and locale = "es_ES"')
            ->willReturn(new Entity([ 'name' => 'baz' ]));

        $this->validator->validate(new Entity([
            'name'   => 'baz',
            'locale' => 'es_ES'
        ]));
    }
}
