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

        $this->variablesExtractor = $this->container->get('core.variables.extractor');
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        return [
            'lastAuthorId' => $this->variablesExtractor->get('lastAuthorId'),
            'canonical'    => $this->variablesExtractor->get('canonicalUrl'),
            'mediaType'    => $this->variablesExtractor->get('mediaType'),
            'baseFile'     => $this->config,
            'tagNames'     => $this->variablesExtractor->get('tagNames'),
            'tagSlugs'     => $this->variablesExtractor->get('tagSlugs'),
            'content'      => $content,
            'layout'       => $this->customizeExtension($this->variablesExtractor->get('extension')),
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
}
