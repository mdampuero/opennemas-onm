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
     * The chartbeat configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Initializes the StatisticsRenderer.
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
            ->get('chartbeat');
    }

    /**
     * Returns if chartbeat is correctly configured or not.
     *
     * @return boolean True if chartbeat is correctly configured. False otherwise.
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
     * @param  Content The content.
     * @return array   The array of parameters for chartbeat.
     */
    public function getParameters($content)
    {
        $params = [
            'id'       => $this->config['id'],
            'domain'   => $this->config['domain'],
            'category' => $this->global->getSection()
        ];

        if (!empty($content)) {
            try {
                $params = array_merge(parent::getParameters($content), $params);
                $author = $this->global->getContainer()
                    ->get('api.service.author')
                    ->getItem($content->fk_author)->name;
            } catch (GetItemException $ie) {
                $author = $this->global->getContainer()
                    ->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('site_name');
            }
        }

        $params['author'] = $author;

        return $params;
    }
}
