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

use Api\Exception\GetItemException;
use Frontend\Renderer\StatisticsRenderer;

class ChartbeatRenderer extends StatisticsRenderer
{
    /**
     * The chartbeat configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Initializes the StatisticsRenderer.
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
            ->get('chartbeat');
    }

    /**
     * Returns if chartbeat is correctly configured or not.
     *
     * @return boolean True if chartbeat is correctly configured, False otherwise.
     */
    public function validate()
    {
        if (!is_array($this->config)
            || !array_key_exists('id', $this->config)
            || !array_key_exists('domain', $this->config)
            || empty(trim($this->config['id'])
            || empty(trim($this->config['domain'])))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Returns parameters needed to generate chartbeat code.
     *
     * @return array The array of parameters for chartbeat.
     */
    public function prepareParams()
    {
        $container = $this->global->getContainer();

        $content = $this->smarty->getValue('content');

        if (!empty($content)) {
            try {
                $author = $container->get('api.service.author')
                    ->getItem($content->fk_author)->name;
            } catch (GetItemException $ie) {
                $author = $container->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('site_name');
            }
        }

        return [
            'id'       => $this->config['id'],
            'domain'   => $this->config['domain'],
            'category' => $this->global->getSection(),
            'author'   => $author
        ];
    }
}
