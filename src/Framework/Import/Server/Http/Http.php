<?php
/*
 * This file is part of the onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Server\Http;

use Framework\Import\Server\Server;

/**
 * Synchronize local folders with an external XML-based source server.
 */
abstract class Http extends Server
{
    /**
     * Opens an HTTP connection basing on the server parameters.
     *
     * @param array         $params The server parameters.
     * @param TemplateAdmin $tpl    The template service.
     *
     * @throws \Exception If the server parameters are not valid.
     */
    public function __construct($params, $tpl)
    {
        parent::__construct($params, $tpl);

        $this->getRemoteFiles();
    }

    /**
     * {@inheritdoc}
     */
    public function downloadFiles($files = null)
    {
        if (empty($files)) {
            $files = $this->remoteFiles;
        }

        if (!is_writable($this->params['path'])) {
            throw new \Exception(
                sprintf(
                    _('Directory %s is not writable.'),
                    $this->params['path']
                )
            );
        }

        foreach ($files as $file) {
            $localFile = $this->params['path'] . DS . $file['filename'];

            if (!file_exists($localFile)) {
                $content = $this->getContentFromUrl($file['url']);

                file_put_contents($localFile, $content);

                $this->localFiles[] = $localFile;
                $this->downloaded++;
            }
        }
    }

    /**
     * Gets and returns the list of remote files.
     *
     * @return array The list of remote files.
     */
    abstract public function getRemoteFiles();
}
