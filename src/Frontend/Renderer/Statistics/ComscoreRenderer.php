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

class ComscoreRenderer extends StatisticsRenderer
{
    /**
     * The comscore configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Initializes the ComscoreRenderer.
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
            ->get('comscore');
    }

    /**
     * Returns if comscore is correctly configured or not.
     *
     * @return boolean True if comscore is correctly configured, False otherwise.
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
     * Returns needed parameters to generate comscore code.
     *
     * @return array The array of parameters for comscore.
     */
    public function getParameters()
    {
        return [ 'page_id' => $this->config['page_id'] ];
    }
}
