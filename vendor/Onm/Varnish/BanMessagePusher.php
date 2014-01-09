<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Varnish;

/**
* Class that allows to send Varnish ban/purge commands
*/
class BanMessagePusher
{
    public function __construct($serverConfiguration)
    {
        $this->headerName = $serverConfiguration['header_name'];

        $this->servers = $serverConfiguration['servers'];

        return $this;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function ban($banRequest)
    {
        $response = array();
        foreach ($this->servers as $serverName => $serverConf) {
            $banHeader = $this->headerName . ': '.$banRequest;
            $this->doHttpRequest(
                $serverConf['host'],
                $serverConf['port'],
                'BAN',
                $banHeader,
                ''
            );

            $response []= "BAN queued - {$serverName}({$serverConf['host']}:{$serverConf['port']}) - {$banHeader}";
        }

        return $response;
    }

    /**
     * Performs a CURL
     *
     * @return string the CURL response
     **/
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
