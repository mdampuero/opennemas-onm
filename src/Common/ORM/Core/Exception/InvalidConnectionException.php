<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Exception;

class InvalidConnectionException extends \Exception
{
    /**
     * Initializes the exception with a custom message.
     *
     * @param string $class The classname of the invalid persister.
     */
    public function __construct($class)
    {
        $message = _('The data source connection for \'%s\' does not exist');

        parent::__construct(sprintf($message, $class));
    }
}
