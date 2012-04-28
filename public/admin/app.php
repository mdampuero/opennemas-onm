<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

require '../../app/autoload.php';
require '../bootstrap.php';

// Dispatch the response
$dispatcher = new \Onm\Framework\Dispatcher\Dispatcher($matcher, $request);
$dispatcher->dispatch();