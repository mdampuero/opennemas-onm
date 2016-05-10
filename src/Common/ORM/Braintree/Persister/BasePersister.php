<?php

namespace Common\ORM\Braintree\Persister;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Persister;

abstract class BasePersister extends Persister
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
     * @param string         $source  The source name.
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
    }
}
