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

use Api\Validator\V1\CategoryValidator;
use Common\Model\Entity\Category;
use Common\Model\Entity\Content;

/**
 * Defines test cases for CategoryValidator class.
 */
class CategoryValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->categoryService = $this->getMockBuilder('CategoryService')
            ->setMethods([ 'getItemBySlug' ])
            ->getMock();

        $this->photoService = $this->getMockBuilder('PhotoService')
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->validator = new CategoryValidator($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.category':
                return $this->categoryService;

            case 'api.service.photo':
                return $this->photoService;
            default:
                return null;
        }
    }

    /**
     * Tests validate when the provided information is valid.
     */
    public function testValidateWhenValidCategory()
    {
        $item = new Category([
            'name' => 'flob',
            'id'   => 1
        ]);

        $this->categoryService->expects($this->any())->method('getItemBySlug')
            ->with('flob')
            ->willReturn($item);

        $this->addToAssertionCount(1);

        $this->validator->validate($item);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validate when the provided information is not valid.
     *
     * @expectedException \Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenNotValidCategory()
    {
        $item = new Category([
            'name' => 'flob',
            'id'   => 1
        ]);

        $this->categoryService->expects($this->any())->method('getItemBySlug')
            ->with('flob')
            ->willReturn($this->throwException(new \Exception()));

        $this->validator->validate($item);
    }

    /**
     * Tests validate when the provided logo is not valid.
     *
     * @expectedException \Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenNotValidCategoryLogo()
    {
        $item = new Category([
            'name'    => 'flob',
            'logo_id' => 123,
            'id'      => 1
        ]);

        $photo = new Content([
            'pk_content' => 123,
            'height'     => 200
        ]);

        $this->categoryService->expects($this->any())->method('getItemBySlug')
            ->with('flob')
            ->willReturn($item);

        $this->photoService->expects($this->any())->method('getItem')
            ->with(123)
            ->willReturn($photo);

        $this->validator->validate($item);
    }
}
