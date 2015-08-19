<?php

namespace Framework\ORM\FreshBooks\Persister;

use Framework\ORM\Persister\Persister;
use Freshbooks\FreshBooksApi;

abstract class FreshBooksPersister extends Persister
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
    protected $source = 'FreshBooks';

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
