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

class GAnalyticsRenderer extends StatisticsRenderer
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
            ->get('google_analytics');
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        $extra['category']  = $this->global->getSection();
        $extra['extension'] = $this->global->getExtension();
        $cleanConfig        = [];

        foreach ($this->config as $account) {
            if (array_key_exists('api_key', $account) && !empty(trim($account['api_key']))) {
                $cleanConfig[] = $account;
            }
        }

        $params = [
            'params'  => $cleanConfig,
            'extra'   => $extra,
            'random'  => rand(0, 0x7fffffff),
            'date'    => date('d/m/Y'),
            'url'     => urlencode(SITE_URL),
            'newsurl' => urlencode(SITE_URL . '/newsletter/' . date("Ymd")),
            'relurl'  => urlencode('newsletter/' . date("Ymd")),
            'utma'    => '__utma%3D999.999.999.999.999.1%3B'
        ];

        if (!empty($content)) {
            $params['title'] = $content->title;
        }

        return $params;
    }
}
