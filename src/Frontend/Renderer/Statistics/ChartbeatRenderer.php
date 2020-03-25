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

class ChartbeatRenderer extends StatisticsRenderer
{
    /**
     * Get code of google analytics for amp pages
     */
    public function getAmp()
    {
        return $this->tpl->fetch('statistics/helpers/Chartbeat/amp.tpl', $this->prepareParams());
    }

    /**
     * Get script code for google analytics
     */
    public function getScript()
    {
        return $this->tpl->fetch('statistics/helpers/Chartbeat/script.tpl', $this->prepareParams());
    }

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
    protected function prepareParams()
    {
        $container = $this->global->getContainer();
        $config    = $container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('chartbeat');

        $content = $this->tpl->getValue('content');

        if (!empty($content)) {
            $author = $container->get('api.service.author')
                ->getItem($content->fk_author);
        }

        if (empty($author)) {
            $author = $container->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('site_name');
        }

        return [
            'id'       => $config['id'],
            'domain'   => $config['domain'],
            'category' => $this->global->getSection(),
            'author'   => $author
        ];
    }
}
