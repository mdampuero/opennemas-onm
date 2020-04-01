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
     * Returns if comscore is correctly configured or not.
     *
     * @return boolean True if comscore is correctly configured, False otherwise.
     */
    public function validate()
    {
        $config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('comscore');

        if (!is_array($config)
            || !array_key_exists('page_id', $config)
            || empty(trim($config['page_id']))
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
    public function prepareParams()
    {
        $config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('comscore');

        return [ 'page_id' => $config['page_id']];
    }
}
