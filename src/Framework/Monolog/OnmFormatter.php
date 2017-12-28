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

class OnmFormatter
{
    /**
     * The current request stack
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Initializes the OnmFormatter.
     *
     * @param RequestStack $requestStack The current request stack.
     * @param Loader       $loader       The core loader service.
     */
    public function __construct(RequestStack $requestStack, $loader)
    {
        $this->requestStack = $requestStack;
        $this->loader       = $loader;
        $this->instance     = $this->loader->getInstance();
    }

    /*
     * Adds extra info to the monolog processor. With this we can enrich our
     * logs.
     *
     * @param array $record The current log record.
     *
     * @return array The modified record.
     */
    public function processRecord(array $record)
    {
        $record['extra']['instance'] = 'unknown';

        if (!empty($this->instance)) {
            $record['extra']['instance'] = $this->instance->internal_name;
        }

        $request = $this->requestStack->getCurrentRequest();

        // Ensure we have a request (maybe we're in a console command)
        if (empty($request)) {
            return $record;
        }

        $record['extra']['client_ip']  = $request->getClientIp();
        $record['extra']['user-agent'] = $request->headers->get('User-Agent');
        $record['extra']['url']        = $request->getUri();

        return $record;
    }
}
