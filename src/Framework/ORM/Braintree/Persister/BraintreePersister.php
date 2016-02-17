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
     * The source name.
     *
     * @var source
     */
    protected $source;

    /**
     * Initializes the Braintree factory.
     *
     * @param Braintree_Base $factory The Braintree factory.
     * @param string         $source  The source name.
     */
    public function __construct($factory, $source)
    {
        $this->factory = $factory;
        $this->source  = $source;
    }
}
