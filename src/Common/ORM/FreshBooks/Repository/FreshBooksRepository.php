<?php

namespace Common\ORM\FreshBooks\Repository;

use Common\ORM\Core\Repository;
use Freshbooks\FreshBooksApi;

abstract class FreshBooksRepository extends Repository
{
    /**
     * The FreshBooks api.
     *
     * @var FreshBooksApi
     */
    protected $api;

    /**
     * The source name.
     *
     * @var string
     */
    protected $source;

    /**
     * Initializes the FreshBooks api.
     *
     * @param FreshBooksApi $api    The FreshBooks api.
     * @param string        $source The source name.
     */
    public function __construct(FreshBooksApi $api, $source)
    {
        $this->api    = $api;
        $this->source = $source;
    }

    /**
     * Returns the FreshBooks API object.
     *
     * @return FreshBooksApi The FreshBooks API object.
     */
    public function getApi()
    {
        return $this->api;
    }
}
