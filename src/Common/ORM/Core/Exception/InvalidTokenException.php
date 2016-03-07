<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Exception;

class InvalidTokenException extends \Exception
{
    /**
     * Creates a new exception from criteria, source and error message.
     *
     * @param string $token  The token.
     * @param string $entity The entity name.
     */
    public function __construct($token , $entity)
    {
        $message = _("The token '%s' is not valid for entities of type '%s'");

        parent::__construct(sprintf($message, $token, $entity));
    }
}
