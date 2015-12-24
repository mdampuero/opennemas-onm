<?php

namespace Framework\Tests\ORM\Validator;

use Framework\ORM\Exception\InvalidSchemaException;
use Framework\ORM\Core\Entity;
use Framework\ORM\Validator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity    = new Entity([ 'foo' => 'bar', 'baz' => 'qux' ]);
        $this->validator = new Validator();
    }

    public function testValidateRequired()
    {
        $this->validator->validate($this->entity);
    }
}
