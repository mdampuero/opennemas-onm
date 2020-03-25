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
     * Return the code of the specified type
     */
    public function getCode($codeType, $type)
    {
        $template = 'statistics/helpers/' . $type . '/' . $codeType . '.tpl';

        try {
            return $this->tpl->fetch($template, $this->prepareParams());
        } catch (\Exception $e) {
            return '';
        }
    }
    /**
     * Return if ojd is correctly configured or not
     */
    public function validate()
    {
        $config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('ojd');

        if (!is_array($config)
            || !array_key_exists('page_id', $config)
            || empty(trim($config['page_id']))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Return parameters needed to generate ojd code
     */
    protected function prepareParams()
    {
        $config = $config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('ojd');

        return ['page_id' => $config['page_id']];
    }
}
