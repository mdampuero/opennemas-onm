<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\FreshBooks\Repository;

use Common\ORM\Core\Repository;
use Freshbooks\FreshBooksApi;

/**
 * The BaseRepository class defines the base repository for FreshBooks.
 */
abstract class BaseRepository extends Repository
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
