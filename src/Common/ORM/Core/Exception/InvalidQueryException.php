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

class InvalidQueryException extends \Exception
{
    /**
     * Initializes the InvalidQueryException.
     */
    public function __construct($token)
    {
        $message = sprintf(_("You have a syntax error near '%s'"), $token);

        parent::__construct($message);
    }
}
