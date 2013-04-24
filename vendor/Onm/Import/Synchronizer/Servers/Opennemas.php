<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\Synchronizer\Servers;

use \Onm\Import\Synchronizer\ServerAbstract;
use \Onm\Import\Synchronizer\ServerInterface;

/**
 * Class to synchronize local folders with an HTTP Onm server.
 *
 * @package    Onm_Import
 */
class Opennemas extends ServerAbstract implements ServerInterface
{
    /**
     * Opens an HTTP connection with the parameters of the object
     *
     * @param array $params the list of params to the http connection
     *
     * @throws Exception, if something went wrong while connecting to FTP server
     */
    public function __construct($params = null)
    {
        $this->canHandle($params);

        $this->params = $params;

        if ($params['sync_from'] = 'no_limits') {
            $params['sync_from'] = 3460000;
        }

        $this->serverUrl = $params['url'].'/export.xml?until='.$params['sync_from'].'&auth='.$params['password'];

        $contentListString = @file_get_contents($this->serverUrl);
        // test if the connection was successful
        if (!$contentListString) {
            throw new \Exception(
                sprintf(
                    _(
                        'Can\'t connect to server %s. Please check your'
                        .' connection details.'
                    ),
                    $params['name']
                )
            );
        }

        $this->contentList = simplexml_load_string($contentListString);

        return $this;
    }

    /**
     * Fetch content from an url and save the file into a local path
     *
     * @param $id the id of the file to be saved
     * @param $url the url from where to get the file content
     *
     * @return true if the file has been saved correctly
     **/
    public function fetchContentAndSave($id, $url)
    {
        $articleString = @file_get_contents($url.'?auth='.$this->params['password']);

        $localFilePath = $this->params['sync_path'].DIRECTORY_SEPARATOR.strtolower($id.'.xml');
        if (!file_exists($localFilePath)) {
            @file_put_contents($localFilePath, $articleString);

            // $element = \Onm\Import\DataSource\DataSourceFactory::get($localFilePath);
            // if (is_object($element)) {
            //     $date = $element->getCreatedTime();
            //     touch($localFilePath, $date->getTimestamp());
            // }

            return true;
        }

    }

    /**
     * Check if this server class can handle the http service
     *
     * @param $params the http server parameters
     *
     * @return true if the url matches the pattern for this server
     *
     * @throws Exception, if this server class can't handle this service url
     **/
    public function canHandle($params)
    {
        // Check url
        $res = preg_match('@http://(.*)/ws/agency@', $params['url'], $matches);
        if ($res) {
            return true;
        }

        throw new \Exception(
            sprintf(
                _(
                    'Can\'t connect to server %s. Please check your'
                    .' connection details.'
                ),
                $params['name']
            )
        );

    }
}
