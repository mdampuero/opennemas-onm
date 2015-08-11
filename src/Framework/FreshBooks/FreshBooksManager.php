<?php

namespace Framework\FreshBooks;

use Freshbooks\FreshBooksApi;
use Framework\FreshBooks\Exception\InvalidRepositoryException;

class FreshBooksManager
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
     * @param string $domain The FreshBooks domain.
     * @param string $token  The FreshBooks auth token.
     */
    public function __construct($domain, $token)
    {
        $this->api = new FreshBooksApi($domain, $token);
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

    /**
     * Returns a new repository by name.
     *
     * @param string $name The repository name.
     *
     * @return Repository The repository.
     */
    public function getRepository($name)
    {
        $repository = __NAMESPACE__ . '\\Repository\\'
            . ucfirst($name) . 'Repository';

        if (class_exists($repository)) {
            return new $repository($this->api);
        } else {
            throw new InvalidRepositoryException();
        }
    }
}
