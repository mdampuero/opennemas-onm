<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Synchronizer;

use Common\NewsAgency\Component\Factory\ParserFactory;
use Common\NewsAgency\Component\Factory\ServerFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Framework\Import\Repository\LocalRepository;

/**
 * Synchronizes contents from an external server and makes them ready-to-import.
 */
class Synchronizer
{
    /**
     * File path used for locking purposes.
     *
     * @var string
     */
    protected $lockFilePath = '';

    /**
     * The logger service.
     *
     * @var Monolog
     */
    protected $logger;

    /**
     * The parser factory.
     *
     * @var ParserFactory
     */
    protected $pf;

    /**
     * The server factory.
     *
     * @var ServerFactory
     */
    protected $sf;

    /**
     * The array of synchronization statistics.
     *
     * @var array
     */
    protected $stats = [ 'contents' => 0, 'deleted' => 0, 'downloaded' => 0 ];

    /**
     * The path where to save the downloaded files
     *
     * @var string
     */
    protected $syncPath = '';

    /**
     * Initializes the Synchronizer.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->finder = new Finder();
        $this->fs     = new Filesystem();
        $this->pf     = $container->get('news_agency.factory.parser');
        $this->sf     = $container->get('news_agency.factory.server');
        $this->logger = $container->get('error.log');

        $this->syncPath = sprintf(
            '%s/%s/importers',
            $container->getParameter('core.paths.cache'),
            $container->get('core.instance')->internal_name
        );

        $this->syncFilePath = $this->syncPath . '/.sync';
        $this->lockFilePath = $this->syncPath . '/.lock';
    }

    /**
     * Removes all contents for a server or a list of servers.
     *
     * @param array $servers A server or a list of servers.
     */
    public function empty(array $servers) : void
    {
        if (!$this->isSyncEnvironmetReady()) {
            return;
        }

        $this->lockSync();

        if (array_key_exists('id', $servers)) {
            $servers = [ $servers ];
        }

        foreach ($servers as $server) {
            $this->emptyServer($server);
        }

        $this->updateSyncFile();
        $this->unlockSync();
    }

    /**
     * Returns the last synchronization statistics.
     *
     * @return array The last synchronization statistics.
     */
    public function getSyncParams() : array
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
     * Returns the number of minutes since last synchronization.
     *
     * @return int The number of minutes since last synchronization.
     */
    public function minutesFromLastSync() : int
    {
        $params = $this->getSyncParams();

        $interval = abs(strtotime($params['last_import']) - time());

        return round($interval / 60);
    }

    /**
     * Resets the synchronizer statistics.
     */
    public function resetStats() : void
    {
        $this->stats = [ 'contents' => 0, 'deleted' => 0, 'downloaded' => 0 ];
    }

    /**
     * Synchronizes contents for a list of servers.
     *
     * @param array $servers A server or a list of servers.
     */
    public function synchronize(array $servers) : void
    {
        if (!$this->isSyncEnvironmetReady()) {
            $this->setupSyncEnvironment();
        }

        $this->lockSync();

        if (array_key_exists('id', $servers)) {
            $servers = [ $servers ];
        }

        foreach ($servers as $server) {
            if ($server['activated'] == '1') {
                $this->updateServer($server);
                $this->cleanServer($server);
            }
        }

        $this->updateSyncFile();
        $this->unlockSync();
    }

    /**
     * Removes files for the provided item.
     *
     * @param array $item The item information.
     */
    protected function cleanServer($server) : void
    {
        if ($server['sync_from'] === 'no_limits') {
            return;
        }

        $date = sprintf('< now - %s seconds', $server['sync_from']);

        $path  = $this->syncPath . '/' . $server['id'];
        $files = $this->finder->in($path)
            ->date($date)
            ->files();

        foreach ($files as $file) {
            $this->fs->remove($file);
        }

        $files = $this->finder->in($this->syncPath)
            ->name('/sync.' . $server['id'] . '.*.php/')
            ->files();

        foreach ($files as $file) {
            $this->fs->remove($file);
        }
    }

    /**
     * Removes files for the provided item.
     *
     * @param array $item The item information.
     */
    protected function emptyServer($server) : void
    {
        $path  = $this->syncPath . '/' . $server['id'];
        $files = $this->finder->in($path)->files();

        foreach ($files as $file) {
            $this->fs->remove($file);
        }

        $files = $this->finder->in($this->syncPath)
            ->name('/sync.' . $server['id'] . '.*.php/')
            ->files();

        foreach ($files as $file) {
            $this->fs->remove($file);
        }
    }

