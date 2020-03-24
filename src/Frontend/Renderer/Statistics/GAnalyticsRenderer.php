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
     * Get code of google analytics for amp pages
     */
    public function getAmp()
    {
        return $this->tpl->fetch('statistics/helpers/GAnalytics/amp.tpl', [
            'params' => $this->prepareParams()[0]
        ]);
    }

    /**
     * Get script code for google analytics
     */
    public function getScript()
    {
        $parameters = $this->prepareParams();
        $params     = $parameters[0];
        $extra      = $parameters[1];

        return $this->tpl->fetch('statistics/helpers/GAnalytics/script.tpl', [
            'params' => $params,
            'extra'  => $extra
        ]);
    }

    /**
     * Get image code for google analytics
     */
    public function getImage()
    {
        return $this->tpl->fetch('statistics/helpers/GAnalytics/image.tpl', [
            'random'  => rand(0, 0x7fffffff),
            'date'    => date('d/m/Y'),
            'url'     => urlencode(SITE_URL),
            'newsurl' => urlencode(SITE_URL . 'newsletter/' . date("Ymd")),
            'relurl'  => urlencode('newsletter/' . date("Ymd")),
            'params'  => $this->prepareParams()[0],
            'utma'    => '__utma%3D999.999.999.999.999.1%3B'
        ]);
    }

    /**
     * Return the parameters needed to generate analytics
     */
    protected function prepareParams()
    {
        $config = $this->em->getDataSet('Settings', 'instance')
            ->get('google_analytics');

        //Keep compatibility with old analytics store format
        if (is_array($config) && array_key_exists('api_key', $config)) {
            $oldConfig = $config;
            $config    = [];
            $config    = $oldConfig;
        }

        $extra['category']  = $this->global->getSection();
        $extra['extension'] = $this->global->getExtension();

        return [ $config, $extra ];
    }

    /**
     * Return if google analytics is correctly configured or not
     */
    public function validate()
    {
        return true;
    }
}
