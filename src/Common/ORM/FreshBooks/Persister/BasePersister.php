<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\FreshBooks\Persister;

use Common\ORM\Core\Persister;
use Freshbooks\FreshBooksApi;

/**
 * The BasePersister class defines the base persister for FreshBooks.
 */
abstract class BasePersister extends Persister
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
}
