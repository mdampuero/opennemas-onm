<?php

namespace Tests\Common\Core\Components\Functions;

use Common\Core\Component\Helper\PollHelper;
use Common\Model\Entity\Content;

/**
 * Defines test cases for photo helper.
 */
class PollHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
    * Configures the testing environment.
    */
    public function setUp()
    {
        $this->items = [
            [
                'pk_item' => 1,
                'votes'   => 5,
                'item'    => 'Item1'
            ],
            [
                'pk_item' => 2,
                'votes'   => 2,
                'item'    => 'Item2'
            ],
            [
                'pk_item' => 3,
                'votes'   => 8,
                'item'    => 'Item3'
            ],
        ];

        $this->item = new Content([
            'pk_content'        => 1,
            'content_type_name' => 'poll',
            'items'             => $this->items,
            'closetime'         => date('Y-m-d H:i:s', strtotime('2021-08-30'))
        ]);

        $this->helper = new PollHelper();
    }

    /**
    * test getTotalVotes
    */
    public function testGetTotalVotes()
    {
        $this->assertNull($this->helper->getTotalVotes(null));
        $this->assertEquals([ '1' => 15 ], $this->helper->getTotalVotes($this->item));
    }

    /**
    * test isClosed
    */
    public function testIsClosed()
    {
        $this->assertEquals(true, $this->helper->isClosed($this->item));

        $this->item->closetime = date('Y-m-d H:i:s', strtotime('3021-08-30'));

        $this->assertEquals(false, $this->helper->isClosed($this->item));
    }
}
