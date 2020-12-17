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
        $accounts = [];

        foreach ($this->config as $account) {
            if (array_key_exists('api_key', $account) && !empty(trim($account['api_key']))) {
                $accounts[] = trim($account['api_key']);
            }
        }

        $params = [
            'accounts' => $accounts,
            'random'   => rand(0, 0x7fffffff),
            'date'     => date('d/m/Y'),
            'url'      => urlencode(SITE_URL),
            'newsurl'  => urlencode(SITE_URL . '/newsletter/'),
            'relurl'   => urlencode('newsletter/'),
            'utma'     => '__utma%3D999.999.999.999.999.1%3B'
        ];

        if (!empty($content)) {
            $params['content'] = $content;
        }

        return $params;
    }
}
