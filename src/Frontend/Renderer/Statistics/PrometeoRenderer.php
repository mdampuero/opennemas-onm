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
        $extractor = $this->container->get('core.variables.extractor');

        // If media type is not a photo, get frontpage image
        $type = $extractor->get('mediaType') === 'photo' ? 'inner' : 'frontpage';

        return [
            'content'    => $content,
            'accessType' => $dataLayer->customizeIsRestricted($extractor->get('isRestricted')),
            'id'         => $this->config['id'],
            'section'    => $extractor->get('categoryName'),
            'type'       => $dataLayer->customizeExtension($extractor->get('extension')),
            'seoTags'    => $extractor->get('tagSlugs'),
            'imagePath'  => $this->container->get('core.helper.photo')->getPhotoPath(
                $this->container->get('core.helper.featured_media')->getFeaturedMedia($content, $type),
                null,
                [],
                true
            )
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
