<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Security;

class AclTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringUtils
     */
    protected $object;

    /**
     * @covers Onm\Security\Acl::deny
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testIsBot()
    {
        \Onm\Security\Acl::deny('Testing exception');
    }
}
