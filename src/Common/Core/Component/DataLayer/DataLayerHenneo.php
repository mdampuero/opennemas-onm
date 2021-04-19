<?php

namespace Common\Core\Component\DataLayer;

/**
 * Generates Data Layer code with henneo customizations.
 */
class DataLayerHenneo extends DataLayer
{
    /**
     * The array of custom variables for henneo.
     *
     * @var array
     */
    const CUSTOM_VARIABLES = [ 'extension', 'format', 'publicationDate', 'updateDate' ];

    /**
     * The array of replacements for extension value.
     *
     * @var array
     */
    const EXTENSION_REPLACEMENTS = [
        'article'    => 'articulo',
        'frontpages' => 'home',
        'category'   => 'subhome',
        'album'      => 'galeria',
        'opinion'    => 'articulo_opinion',
        'blog'       => 'blogpost',
        'poll'       => 'encuesta'
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataLayer()
    {
        $dataLayerMap = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('data_layer');

        if (empty($dataLayerMap)) {
            return null;
        }

        $variables = [];

        foreach ($dataLayerMap as $value) {
            $variables[$value['key']] = in_array($value['value'], self::CUSTOM_VARIABLES)
                ? $this->customize($value)
                : $this->container->get('core.variables.extractor')->get($value['value']);
        }

        return $variables;
    }

    /**
     * Returns the customized value for the variable.
     *
     * @param array $data The data to customize.
     *
     * @return string The customized value.
     */
    protected function customize(array $data)
    {
        $value = $this->container
            ->get('core.variables.extractor')
            ->get($data['value']);

        if ($data['value'] === 'extension') {
            if ($value === 'frontpages') {
                $category = $this->container
                    ->get('core.variables.extractor')
                    ->get('categoryId');

                if (!empty($category)) {
                    return 'subhome';
                }
            }

            return !empty(self::EXTENSION_REPLACEMENTS[$value])
                ? self::EXTENSION_REPLACEMENTS[$value]
                : $value;
        }

        if ($data['value'] === 'format') {
            return $value === 'html' ? 'web' : $value;
        }

        return !empty($value) ? date('Ymd', strtotime($value)) : '';
    }
}
