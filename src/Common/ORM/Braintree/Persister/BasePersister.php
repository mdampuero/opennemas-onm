<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Braintree\Persister;

use Common\ORM\Braintree\Data\Converter\BaseConverter;
use Common\ORM\Core\Persister;

/**
 * The BasePersister class defines basic actions for braintree persisters.
 */
abstract class BasePersister extends Persister
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
     * @param Braintree_Base $factory  The Braintree factory.
     * @param Metadata       $metadata The entity metadata.
     */
    public function __construct($factory, $metadata)
    {
        $this->converter = new BaseConverter($metadata);
        $this->factory   = $factory;
        $this->metadata  = $metadata;
    }
}
