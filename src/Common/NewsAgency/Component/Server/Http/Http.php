<?php
/*
 * This file is part of the onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Server\Http;

use Common\NewsAgency\Component\Server\Server;

/**
 * Synchronize local folders with an external XML-based source server.
 */
abstract class Http extends Server
{
    /**
     * {@inheritdoc}
     */
    public function checkConnection() : bool
    {
        return !empty($this->getContentFromUrl($this->getUrl()));
    }

    /**
     * {@inheritdoc}
     */
    public function downloadFiles(string $path, ?array $files = null) : Server
    {
        if (empty($files)) {
            $files = $this->remoteFiles;
        }

        if (!is_writable($path)) {
            throw new \Exception(
                sprintf(_('Directory %s is not writable.'), $path)
            );
        }

        foreach ($files as $file) {
            $localFile = $path . '/' . $file['filename'];

            if (!file_exists($localFile)) {
                $content = $this->getContentFromUrl($file['url']);

                file_put_contents($localFile, $content);

                $this->localFiles[] = $localFile;
                $this->downloaded++;
            }
        }

        return $this;
    }
}
