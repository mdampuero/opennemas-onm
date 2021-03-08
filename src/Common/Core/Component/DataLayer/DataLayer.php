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
        'category', 'instance_name', 'extension', 'author_name', 'author_id',
        'last_modify', 'keywords', 'published_time', 'content_id', 'media_type',
         'seotag', 'device', 'user_id', 'ga_id', 'subscription'
    ];

    /**
     * Generates Data Layer code.
     *
     * @param Array   $data The Data Layer data.
     *
     * @return String $code The generated code.
     */
    public function getDataLayerCode($data)
    {
        $data = $this->parseDataMap($data);

        if (empty($data)) {
            return '';
        }

        $code = '<script>
            dataLayer = [{
                ' . json_encode($data) . '
            }];
        </script>';

        return $code;
    }

    /**
     * Generates Data Layer code for AMP pages using Google TagManager.
     *
     * @param Array   $data The Data Layer data.
     *
     * @return String $code The generated code.
     */
    public function getDataLayerAMPCodeGTM($data)
    {
        $data = $this->parseDataMap($data);

        if (empty($data)) {
            return '';
        }

        $code = '<script type="application/json ">
            {
                "vars" : {
                    ' . json_encode($data) . '
                }
            }
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
    public function getDataLayerAMPCodeGA($data)
    {
        $data = $this->parseDataMap($data);

        if (empty($data)) {
            return '';
        }

        $code = '<script type="application/json ">
            {
                "vars" : {
                    ' . json_encode($data) . '
                }
            }
        </script>';

        return $code;
    }

    /**
     * Get the available types for data layer.
     *
     * @return Array The available types.
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Parse data map.
     *
     * @return Array The parsed data map.
     */
    protected function parseDataMap($data)
    {
        if (empty($data)) {
            return null;
        }

        $variables = [];
        foreach ($data as $value) {
            // Proccess values before generate json elements
            $variables[$value['key']] = $value['value'];
        }

        return $variables;
    }
}
