<?php
/**
 * Implements the ServerFactory class
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
 * Class for initialize a Server class handler
 *
 * @package Onm_Import
 **/
class ServerFactory
{
    /**
     * Returns an instance of the server where to sync from
     *
     * @param string $filePath the file path to initialize the element
     * @param array $serverParams the server params
     *
     * @return ServerInterface
     **/
    public static function get($serverParams)
    {
        $baseServersClassPath = __DIR__.'/Servers';
        $availableServers = glob($baseServersClassPath.'/*.php');

        $server = null;
        foreach ($availableServers as $value) {
            $serverName = basename($value, '.php');
            $serverClass = __NAMESPACE__."\Servers\\".$serverName;
            try {
                $server = new $serverClass($serverParams);
                break;
            } catch (\Exception $e) {
                continue;
            }
        }

        return $server;
    }
}
