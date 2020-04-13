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

use Frontend\Renderer\StatisticsRenderer;

class ComscoreRenderer extends StatisticsRenderer
{
    /**
     * {@inheritdoc}
     */
    public function __construct($global, $backend, $frontend)
    {
        parent::__construct($global, $backend, $frontend);

        $this->config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('comscore');
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        return [ 'content' => $content, 'page_id' => $this->config['page_id'] ];
    }

    /**
     * {@inheritdoc}
     */
    protected function validate()
    {
        if (!is_array($this->config)
            || !array_key_exists('page_id', $this->config)
            || empty(trim($this->config['page_id']))
        ) {
            return false;
        }

        return true;
    }
}
