<?php

namespace Framework\ORM\Braintree\Repository;

use Framework\ORM\Core\Entity;
use Framework\ORM\Repository\Repository;

abstract class BraintreeRepository extends Repository
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
