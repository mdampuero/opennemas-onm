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

class OjdRenderer extends StatisticsRenderer
{
    /**
     * The google analytics configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Initializes the GAnalyticsRenderer.
     *
     * @param GlobalVariables $global   The global variables.
     * @param Template        $backTpl  The backend template.
     * @param Template        $frontTpl The frontend template.
     */
    public function __construct($global, $backTpl, $frontTpl)
    {
        parent::__construct($global, $backTpl, $frontTpl);

        $this->config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('ojd');
    }

    /**
     * Returns if ojd is correctly configured or not.
     *
     * @return boolean True if Ojd is correctly configured. False otherwise.
     */
    public function validate()
    {
        if (!is_array($this->config)
            || !array_key_exists('page_id', $this->config)
            || empty(trim($this->config['page_id']))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Returns parameters needed to generate ojd code.
     *
     * @param  Content The content.
     * @return array The array of parameters for ojd.
     */
    public function getParameters($content)
    {
        return [ 'content' => $content, 'page_id' => $this->config['page_id'] ];
    }
}
