<?php

namespace Backend\Tests\Annotation;

use Backend\Annotation\CheckModuleAccess;

class CheckModuleAccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Backend\Annotation\CheckModuleAccess::getModule
     */
    public function testAnnotation()
    {
        $module     = uniqid();
        $annotation = new CheckModuleAccess([ 'module' => $module ]);

        $this->assertEquals($module, $annotation->getModule());
    }
}
