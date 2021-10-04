<?php

namespace Frontend\Renderer\Statistics;

use Frontend\Renderer\StatisticsRenderer;

class PrometeoRenderer extends StatisticsRenderer
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
            ->get('prometeo');
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        $dataLayer = $this->container->get('core.service.data_layer');
        $extension = $this->container->get('core.variables.extractor')->get('extension');
        $seoTags   = $this->container->get('core.variables.extractor')->get('tagSlugs');

        return [
            'content' => $content,
            'id'      => $this->config['id'],
            'type'    => $dataLayer->customizeExtension($extension),
            'seoTags' => $seoTags
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function validate()
    {
        $modules = $this->container->get('core.instance')->activated_modules;

        if (!in_array('es.openhost.module.dataLayerHenneo', $modules)
            || !is_array($this->config)
            || !array_key_exists('id', $this->config)
            || empty(trim($this->config['id']))
        ) {
            return false;
        }

        return true;
    }
}
