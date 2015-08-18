<?php

namespace Framework\ORM\Braintree\Persister;

use Framework\ORM\Entity\Entity;
use Framework\ORM\Persister\Persister;

abstract class BraintreePersister extends Persister
{
    /**
     * The Braintree factory.
     *
     * @var Braintree_Base
     */
    protected $factory;

    /**
     * Initializes the Braintree factory.
     *
     * @param Braintree_Base $factory The Braintree factory.
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    /**
     * Returns the Braintree factory object.
     *
     * @return Braintreefactory The Braintree factory object.
     */
    public function getfactory()
    {
        return $this->factory;
    }
}
