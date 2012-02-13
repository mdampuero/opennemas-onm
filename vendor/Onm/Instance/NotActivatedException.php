<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Instance;
/**
 * Extends exception for handling not activated instances.
 *
 * @package    Onm
 * @subpackage Instance
 * @author     me
 **/
class NotActivatedException extends \Exception
{

    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }

}