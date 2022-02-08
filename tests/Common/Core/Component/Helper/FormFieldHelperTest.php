<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\FormFieldHelper;

/**
 * Defines test cases for FormFieldHelper class.
 */
class FormFieldHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->FormFieldHelper = new FormFieldHelper();
    }

    /**
     * Tests filterFields when empty name.
     */
    public function testfilterFieldsEmptyName()
    {
        $this->assertEquals([], $this->FormFieldHelper->filterFields(''));
    }

    /**
     * Tests filterFields when valid name.
     */
    public function testfilterFieldsValidName()
    {
        $this->assertEquals([
            [
            'name' => 'subscriptions',
            'title' => _('Lists'),
            'available' => [ 'subscriber' ]
            ]
        ], $this->FormFieldHelper->filterFields('subscriber'));
    }
}
