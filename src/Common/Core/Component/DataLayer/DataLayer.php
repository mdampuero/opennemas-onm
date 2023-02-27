<?php

namespace Common\Core\Component\DataLayer;

/**
 * Generates Data Layer code
 * See more: https://developers.google.com/tag-manager/devguide#datalayer
 */
class DataLayer
{
    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The datalayer map.
     *
     * @var array
     */
    protected $dataLayerMap = [];

    /**
     * The variables extractor.
     *
     * @var VariablesExtractor
     */
    protected $variablesExtractor;

    /**
     * Initializes the DataLayer.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container          = $container;
        $this->dataLayerMap       = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('data_layer', []);
        $this->variablesExtractor = $this->container->get('core.variables.extractor');
    }

    /**
     * Get Data Layer parsed array.
     *
     * @return array $data The Data layer array.
     */
    public function getDataLayer()
    {
        if (empty($this->dataLayerMap)) {
            return null;
        }

        $variables = [];

        foreach ($this->dataLayerMap as $value) {
            $variables[$value['key']] = $this->variablesExtractor->get($value['value']);
        }

        return $variables;
    }

    /**
     * Generates Data Layer code.
     *
     * @param Array   $data The Data Layer data.
     *
     * @return String $code The generated code.
     */
    public function getDataLayerCode()
    {
        $data = $this->getDataLayer();

        if (empty($data)) {
            return '';
        }

        $json = json_encode(
            array_map(function ($a) {
                return $a === null ? '' : $a;
            }, $data)
        );

        $json = str_replace('"%device%"', 'device', $json);

        $code = '<script>
            var device = (window.innerWidth || document.documentElement.clientWidth '
            . '|| document.body.clientWidth) < 768 ? "phone" : '
            . '((window.innerWidth || document.documentElement.clientWidth '
            . '|| document.body.clientWidth) < 992 ? "tablet" : "desktop");
            dataLayer = [' . $json . '];</script>';

        return $code;
    }

    /**
     * Get the available types for data layer.
     *
     * @return Array The available types.
     */
    public function getTypes()
    {
        return [
            'authorId'        => _('Author id'),
            'authorName'      => _('Author name'),
            'blank'           => _('Blank'),
            'canonicalUrl'    => _('Canonical url'),
            'categoryId'      => _('Category id'),
            'categoryName'    => _('Category name'),
            'contentId'       => _('Content id'),
            'device'          => _('Devices'),
            'extension'       => _('Page type'),
            'format'          => _('Page format'),
            'instanceName'    => _('Instance name'),
            'isRestricted'    => _('Restricted'),
            'language'        => _('Language'),
            'lastAuthorId'    => _('Last editor id'),
            'lastAuthorName'  => _('Last editor name'),
            'mainDomain'      => _('Main domain'),
            'mediaType'       => _('Media element'),
            'pretitle'        => _('Pretitle'),
            'publicationDate' => _('Published date'),
            'publisherId'     => _('Publisher id'),
            'publisherName'   => _('Publisher name'),
            'tagNames'        => _('Tag names'),
            'tagSlugs'        => _('Tag slugs'),
            'updateDate'      => _('Updated date'),
        ];
    }
}
