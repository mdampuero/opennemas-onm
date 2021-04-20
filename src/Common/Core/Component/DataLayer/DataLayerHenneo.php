<?php

namespace Common\Core\Component\DataLayer;

/**
 * Generates Data Layer code with henneo customizations.
 */
class DataLayerHenneo extends DataLayer
{
    /**
     * {@inheritdoc}
     */
    public function getDataLayer()
    {
        $variables = parent::getDataLayer();

        for ($i = 0; $i < count($this->dataLayerMap); $i++) {
            $method = sprintf('customize%s', ucfirst($this->dataLayerMap[$i]['value']));

            if (method_exists($this, $method)) {
                $variables[$this->dataLayerMap[$i]['key']] =
                    $this->{$method}($variables[$this->dataLayerMap[$i]['key']]);
            }
        }

        return $variables;
    }

    /**
     * Returns the customization for the extension.
     *
     * @param string $extension The extension to customize.
     *
     * @return string The customized extension.
     */
    protected function customizeExtension(string $extension)
    {
        $replacements = [
            'article'    => 'articulo',
            'frontpages' => 'home',
            'category'   => 'subhome',
            'album'      => 'galeria',
            'opinion'    => 'articulo_opinion',
            'blog'       => 'blogpost',
            'poll'       => 'encuesta'
        ];

        if ($extension === 'frontpages') {
            $category = $this->variablesExtractor->get('categoryId');

            if (!empty($category)) {
                return 'subhome';
            }
        }

        return $replacements[$extension];
    }

    /**
     * Returns the customization for the publicationDate.
     *
     * @param null|string $date The date to customize.
     *
     * @return null|string The customized date.
     */
    protected function customizePublicationDate(?string $date)
    {
        return !empty($date) ? date('Ymd', strtotime($date)) : null;
    }

    /**
     * Returns the customization for the updateDate.
     *
     * @param null|string $date The date to customize.
     *
     * @return null|string The customized date.
     */
    protected function customizeUpdateDate(?string $date)
    {
        return $this->customizePublicationDate($date);
    }

    /**
     * Returns the customization for the format.
     *
     * @param string $format The format to customize.
     *
     * @return string The customized format.
     */
    protected function customizeFormat(string $format)
    {
        return $format === 'html' ? 'web' : $format;
    }
}
