<?php
/**
 * Defines the Onm\Instance\NotFoundException clas
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm
 * @subpackage Instance
 */
namespace Onm\Instance;

/**
 * Extends exception for handling not found instances.
 *
 * @package    Onm
 * @subpackage Instance
 **/
class NotFoundException extends \Exception
{
    /**
     * Initializes the exception
     *
     * @param string $message the message that raises the exception
     * @param int    $code    the code that identifies this exception
     **/
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
