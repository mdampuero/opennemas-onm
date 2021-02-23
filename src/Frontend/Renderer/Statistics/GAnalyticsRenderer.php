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

use Common\Model\Entity\Newsletter;
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
        $siteUrl  = $this->container->get('core.instance')->getBaseUrl();

        foreach ($this->config as $account) {
            if (array_key_exists('api_key', $account) && !empty(trim($account['api_key']))) {
                $accounts[] = trim($account['api_key']);
            }
        }

        $params = [
            'accounts' => $accounts,
            'content'  => $content,
            'date'     => date('d/m/Y'),
            'random'   => rand(0, 0x7fffffff),
            'url'      => urlencode($siteUrl),
            'utma'     => '__utma%3D999.999.999.999.999.1%3B'
        ];

        if (!empty($content) && $content instanceof Newsletter) {
            $relativeUrl = $this->container->get('router')->generate(
                'frontend_newsletter_show',
                [ 'id' => $content->id ]
            );

            $params['relurl'] = urlencode($relativeUrl);
        }

        return $params;
    }
}
