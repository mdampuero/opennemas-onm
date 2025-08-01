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

use Common\Core\Component\Exception\Instance\InstanceNotActivatedException;

/**
 * Defines test cases for InstanceNotActivatedException class.
 */
class InstanceNotActivatedExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests constructor for InstanceNotActivatedException.
     */
    public function testInstanceNotActivatedException()
    {
        $instance = 'xyzzy';
        $e        = new InstanceNotActivatedException($instance);

        $this->assertRegexp(
            '/The instance "' . $instance . '" is not activated$/',
            $e->getMessage()
        );
    }
}
