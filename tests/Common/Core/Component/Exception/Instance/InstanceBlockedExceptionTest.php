<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component\Exception\Instance;

use Common\Core\Component\Exception\Instance\InstanceBlockedException;

/**
 * Defines test cases for InstanceBlockedException class.
 */
class InstanceBlockedExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests constructor for InstanceBlockedException.
     */
    public function testInstanceBlockedException()
    {
        $instance = 'xyzzy';
        $e        = new InstanceBlockedException($instance);

        $this->assertRegexp('/The instance "' . $instance . '" is blocked$/', $e->getMessage());
    }
}
