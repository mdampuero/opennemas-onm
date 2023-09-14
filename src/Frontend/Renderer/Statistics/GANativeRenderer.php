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
            'content'       => $content,
            'ga4Id'         => $this->ga4Id,
            'tagNames'      => $this->variablesExtractor->get('tagNames'),
            'layout'        => $this->customizeExtension($this->variablesExtractor->get('extension')),
            'lastAuthorId'  => $this->variablesExtractor->get('lastAuthorId'),
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
}
