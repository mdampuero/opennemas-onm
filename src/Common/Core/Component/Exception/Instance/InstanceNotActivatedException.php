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

/**
 * Exception thrown when the current instance is not activated.
 */
class InstanceNotActivatedException extends \Exception
{
    /**
     * Initializes the exception.
     */
    public function __construct($instance = '')
    {
        $message =_('Instance not activated');

        if (!empty($instance)) {
            $message = _('The instance "%s" is not activated');
        }

        parent::__construct(sprintf($message, $instance));
    }
}
