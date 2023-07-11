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

class AdobeRenderer extends StatisticsRenderer
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
            ->get('adobe_base');
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        $extractor = $this->container->get('core.variables.extractor');
        return [
            'lastAuthorId' => $extractor->get('lastAuthorId'),
            'canonical'    => $extractor->get('canonicalUrl'),
            'mediaType'    => $extractor->get('mediaType'),
            'baseFile'     => $this->config,
            'tagNames'     => $extractor->get('tagNames'),
            'tagSlugs'     => $extractor->get('tagSlugs'),
            'content'      => $content,
            'layout'       => $this->customizeExtension($extractor->get('extension')),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function validate()
    {
        $uri = $this->global->getRequest()->getUri();
        if (!empty($this->config) && preg_match('@\.amp\.html@', $uri)) {
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
