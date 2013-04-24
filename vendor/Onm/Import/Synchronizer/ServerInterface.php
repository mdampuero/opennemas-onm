<?php
/**
 * Defines the ServerInterface class
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_Import
 **/
namespace Onm\Import\Synchronizer;

/**
 * Defines the common methods that must implement the servers providers
 *
 * @package Onm_Import
 **/
interface ServerInterface
{
    public function canHandle($params);
}
