<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Synchronizer;

use Framework\Import\Compiler\Compiler;
use Framework\Import\ParserFactory;
use Framework\Import\ServerFactory;

/**
 * Synchronizes contents from an external server and makes them ready-to-import.
 */
class Synchronizer
{
    /**
     * The array of synchronization statistics.
     *
     * @var array
     */
    public $stats = [ 'contents' => 0, 'deleted' => 0, 'downloaded' => 0 ];

    /**
     * The path where to save the downloaded files
     *
     * @var string
     */
    public $syncPath = '';

    /**
     * File path used for locking purposes
     *
     * @var string
     */
    protected $lockFilePath = '';

    /**
     * Initializes the object and initializes configuration
     *
     * @param array $params The synchronizer parameters.
     */
    public function __construct($params = [])
    {
        $this->syncPath     = $params['cache_path'] . DS .'importers';
        $this->syncFilePath = $this->syncPath . DS . '.sync';
        $this->lockFilePath = $this->syncPath . DS . '.lock';

        $this->compiler      = new Compiler($this->syncPath);
        $this->serverFactory = new ServerFactory();
        $this->parserFactory = new ParserFactory();
    }

    /**
     * Checks if some files (photos, videos, etc.) are missing.
     *
     * @param array  $contents The list of contents to check.
     * @param string $path     The path to content files.
     *
     * @return array The list of missing files.
     */
    public function getMissingFiles($contents, $path)
    {
        $missing = [];
        foreach ($contents as $content) {
            if (($content->type === 'photo'
                    || $content->type === 'video')
                && !file_exists($path . DS . $content->file_path)
            ) {
                $missing[] = $content->file_path;
            }
        }

        return $missing;
    }

    /**
     * Returns the last synchronization statistics.
     *
     * @return array The last synchronization statistics.
     */
    public function getSyncParams()
    {
        $params = [ 'lastimport' => '' ];

        if (file_exists($this->syncFilePath)) {
            $params = unserialize(file_get_contents($this->syncFilePath));
        }

        return $params;
    }

    /**
     * Returns true if the syncPath exists and is writable
     *
     * @return boolean True if syncPath is present and writable. Otherwise,
     *                 return false.
     */
    public function isSyncEnvironmetReady()
    {
        return file_exists($this->syncFilePath)
            && is_writable($this->syncPath)
            && is_writable($this->syncFilePath);
    }

    /**
     * Creates a lock file to avoid concurrent synchronizations.
     */
    public function lockSync()
    {
        try {
            touch($this->lockFilePath);
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * Returns the number of minutes since last synchronization.
     *
     * @return integer The number of minutes since last synchronization.
     */
    public function minutesFromLastSync()
    {
        $params = $this->getSyncParams();

        $interval = abs(strtotime($params['lastimport']) - time());

        return round($interval / 60);
    }

    public function parseFiles($files)
    {
        $contents = [];
        foreach ($files as $file) {
            $xml = simplexml_load_file($file);

            $parser = $this->parserFactory->get($xml);
            $parsed = $parser->parse($xml);

            if (is_object($parsed)) {
                $parsed = [ $parsed ];
            }

            $contents = array_merge($contents, $parsed);
        }

        return $contents;
    }

    /**
     * Synchronizes contents for a server.
     *
     * @param array $server The server configuration.
     *
     * @return type Description
     */
    public function sync($server)
    {
        $this->compiler->cleanCompileForServer($server['id']);

        $server['path'] = $this->syncPath . DS . $server['id'];

        if (!is_dir($server['path'])) {
            mkdir($server['path']);
        }

        $source = $this->serverFactory->get($server);
        $source->downloadFiles();

        $contents = $this->parseFiles($source->localFiles);

        // Check for missing files (photos, videos, ...)
        $missing = $this->getMissingFiles($contents, $server['path']);

        if (!empty($missing)) {
            $server->downloadFiles($missing);
        }

        $this->compiler->compile($server['id'], $contents);

        $this->stats['contents']   += count($contents);
        $this->stats['deleted']    += $source->deleted;
        $this->stats['downloaded'] += $source->downloaded;
    }

    /**
     * Synchronizes contents for a list of servers.
     *
     * @param array $servers A list of source servers.
     */
    public function syncMultiple($servers)
    {
        if (!$this->isSyncEnvironmetReady()) {
            $this->setupSyncEnvironment();
        }

        $this->lockSync();

        $messages = array();
        foreach ($servers as $server) {
            if ($server['activated'] == '1') {
                $this->sync($server);
            }
        }

        $this->updateSyncFile();

        $this->unlockSync();

        return $messages;
    }

    /**
     * Sets up the environment for a new synchronization.
     */
    public function setupSyncEnvironment()
    {
        if (!file_exists($this->syncPath)) {
            mkdir($this->syncPath);
        }

        if (!file_exists($this->syncFilePath)) {
            touch($this->syncFilePath);
        }
    }

    /**
     * Deletes the lock file.
     */
    public function unlockSync()
    {
        if (file_exists($this->lockFilePath)) {
            unlink($this->lockFilePath);
        }
    }

    /**
     * Update statistics of synchronization file
     *
     * @param array $importedElements ids of new imported elements
     *
     * @return array array('lastimport' => Date)
     */
    public function updateSyncFile()
    {
        $newSyncParams = [
            'lastimport' => date('c'),
        ];

        file_put_contents($this->syncFilePath, serialize($newSyncParams));

        return $newSyncParams;
    }
}
