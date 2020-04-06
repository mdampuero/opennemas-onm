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
     * The piwik configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Initializes the PiwikRenderer.
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
            ->get('piwik');

        $this->piwikConfig          = $this->global->getContainer()->getParameter('opennemas.piwik');
        $this->config['server_url'] = rtrim($this->piwikConfig['url'], DS);
    }

    /**
     * Returns if piwik is correctly configured or not.
     *
     * @return boolean True if piwik is correctly configured, False otherwise.
     */
    public function validate()
    {
        if (!is_array($this->config)
            || !array_key_exists('page_id', $this->config)
            || !array_key_exists('server_url', $this->config)
            || empty(trim($this->config['page_id']))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Returns parameters needed to generate piwik code.
     *
     * @return array The array of parameters for piwik.
     */
    public function getParameters($content)
    {
        $httpsHost = preg_replace("/http:/", "https:", $this->config['server_url']);
        $newsUrl   = urlencode(SITE_URL . 'newsletter/' . date("YmdHis"));
        $ampHost   = preg_replace("/^https?:/", "", $this->config['server_url']);

        return [
            'content'   => $content,
            'config'    => $this->config,
            'httpsHost' => $httpsHost,
            'newsurl'   => $newsUrl,
            'ampHost'   => $ampHost
        ];
    }
}
