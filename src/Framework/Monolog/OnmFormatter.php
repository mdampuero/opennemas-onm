<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Monolog;

class OnmFormatter
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the OnmFormatter.
     *
     * @param ServiceCotnainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /*
     * Adds extra info to the monolog processor.
     *
     * @param array $record The current log record.
     *
     * @return array The modified record.
     */
    public function processRecord(array $record)
    {
        $record['extra']['instance'] = 'unknown';

        if (!empty($this->container->get('core.instance'))) {
            $record['extra']['instance'] = $this->container
                ->get('core.instance')->internal_name;
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();

        // Ensure we have a request (maybe we're in a console command)
        if (empty($request)) {
            return $record;
        }

        $record['extra']['client_ip']  = $request->getClientIp();
        $record['extra']['user_agent'] = $request->headers->get('User-Agent');
        $record['extra']['url']        = $request->getUri();

        return $record;
    }
}
