<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Tracker;

/**
 * The ImportTracker provides methods to translate contents when importing
 * contents from a news agency.
 */
class ImportTracker extends Tracker
{
    /**
     * {@inheritdoc}
     */
    public function load($type = null)
    {
        $values = $this->conn
            ->fetchAll('SELECT pk_content, urn_source, slug FROM contents');

        foreach ($values as $value) {
            $this->add(
                $value['urn_source'],
                $value['pk_content'],
                null,
                $value['slug']
            );
        }
    }
}
