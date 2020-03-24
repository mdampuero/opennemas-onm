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

class PiwikRenderer extends StatisticsRenderer
{
    /**
     * Get code of google analytics for amp pages
     */
    public function getAmp()
    {
        return $this->tpl->fetch('statistics/helpers/Piwik/amp.tpl', $this->prepareParams());
    }

    /**
     * Get script code for google analytics
     */
    public function getScript()
    {
        return $this->tpl->fetch('statistics/helpers/Piwik/script.tpl', $this->prepareParams());
    }

    /**
     * Get image code for google analytics
     */
    public function getImage()
    {
        return $this->tpl->fetch('statistics/helpers/Piwik/image.tpl', $this->prepareParams());
    }

    /**
     * Return if piwik is correctly configured or not
     */
    public function validate()
    {
        $config      = $this->em->getDataSet('Settings', 'instance')->get('piwik');
        $piwikConfig = getService('service_container')->getParameter('opennemas.piwik');

        $config['server_url'] = rtrim($piwikConfig['url'], DS) . DS;

        if (!is_array($config)
        || !array_key_exists('page_id', $config)
        || !array_key_exists('server_url', $config)
        || empty(trim($config['page_id']))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Return parameters needed to generate piwik code
     */
    protected function prepareParams()
    {
        $config      = $this->em->getDataSet('Settings', 'instance')->get('piwik');
        $piwikConfig = getService('service_container')->getParameter('opennemas.piwik');

        $config['server_url'] = rtrim($piwikConfig['url'], DS) . DS;
        $httpsHost            = preg_replace("/http:/", "https:", $config['server_url']);
        $newsUrl              = urlencode(SITE_URL . 'newsletter/' . date("YmdHis"));
        $ampHost              = preg_replace("/^https?:/", "", $config['server_url']);

        return [
            'config'   => $config,
            'httpsHost' => $httpsHost,
            'newsurl'  => $newsUrl,
            'ampHost'  => $ampHost
        ];
    }
}
