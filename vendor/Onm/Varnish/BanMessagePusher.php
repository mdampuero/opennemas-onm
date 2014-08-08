<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onm\Varnish;

/**
 * Class that allows to send Varnish ban/purge commands
 */
class BanMessagePusher
{
    /**
     * Initializes the object with the server configuration.
     *
     * @param array $serverConfiguration Varnish configuration.
     */
    public function __construct($serverConfiguration)
    {
        $this->headerName = $serverConfiguration['header_name'];

        $this->servers = $serverConfiguration['servers'];

        return $this;
    }

    /**
     * Sends a ban command.
     *
     * @param string $banRequest
     */
    public function ban($banRequest)
    {
        $response = array();
        foreach ($this->servers as $serverName => $serverConf) {
            $banHeader = $this->headerName . ': '.$banRequest;

            $return = $this->doHttpRequest(
                $serverConf['host'],
                $serverConf['port'],
                'BAN',
                $banHeader,
                ''
            );

            $response []= "BAN queued - {$serverName}({$serverConf['host']}:{$serverConf['port']}) - {$banHeader} || Return:".$return;
        }

        return $response;
    }

    /**
     * Performs a CURL.
     *
     * @param string $server  The server name.
     * @param string $port    The server port.
     * @param string $method  The method to use.
     * @param string $headers The headers to include in the request.
     * @param string $body    The body of the request.
     *
     * @return string The CURL response.
     */
    private function doHttpRequest($server, $port, $method, $headers = '', $body = '')
    {
        $curlHandler = curl_init();

        $url = "http://{$server}:{$port}/";

        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $body,
            // CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 2
        );

        if (!empty($headers)) {
            if (!is_array($headers)) {
                $headers = array($headers);
            }
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        curl_setopt_array($curlHandler, $options);

        $return = curl_exec($curlHandler);

        curl_close($curlHandler);

        return $return;
    }
}
