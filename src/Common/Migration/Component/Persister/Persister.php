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
 * The Persister interface defines methods to save items basing on information
 * from an external data source.
 */
interface Persister
{
    /**
     * Saves a content from an external data source.
     *
     * @param array $data The content data.
     */
    public function persist($data);
}
