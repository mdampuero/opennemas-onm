<?php

namespace Framework\Monolog;

use Symfony\Component\HttpFoundation\Request;

class OnmFormatter
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The patter to look for.
     *
     * @var array
     */
    protected $patterns;

    /**
     * The replacement string.
     *
     * @var string
     */
    protected $replacement = '<censored>';

    /**
     * Initializes the OnmFormatter.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;

        $conn = $container->get('orm.connection.instance');

        $this->patterns = [
            "@" . preg_quote($conn->user, '@') . "@",
            "@" . preg_quote($conn->password, '@') . "@"
        ];
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
        $record['extra']['message']  = $this->getMessage($record['message']);
        $record['extra']['context']  = $this->getContext($record['context']);
        $record['extra']['instance'] = $this->getInstance();
        $record['extra']['user']     = $this->getUser();
        $record['extra']['url']      = null;

        $request = $this->container->get('core.globals')->getRequest();

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

        $record['extra']['client_ip']  = $this->getClientIp($request);
        $record['extra']['user_agent'] = $request->headers->get('User-Agent');
        $record['extra']['url']        = $request->getUri();

        return $record;
    }

    /**
     * Returns the "real" client IP basing on the request.
     *
     * @param Request $request The current request.
     *
     * @return string The "real" client IP
     */
    protected function getClientIp(Request $request)
    {
        $ips = $request->getClientIps();

        if (!empty($ips)) {
            return array_pop($ips);
        }

        return null;
    }

    /*
     * Replaces the user & password in $context by generic message.
     *
     * @param array $context The context.
     *
     * @return array The censored context.
     */
    protected function getContext(array $context)
    {
        return preg_replace(
            $this->patterns,
            $this->replacement,
            json_encode($context, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Returns the instance to include in the record.
     *
     * @return string The instance to include in the record.
     */
    protected function getInstance()
    {
        if (empty($this->container->get('core.globals')->getInstance())) {
            return 'unknown';
        }

        return $this->container->get('core.globals')->getInstance()->internal_name;
    }

    /*
     * Replaces the user & password in $message by generic message.
     *
     * @param string $message The message.
     *
     * @return string The censored message.
     */
    protected function getMessage(string $message)
    {
        return preg_replace(
            $this->patterns,
            $this->replacement,
            $message
        );
    }

    /**
     * Returns the user to include in the record.
     *
     * @return string The user to include in the record.
     */
    protected function getUser()
    {
        if (empty($this->container->get('core.globals')->getUser())) {
            return 'anon.';
        }

        return $this->container->get('core.globals')->getUser()->email;
    }
}
