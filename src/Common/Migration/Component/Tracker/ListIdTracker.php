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
 * The ListIdTracker provides methods to track which contents are already
 * migrated when executing a migration from a data source.
 *
 * This handles the list of last migrated ids so the tracking value has no
 * special requirements.
 */
class ListIdTracker extends Tracker
{
    /**
     * The list of parsed items ids.
     *
     * @var array
     */
    protected $parsed = [];

    /**
     * {@inheritdoc}
     */
    public function add($sourceId, $targetId, $slug = null)
    {
        $this->parsed[] = $sourceId;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsed()
    {
        return $this->parsed;
    }

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
