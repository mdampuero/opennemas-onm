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
     * The google analytics configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Initializes the GAnalyticsRenderer.
     *
     * @param GlobalVariables $global The global variables.
     * @param Template        $tpl    The template.
     * @param Template        $smarty The smarty template.
     */
    public function __construct($global, $tpl, $smarty)
    {
        parent::__construct($global, $tpl, $smarty);
        $this->config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('google_analytics');
    }

    /**
     * Return the parameters needed to generate analytics.
     *
     * @return array The array of parameters for google analytics.
     */
    public function getParameters($content)
    {
        $extra['category']  = $this->global->getSection();
        $extra['extension'] = $this->global->getExtension();

        $params = [
            'params'  => $this->config,
            'extra'   => $extra,
            'random'  => rand(0, 0x7fffffff),
            'date'    => date('d/m/Y'),
            'url'     => urlencode(SITE_URL),
            'newsurl' => urlencode(SITE_URL . 'newsletter/' . date("Ymd")),
            'relurl'  => urlencode('newsletter/' . date("Ymd")),
            'utma'    => '__utma%3D999.999.999.999.999.1%3B'
        ];

        if (!empty($content)) {
            $params['title'] = $content->title;
        }

        return $params;
    }
}
