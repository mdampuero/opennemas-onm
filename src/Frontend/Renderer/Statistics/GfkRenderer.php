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

class GfkRenderer extends StatisticsRenderer
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
            ->get('gfk');
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        return [
            'content'   => $content,
            'category'  => $this->global->getSection(),
            'mediaId'   => $this->config['media_id'],
            'regionId'  => !empty($this->config['region_id']) ? $this->config['region_id'] : 'es',
            'contentId' => !empty($this->config['content_id']) ? $this->config['content_id'] : 'default'
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function validate()
    {
        if (!is_array($this->config)
            || !array_key_exists('media_id', $this->config)
            || empty(trim($this->config['media_id']))
        ) {
            return false;
        }

        return true;
    }
}
