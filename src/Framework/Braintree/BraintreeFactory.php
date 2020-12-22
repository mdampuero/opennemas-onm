<?php

namespace Framework\Braintree;

class BraintreeFactory
{
    /**
     * Constructor with Braintree configuration
     *
     * @param string $environment
     * @param string $merchantId
     * @param string $publicKey
     * @param string $privateKey
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    public function get($serviceName, array $attributes = [])
    {
        try {
            return $this->factory->get($serviceName, $attributes);
        } catch (\Exception $e) {
            $className = '\\Braintree\\' . ucfirst($serviceName);

            if (class_exists($className)) {
                return $className;
            }

            throw $e;
        }
    }
}
