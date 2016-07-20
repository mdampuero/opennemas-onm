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
use Common\ORM\FreshBooks\Data\Converter\BaseConverter;
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
     * The entity converter.
     *
     * @var BaseConverter
     */
    protected $converter;

    /**
     * The entity metadata.
     *
     * @var Metadata.
     */
    protected $metadata;

    /**
     * Initializes the FreshBooks api.
     *
     * @param string   $domain   The FreshBooks domain.
     * @param string   $token    The FreshBooks auth token.
     * @param Metadata $metadata The entity metadata.
     */
    public function __construct($domain, $token, $metadata)
    {
        $this->api = new FreshBooksApi($domain, $token);
        $this->converter = new BaseConverter($metadata);
        $this->metadata  = $metadata;
    }
}
