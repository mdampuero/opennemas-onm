<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\DataLayer;

use function GuzzleHttp\json_encode;

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
        'categoryName', 'contentId', 'device', 'extension', 'format', 'hostName',
        'instanceName', 'tags', 'language', 'lastAuthorId', 'lastAuthorName',
        'mediaType', 'publishedDate', 'seoTags', 'subscription', 'updateDate',
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
        $data = $this->parseDataMap();

        if (empty($data)) {
            return '';
        }

        $device = '';
        if (array_key_exists('device', $data)) {
            $device = 'dataLayer.push({ "device":device });';
        }

        $code = '<script>
            var device = (window.innerWidth || document.documentElement.clientWidth '
            . '|| document.body.clientWidth) < 768 ? "phone" : '
            . '((window.innerWidth || document.documentElement.clientWidth '
            . '|| document.body.clientWidth) < 992 ? "tablet" : "desktop");
            dataLayer = [' . json_encode($data) . '];'
            . $device . '</script>';

        return $code;
    }

    /**
     * Generates Data Layer code for AMP pages using Google TagManager.
     *
     * @param Array   $data The Data Layer data.
     *
     * @return String $code The generated code.
     */
    public function getDataLayerAMPCodeGTM()
    {
        $data = $this->parseDataMap();

        if (empty($data)) {
            return '';
        }

        $code = '<script type="application/json">
            { "vars" : ' . json_encode($data) . ' }
        </script>';

        return $code;
    }

    /**
     * Generates Data Layer code for AMP pages using Google Analytics.
     *
     * @param Array   $data The Data Layer data.
     *
     * @return String $code The generated code.
     */
    public function getDataLayerAMPCodeGA()
    {
        $data = $this->parseDataMap();

        if (empty($data)) {
            return '';
        }

        $code = '"vars" : ' . json_encode($data);

        return $code;
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
            _('Page type'), _('Page format'), _('Hostname'), _('Instance name'),
            _('Tags'), _('Language'), _('Last editor Id'), _('Last editor name'),
            _('Media element'), _('Published date'), _('Seo tags'),
            _('Subscription'), _('Updated date'),
        ];


        $this->types = array_combine($this->types, $typesTranslated);

        return $this->types;
    }

    /**
     * Parse data map.
     *
     * @return Array The parsed data map.
     */
    protected function parseDataMap()
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
}
