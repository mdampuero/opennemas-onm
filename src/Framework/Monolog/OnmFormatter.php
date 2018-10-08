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
        $record['extra']['instance'] = $this->getInstance();
        $record['extra']['user']     = $this->getUser();
        $record['extra']['url']      = null;

        $request = $this->container->get('request_stack')->getCurrentRequest();

        // Check if request (maybe it is a console command)
        if (empty($request)) {
            $record['extra']['client_ip']  = php_sapi_name();
            $record['extra']['user_agent'] = php_sapi_name();

            if (array_key_exists('argv', $_SERVER)
                && is_array($_SERVER['argv'])
            ) {
                $record['extra']['url'] = implode(' ', $_SERVER['argv']);
            }

            return $record;
        }

        $record['extra']['client_ip']  = $request->getClientIp();
        $record['extra']['user_agent'] = $request->headers->get('User-Agent');
        $record['extra']['url']        = $request->getUri();

        return $record;
    }

    /**
     * Returns the instance to include in the record.
     *
     * @return string The instance to include in the record.
     */
    protected function getInstance()
    {
        if (!empty($this->container->get('core.instance'))) {
            return $this->container->get('core.instance')->internal_name;
        }

        return 'unknown';
    }

    /**
     * Returns the user to include in the record.
     *
     * @return string The user to include in the record.
     */
    protected function getUser()
    {
        $ts = $this->container->get('security.token_storage');

        if (empty($ts->getToken())
            || empty($ts->getToken()->getUser())
            || empty($ts->getToken()->getUser() !== 'anon.')
        ) {
            return 'anon.';
        }

        return $ts->getToken()->getUser()->email;
    }
}
