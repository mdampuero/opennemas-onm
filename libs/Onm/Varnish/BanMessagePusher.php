<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Varnish;

use GuzzleHttp\Client;
use Symfony\Bridge\Monolog\Logger;

class BanMessagePusher
{
    /**
     * The HTTP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * The varnish configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The logger service.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Initializes the object with the server configuration.
     *
     * @param Logger $logger The application log.
     * @param array  $config The varnish configuration.
     */
    public function __construct(Logger $logger, array $config)
    {
        $this->client = new Client();
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Sends a ban command.
     *
     * @param string $banRequest
     */
    public function ban($request)
    {
        foreach ($this->config['servers'] as $name => $config) {
            $url = sprintf('http://%s:%s', $config['host'], $config['port']);

            $response = $this->client->request('BAN', $url, [
                'headers' => [ $this->config['header_name'] => $request ],
                'timeout' => 2
            ]);

            $this->logger->info(json_encode([
                'server'   => $name,
                'url'      => $config['host'] . ':' . $config['port'],
                'request'  => $request,
                'status'   => $response->getStatusCode(),
                'response' => $this->parseResponse($response->getReasonPhrase())
            ]));
        }
    }

    /**
     * Parses the varnish response to show specific messages.
     *
     * @param string The response to parse.
     *
     * @return string The parsed response.
     */
    protected function parseResponse(string $response) : string
    {
        if (!preg_match('@(Ban not allowed|Ban added|No Ban specified)@i', $response)) {
            return 'Not valid response from varnish.';
        }

        return $response;
    }
}
