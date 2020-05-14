<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Renderer\Statistics;

use Api\Exception\GetItemException;
use Frontend\Renderer\StatisticsRenderer;

class ChartbeatRenderer extends StatisticsRenderer
{
    /**
     * {@inheritdoc}
     */
    public function __construct($container)
    {
        parent::__construct($container);

        $this->config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('chartbeat');
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        $params = array_merge(parent::getParameters($content), [
            'id'       => $this->config['id'],
            'domain'   => $this->config['domain'],
            'category' => $this->global->getSection()
        ]);

        if (!empty($content)) {
            try {
                $params['author'] = $this->global->getContainer()
                    ->get('api.service.author')
                    ->getItem($content->fk_author)->name;
            } catch (GetItemException $ie) {
                $params['author'] = $this->global->getContainer()
                    ->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('site_name');
            }
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    protected function validate()
    {
        if (!is_array($this->config)
            || !array_key_exists('id', $this->config)
            || !array_key_exists('domain', $this->config)
            || empty(trim($this->config['id'])
            || empty(trim($this->config['domain'])))
        ) {
            return false;
        }

        return true;
    }
}
