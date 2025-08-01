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
            ->get('marfeel_compass');
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        return [ 'id' => $this->config['id'] ];
    }

    /**
     * Checks if the renderer configuration is valid.
     *
     * @return boolean True if the configuration is valid. False otherwise.
     */
    protected function validate()
    {
        if (!is_array($this->config)
            || !array_key_exists('id', $this->config)
            || empty(trim($this->config['id']))
        ) {
            return false;
        }

        return true;
    }
}
