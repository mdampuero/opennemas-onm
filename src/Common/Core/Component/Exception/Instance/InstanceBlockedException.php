<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Exception\Instance;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Exception thrown when the current instance is not activated.
 */
class InstanceBlockedException extends AuthenticationException
{
    /**
     * Initializes the exception.
     */
    public function __construct($instance = '')
    {
        $message =_('Instance is blocked');

        if (!empty($instance)) {
            $message = _('The instance "%s" is blocked');
        }

        parent::__construct(sprintf($message, $instance));
    }
}
