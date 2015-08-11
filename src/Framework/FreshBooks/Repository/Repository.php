<?php

namespace Framework\FreshBooks\Repository;

class Repository
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
    public function __construct($api)
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
