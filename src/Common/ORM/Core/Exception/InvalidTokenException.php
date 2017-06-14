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
     * Initializes the InvalidTokenException.
     *
     * @param string $token  The token.
     * @param string $entity The entity name.
     */
    public function __construct($token, $entity = '')
    {
        $message = sprintf(_("The token '%s' is not valid"), $token);

        if (!empty($entity)) {
            $message = sprintf(
                _("The token '%s' is not valid for entities of type '%s'"),
                $token,
                $entity
            );
        }

        parent::__construct($message);
    }
}
