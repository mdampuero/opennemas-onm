<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Persister;

/**
 * The Persister class provides methods to save Entities basing on information from
 * an external data source.
 */
abstract class Persister
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Initializes the Persister.
     *
     * @param EntityManager $em The entity manager.
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Saves a content from an external data source.
     *
     * @param array $data The content data.
     */
    abstract public function persist($data);
}
