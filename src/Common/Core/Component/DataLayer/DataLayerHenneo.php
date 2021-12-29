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
    public function customizeExtension(string $extension)
    {
        $contentTypes = [
            'album',
            'blog',
            'event',
            'letter',
            'opinion',
            'poll',
            'video',
        ];

        $replacements = [
            'article'    => 'articulo',
            'frontpages' => 'home',
            'category'   => 'subhome',
            'album'      => 'galeria',
            'opinion'    => 'articulo_opinion',
            'blog'       => 'blogpost',
            'poll'       => 'encuesta'
        ];

        if (in_array($extension, $contentTypes)) {
            if (empty($this->variablesExtractor->get('contentId'))) {
                return 'subhome';
            }
        }

        if ($extension === 'frontpages') {
            $category = $this->variablesExtractor->get('categoryId');

            if (!empty($category)) {
                return 'subhome';
            }
        }

        return !empty($replacements[$extension]) ? $replacements[$extension] : $extension;
    }

    /**
     * Get the content and check if it's under subscription.
     *
     * @param Boolean True if the content is restricted, false otherwise.
     *
     * @return string The customized string for restricted content.
     */
    public function customizeIsRestricted(?bool $isRestricted)
    {
        return $isRestricted ? 'registro' : 'abierto';
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
        $date = new \DateTime($date);

        return !empty($date) ? $date->format('Ymd') : null;
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
