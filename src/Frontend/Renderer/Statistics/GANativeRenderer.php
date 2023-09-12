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

class GANativeRenderer extends StatisticsRenderer
{
    /**
     * {@inheritdoc}
     */
    public function __construct($container)
    {
        parent::__construct($container);

        $this->ga4Id = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('ga4_native_id');

        $this->ga4config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('ga4_native_config');

        $this->variablesExtractor = $this->container->get('core.variables.extractor');
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        return [
            'configfile'    => $this->ga4config,
            'ga4Id'         => $this->ga4Id,
            'tagNames'      => $this->variablesExtractor->get('tagNames'),
            'layout'        => $this->customizeExtension($this->variablesExtractor->get('extension')),
            'category'      => $this->variablesExtractor->get('categoryName'),
            'publisherId'   => $this->variablesExtractor->get('publisherId'),
            'publisherName' => $this->variablesExtractor->get('publisherName'),
            'pubDate'       => $this->variablesExtractor->get('publicationDate'),
            'updated'       => $this->variablesExtractor->get('updateDate'),
            'mainDomain'    => $this->variablesExtractor->get('mainDomain'),
            'lastAuthorId'  => $this->variablesExtractor->get('lastAuthorId'),
            'contentId'     => $this->variablesExtractor->get('contentId'),
            'mediaType'     => $this->variablesExtractor->get('mediaType'),
            'tagSlugs'      => $this->variablesExtractor->get('tagSlugs'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function validate()
    {
        $uri = $this->global->getRequest()->getUri();
        if (!empty($this->ga4Id) && !empty($this->ga4config) && preg_match('@\.amp\.html@', $uri)) {
            return true;
        }

        return false;
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
}
