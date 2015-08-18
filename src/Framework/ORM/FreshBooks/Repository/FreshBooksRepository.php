<?php

namespace Framework\ORM\FreshBooks\Repository;

use Framework\ORM\Repository\Repository;
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
     * Initializes the FreshBooks api.
     *
     * @param FreshBooksApi $api The FreshBooks api.
     */
    public function __construct(FreshBooksApi $api)
    {
        $this->api = $api;
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
