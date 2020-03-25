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
     * Get code of google analytics for amp pages
     */
    public function getAmp()
    {
        return $this->tpl->fetch('statistics/helpers/Comscore/amp.tpl', $this->prepareParams());
    }

    /**
     * Get script code for google analytics
     */
    public function getScript()
    {
        return $this->tpl->fetch('statistics/helpers/Comscore/script.tpl', $this->prepareParams());
    }

    /**
     * Return if comscore is correctly configured or not
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
     * Return needed parameters to generate comscore code
     */
    protected function prepareParams()
    {
        $config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('comscore');

        return [ 'page_id' => $config['page_id']];
    }
}
