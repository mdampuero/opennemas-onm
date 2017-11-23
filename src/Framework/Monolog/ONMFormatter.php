<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Monolog;

use Symfony\Component\HttpFoundation\RequestStack;

class ONMFormatter
{

    private $requestStack;

    public function __construct(RequestStack $requestStack, $loader)
    {
        $this->requestStack = $requestStack;
        $this->loader       = $loader;
    }

    /*
     *     Adds extra info to the monolog processor. With this we can enrich our
     * logs
     *
     * @param array $record the current log record
     *
     * @return array the modified record
     */
    public function processRecord(array $record)
    {
        if (!empty($this->instance)) {
            $record['extra']['instance'] = $this->instance;
        } elseif (!empty($this->loader->getInstance())) {
            $this->instance              = $this->loader->getInstance()->internal_name;
            $record['extra']['instance'] = $this->instance;
        } else {
            $record['extra']['instance'] = 'unknown';
        }

        // Ensure we have a request (maybe we're in a console command)
        if (! $request = $this->requestStack->getCurrentRequest()) {
            return $record;
        }

        $record['extra']['client_ip']  = $request->getClientIp();
        $record['extra']['user-agent'] = $request->headers->get('User-Agent');
        $record['extra']['url']        = $request->getUri();

        return $record;
    }
}
