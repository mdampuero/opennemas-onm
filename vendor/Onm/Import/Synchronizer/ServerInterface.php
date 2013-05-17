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
    /**
     * Check if this server class can handle the http service
     *
     * @param $params the http server parameters
     *
     * @return true if the url matches the pattern for this server
     *
     * @throws Exception, if this server class can't handle this service url
     **/
    public function canHandle($params);
}
