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
            'name'                => 'flob',
            'id' => 1
        ]);

        $this->categoryService->expects($this->any())->method('getItemBySlug')
            ->willReturn($this->throwException(new \Exception()));

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
            'name'                => 'flob',
            'id' => 1
        ]);

        $category = new Category([
            'name'                => 'flob',
            'id' => 2
        ]);

        $this->categoryService->expects($this->any())->method('getItemBySlug')
            ->willReturn($category);


        $this->validator->validate($item);
    }
}
