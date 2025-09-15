<?php

namespace Frontend\Renderer\Statistics;

use Frontend\Renderer\StatisticsRenderer;

class MCompassRenderer extends StatisticsRenderer
{
    /**
     * {@inheritdoc}
     */
    public function __construct($container)
    {
        parent::__construct($container);

        $this->config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get(['marfeel_compass', 'cookies']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        return [
            'id'      => $this->config['marfeel_compass']['id'],
            'cookies' => $this->config['cookies']
        ];
    }

    /**
     * Checks if the renderer configuration is valid.
     *
     * @return boolean True if the configuration is valid. False otherwise.
     */
    protected function validate()
    {
        if (!is_array($this->config)
            || !array_key_exists('marfeel_compass', $this->config)
            || empty(trim($this->config['marfeel_compass']['id']))
        ) {
            return false;
        }

        return true;
    }
}
