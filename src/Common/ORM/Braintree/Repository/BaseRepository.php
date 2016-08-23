<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Braintree\Repository;

use Common\ORM\Braintree\Data\Converter\BaseConverter;
use Common\ORM\Core\Repository;

/**
 * The BaseRepository class defines basic actions for braintree repositories.
 */
abstract class BaseRepository extends Repository
{
    /**
     * The entity converter.
     *
     * @var BaseConverter
     */
    protected $converter;

    /**
     * The Braintree factory.
     *
     * @var Braintree_Base
     */
    protected $factory;

    /**
     * The entity metadata.
     *
     * @var Metadata.
     */
    protected $metadata;

    /**
     * Initializes the Braintree factory.
     *
     * @param string         $name     The repository name.
     * @param Braintree_Base $factory  The Braintree factory.
     * @param Metadata       $metadata The entity metadata.
     */
    public function __construct($name, $factory, $metadata)
    {
        $this->converter = new BaseConverter($metadata);
        $this->factory   = $factory;
        $this->metadata  = $metadata;
        $this->name      = $name;
    }
}
