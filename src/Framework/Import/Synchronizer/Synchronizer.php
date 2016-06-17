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
     * The service container.
     *
     * @var Template
     */
    protected $tpl;

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
     * @param string   $path The path to synchronized files.
     * @param Template $tpl  The template service.
     */
    public function __construct($path, $tpl)
    {
        $this->syncPath     = $path . DS .'importers';
        $this->syncFilePath = $this->syncPath . DS . '.sync';
        $this->lockFilePath = $this->syncPath . DS . '.lock';

        $this->compiler      = new Compiler($this->syncPath);
        $this->parserFactory = new ParserFactory();
        $this->serverFactory = new ServerFactory($tpl);
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
                && !file_exists($path . DS . $content->file_name)
            ) {
                $missing[] = [
                    'filename' => $content->file_name,
                    'url'      => $content->file_path
                ];
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
        $params = null;

        if (file_exists($this->syncFilePath)) {
            $params = unserialize(file_get_contents($this->syncFilePath));
        }

        if (empty($params)) {
            return [ 'last_import' => '' ];
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

        $interval = abs(strtotime($params['last_import']) - time());

        return round($interval / 60);
    }

    /**
     * Parses and returns the list of contents in the files.
     *
     * @param array $files  The files to parse.
     * @param array $source The server id.
     *
     * @return array The list of contents.
     */
    public function parseFiles($files, $source)
    {
        $contents = [];
        foreach ($files as $file) {
            $xml = @simplexml_load_file($file);

            if ($xml) {
                $parser = $this->parserFactory->get($xml);
                $parsed = $parser->parse($xml, $file);

                if (is_object($parsed)) {
                    $parsed = [ $parsed ];
                }

                foreach ($parsed as $p) {
                    $p->filename = basename($file);
                    $p->source   = $source;
                }

                $contents = array_merge($contents, $parsed);
            }
        }

        return $contents;
    }

    /**
     * Resets the synchronizer statistics.
     */
    public function resetStats()
    {
        $this->stats = [ 'contents' => 0, 'deleted' => 0, 'downloaded' => 0 ];
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
        $server['path'] = $this->syncPath . DS . $server['id'];

        if (!is_dir($server['path'])) {
            mkdir($server['path']);
        }

        $source = $this->serverFactory->get($server);
        $source->downloadFiles();

        $contents = $this->parseFiles($source->localFiles, $server['id']);

        // Check for missing files (photos, videos, ...)
        $missing = $this->getMissingFiles($contents, $server['path']);

        if (!empty($missing)) {
            $source->downloadFiles($missing);
        }

        foreach ($contents as $content) {
            if ($content->type === 'photo') {
                $path = $this->syncPath . DS . $server['id'] . DS . $content->file_name;

                if (file_exists($path)) {
                    $content->size = sprintf('%.2f', filesize($path) / 1024);
                }
            }
        }

        $this->compiler->cleanCompileForServer($server['id']);
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

        foreach ($servers as $server) {
            if ($server['activated'] == '1') {
                $this->sync($server);
            }
        }

        $this->updateSyncFile();
        $this->unlockSync();
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
        $params = [ 'last_import' => date('c') ];

        file_put_contents($this->syncFilePath, serialize($params));

        return $params;
    }
}
