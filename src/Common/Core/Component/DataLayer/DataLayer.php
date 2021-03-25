<?php

namespace Common\Core\Component\DataLayer;

/**
 * Generates Data Layer code
 * See more: https://developers.google.com/tag-manager/devguide#datalayer
 */
class DataLayer
{
    /**
     * The available advertisement types.
     *
     * @var array
     */
    protected $types = [
        'authorId', 'authorName', 'blank', 'canonicalUrl', 'categoryId',
        'categoryName', 'contentId', 'device', 'extension', 'format',
        'instanceName', 'isRestricted', 'language', 'lastAuthorId',
        'lastAuthorName', 'mainDomain', 'mediaType', 'pretitle',
        'publicationDate', 'tagsName', 'tagsSlug', 'updateDate',
    ];

    /**
     * Initializes the DataLayer.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
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

        $device = '';
        if (array_key_exists('device', $data)) {
            $device = 'dataLayer.push({ "device":device });';
        }

        $data = json_encode(
            array_map(function ($a) {
                return $a === null ? '' : $a;
            }, $data)
        );

        $code = '<script>
            var device = (window.innerWidth || document.documentElement.clientWidth '
            . '|| document.body.clientWidth) < 768 ? "phone" : '
            . '((window.innerWidth || document.documentElement.clientWidth '
            . '|| document.body.clientWidth) < 992 ? "tablet" : "desktop");
            dataLayer = [' . $data . '];'
            . $device . '</script>';

        return $code;
    }

    /**
     * Get Data Layer parsed array.
     *
     * @return array $data The Data layer array.
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
            // Proccess values before generate json elements
            $variables[$value['key']] = $this->container
                ->get('core.variables.extractor')
                ->get($value['value']);
        }

        return $variables;
    }

    /**
     * Get the available types for data layer.
     *
     * @return Array The available types.
     */
    public function getTypes()
    {
        // Add types translation
        $typesTranslated = [
            _('Author Id'), _('Author name'), _('Blank'), _('Canonical url'),
            _('Category Id'), _('Category name'), _('Content Id'), _('Devices'),
            _('Page type'), _('Page format'), _('Instance name'), _('Subscription'),
            _('Language'), _('Last editor Id'), _('Last editor name'),
            _('Hostname'), _('Media element'), _('Pretitle'), _('Published date'),
            _('Tags'), _('Seo tags'), _('Updated date'),
        ];


        $this->types = array_combine($this->types, $typesTranslated);

        return $this->types;
    }
}
