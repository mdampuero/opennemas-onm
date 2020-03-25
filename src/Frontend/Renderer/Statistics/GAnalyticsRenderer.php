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
     * Return the code of the specified type
     */
    public function getCode($codeType, $type)
    {
        $template = 'statistics/helpers/' . $type . '/' . $codeType . '.tpl';

        try {
            return $this->tpl->fetch($template, $this->prepareParams());
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Return if google analytics is correctly configured or not
     */
    public function validate()
    {
        return true;
    }

    /**
     * Return the parameters needed to generate analytics
     */
    protected function prepareParams()
    {
        $config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('google_analytics');

        //Keep compatibility with old analytics store format
        if (is_array($config) && array_key_exists('api_key', $config)) {
            $oldConfig = $config;
            $config    = [];
            $config    = $oldConfig;
        }

        $extra['category']  = $this->global->getSection();
        $extra['extension'] = $this->global->getExtension();

        return [
            'params' => $config,
            'extra'  => $extra,
            'random'  => rand(0, 0x7fffffff),
            'date'    => date('d/m/Y'),
            'url'     => urlencode(SITE_URL),
            'newsurl' => urlencode(SITE_URL . 'newsletter/' . date("Ymd")),
            'relurl'  => urlencode('newsletter/' . date("Ymd")),
            'utma'    => '__utma%3D999.999.999.999.999.1%3B'
        ];
    }
}
