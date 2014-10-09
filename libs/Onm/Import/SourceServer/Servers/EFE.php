<?php
/*
 * Implements the EFE class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\SourceServer\Servers;

use \Onm\Import\SourceServer\ServerAbstract;
use \Onm\Import\SourceServer\ServerInterface;

/**
 * Class to synchronize local folders with an HTTP Efe server.
 *
 * @package    Onm_Import
 */
class EFE extends ServerAbstract implements ServerInterface
{
    /**
     * Opens an HTTP connection with the parameters of the object
     *
     * @param array $params the list of params to the http connection
     *
     * @throws Exception, if something went wrong while connecting to HTTP server
     */
    public function __construct($params = null)
    {
        $this->canHandle($params);

        $this->params = $params;

        $this->serverUrl = $params['url'];

        $contentListString = $this->getContentFromUrlWithDigestAuth($this->serverUrl);
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
     * Get content from a given url using http digest auth
     *
     * @param $url the http server url
     *
     * @return $content the content from this url
     *
     **/
    public function getContentFromUrlWithDigestAuth($url)
    {
        $url = str_replace(
            'http://',
            'http://'.$this->params['username'].':'.$this->params['password'].'@',
            $url
        );

        $content = @file_get_contents($url);

        return $content;
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
        $res = preg_match('@efeservicios@', $params['url'], $matches);
        if ($res) {
            return true;
        }

        throw new \Exception(
            sprintf(
                _('Can\'t connect to server %s. Please check your connection details.'),
                $params['name']
            )
        );

    }
}
