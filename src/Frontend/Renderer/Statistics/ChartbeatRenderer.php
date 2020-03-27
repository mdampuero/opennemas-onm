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
     * Return if chartbeat is correctly configured or not
     */
    public function validate()
    {
        $config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('chartbeat');

        if (!is_array($config)
            || !array_key_exists('id', $config)
            || !array_key_exists('domain', $config)
            || empty(trim($config['id'])
            || empty(trim($config['domain'])))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Return parameters needed to generate chartbeat code
     */
    public function prepareParams()
    {
        $container = $this->global->getContainer();
        $config    = $container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('chartbeat');

        $smarty  = $container->get('core.template.frontend');
        $content = $smarty->getValue('content');

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
            'id'       => $config['id'],
            'domain'   => $config['domain'],
            'category' => $this->global->getSection(),
            'author'   => $author
        ];
    }
}