    /**
     * Checks if some files (photos, videos, etc.) are missing.
     *
     * @param array  $contents The list of contents to check.
     * @param string $path     The path to content files.
     *
     * @return array The list of missing files.
     */
    protected function getMissingFiles($contents, $path) : array
    {
        $fs = $this->fs;

        return array_map(function ($a) {
            return [
                'filename' => $a->file_name,
                'url'      => $a->file_path
            ];
        }, array_filter($contents, function ($a) use ($fs) {
            return ($a->type === 'photo' || $a->type === 'video')
                && !$fs->exists($path . '/' . $a->file_name);
        }));
    }

    /**
     * Returns the path for the provided server and creates the directory if it
     * does not exist.
     *
     * @param array $item The server information
     *
     * @return string The path for the server.
     */
    protected function getPath(array $item) : string
    {
        $path = $this->syncPath . '/' . $item['id'];

        if (!$this->fs->exists($path) || !is_dir($path)) {
            $this->fs->mkdir($path);
        }

        return $path;
    }

    /**
     * Returns true if the syncPath exists and is writable.
     *
     * @return bool True if syncPath is present and writable. Otherwise,
     *              return false.
     */
    protected function isSyncEnvironmetReady() : bool
    {
        return file_exists($this->syncFilePath)
            && is_writable($this->syncPath)
            && is_writable($this->syncFilePath);
    }

    /**
     * Creates a lock file to avoid concurrent synchronizations.
     */
    protected function lockSync()
    {
        try {
            touch($this->lockFilePath);
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * Parses and returns the list of contents in the files.
     *
     * @param array $files  The files to parse.
     * @param array $source The server id.
     *
     * @return array The list of contents.
     */
    protected function parseFiles(array $files, string $source) : array
    {
        $contents = [];
        foreach ($files as $file) {
            try {
                $xml = simplexml_load_file($file);

                $parser = $this->pf->get($xml);
                $parsed = $parser->parse($xml, $file);

                if (is_object($parsed)) {
                    $parsed = [ $parsed ];
                }

                foreach ($parsed as $p) {
                    $p->filename = basename($file);
                    $p->source   = $source;
                }

                $contents = array_merge($contents, $parsed);
            } catch (\Exception $e) {
                $this->logger->error('Cannot parse XML: ' . $file);
            }
        }

        return $contents;
    }

    /**
     * Returns the list of contents after removing invalid contents. A content
     * is invalid when it is a photo and the file can not be found in path.
     *
     * @param array  $contents The list of contents.
     * @param string $path     The path to search media in.
     *
     * @return array The list of valid contents.
     */
    protected function removeInvalidContents(array $contents, string $path) : array
    {
        $valid = [];

        foreach ($contents as &$content) {
            if ($content->type !== 'photo') {
                $valid[] = $content;
            }

            $filePath = $path . '/' . $content->file_name;

            if (file_exists($filePath)) {
                $content->size = sprintf('%.2f', filesize($filePath) / 1024);
                $valid[]       = $content;
            }
        }

        return $valid;
    }

    /**
     * Sets up the environment for a new synchronization.
     */
    protected function setupSyncEnvironment() : void
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
    public function unlockSync() : void
    {
        if (file_exists($this->lockFilePath)) {
            unlink($this->lockFilePath);
        }
    }

    /**
     * Synchronizes contents for a server.
     *
     * @param array $server The server configuration.
     */
    protected function updateServer(array $server) : void
    {
        $path   = $this->getPath($server);
        $source = $this->sf->get($server);

        $source->getRemoteFiles()->downloadFiles($path);

        $contents = $this->parseFiles($source->getFiles(), $server['id']);
        $missing  = $this->getMissingFiles($contents, $path);

        if (!empty($missing)) {
            $source->downloadFiles($path, $missing);
        }

        $contents = $this->removeInvalidContents($contents, $path);
        $filePath = sprintf(
            '%s/sync.%s.%s.php',
            $this->syncPath,
            $server['id'],
            time()
        );

        $repository = new LocalRepository();
        $repository->remove($filePath)
            ->setContents($contents)
            ->write($filePath);

        $this->stats['contents']   += count($contents);
        $this->stats['deleted']    += $source->deleted;
        $this->stats['downloaded'] += $source->downloaded;
    }

    /**
     * Update statistics of synchronization file.
     */
    protected function updateSyncFile() : void
    {
        $params = [ 'last_import' => date('c') ];

        file_put_contents($this->syncFilePath, serialize($params));
    }
}
