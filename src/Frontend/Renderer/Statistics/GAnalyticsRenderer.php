<?php

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
        $accounts  = [];
        $instance  = $this->container->get('core.instance');
        $siteUrl   = $instance->getBaseUrl();
        $dataLayer = '';

        foreach ($this->config as $account) {
            if (array_key_exists('api_key', $account) && !empty(trim($account['api_key']))) {
                $accounts[] = trim($account['api_key']);
            }
        }

        if (!$content instanceof Newsletter) {
            $data = $this->container->get('core.service.data_layer')->getDataLayer();
            if (!empty($data)) {
                $dataLayer = trim(json_encode(
                    array_map(function ($a) {
                        return $a === null ? '' : $a;
                    }, $data)
                ), '{}');
            }
        }

        $params = [
            'accounts'  => $accounts,
            'content'   => $content,
            'dataLayer' => $dataLayer,
            'date'      => date('d/m/Y'),
            'random'    => rand(0, 0x7fffffff),
            'url'       => urlencode($siteUrl),
            'utma'      => '__utma%3D999.999.999.999.999.1%3B'
        ];

        if (!empty($content) && $content instanceof Newsletter) {
            $relativeUrl = $this->container->get('router')->generate(
                'frontend_newsletter_show',
                [ 'id' => $content->id ]
            );
            $relativeUrl = $this->container->get('core.decorator.url')->prefixUrl($relativeUrl);

            $params['relurl'] = urlencode($relativeUrl);
        }

        return $params;
    }
}
