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
use Common\NewsAgency\Component\Repository\LocalRepository;
use Common\Model\Entity\Instance;
use Opennemas\Data\Serialize\Serializer\PhpSerializer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Synchronizes contents from an external server and makes them ready-to-import.
 */
class Synchronizer
{
    protected $cachePath;

    /**
     * The Filesystem component.
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * The path to the file used to lock synchronization.
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
     * The repository component.
     *
     * @var LocalRepository
     */
    protected $repository;

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
    protected $stats = [];

    /**
     * The path to the file used to save synchronization statistics.
     *
     * @var string
     */
    protected $syncFilePath = '';

    /**
     * The path where to save the downloaded files
     *
     * @var string
     */
    protected $syncPath = '';

    /**
     * The syncronization statistics.
     *
     * @var array
     */
    protected $serverStats = [];

    /**
     * Initializes the Synchronizer.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->fs         = new Filesystem();
        $this->repository = new LocalRepository();

        $this->cachePath = $container->getParameter('core.paths.cache');
        $this->logger    = $container->get('application.log');
        $this->pf        = $container->get('news_agency.factory.parser');
        $this->sf        = $container->get('news_agency.factory.server');

        $this->setInstance($container->get('core.instance'));
        $this->resetStats();
    }

    /**
     * Removes all contents for a server or a list of servers.
     *
     * @param array $servers A server or a list of servers.
     */
    public function empty(array $servers) : void
    {
        if (!$this->isSyncEnvironmentReady()) {
            return;
        }

        $this->lockSync();

        if (array_key_exists('id', $servers)) {
            $servers = [ $servers ];
        }

        foreach ($servers as $server) {
            $this->emptyServer($server);
        }

        $this->unlockSync();
    }

    /**
     * Returns the statistics.
     *
     * @return array The statistics.
     */
    public function getResourceStats() : array
    {
        return $this->stats;
    }

    /**
     * Returns the last synchronization statistics.
     *
     * @return array The last synchronization statistics.
     */
    public function getServerStats() : array
    {
        $stats = null;

        if ($this->fs->exists($this->syncFilePath)) {
            $stats = PhpSerializer::unserialize(
                $this->getFile($this->syncFilePath)->getContents()
            );
        }

        if (empty($stats)) {
            return [];
        }

        return $stats;
    }

    /**
     * Checks if the environment is locked.
     *
     * @return bool True if the environment is locked. False otherwise.
     */
    public function isLocked() : bool
    {
        return $this->fs->exists($this->lockFilePath);
    }

    /**
     * Resets the synchronizer statistics.
     *
     * @return Synchronizer The current synchronizer.
     */
    public function resetStats() : Synchronizer
    {
        $this->stats = [
            'contents'   => 0,
            'deleted'    => 0,
            'downloaded' => 0,
            'parsed'     => 0,
            'invalid'    => 0,
            'valid'      => 0,
        ];

        return $this;
    }

    /**
     * Synchronizes contents for a list of servers.
     *
     * @param array $servers A server or a list of servers.
     *
     * @return Synchronizer The current synchronizer.
     */
    public function synchronize(array $servers) : Synchronizer
    {
        if (!$this->isSyncEnvironmentReady()) {
            $this->setupSyncEnvironment();
        }

        $this->lockSync();

        if (array_key_exists('id', $servers)) {
            $servers = [ $servers ];
        }

        foreach ($servers as $server) {
            if ($server['activated'] == '1') {
                $this->updateServer($server);
            }
        }

        $this->updateSyncFile();
        $this->unlockSync();

        return $this;
    }

    /**
     * Removes files for the provided server.
     *
     * @param array $server The server information.
     */
    protected function cleanServer($server) : void
    {
        if ($server['sync_from'] === 'no_limits') {
            return;
        }

        $date = sprintf('< now - %s seconds', $server['sync_from']);

        $path  = $this->syncPath . '/' . $server['id'];
        $files = $this->getFinder()->in($path)
            ->date($date)
            ->files();

        foreach ($files as $file) {
            $this->fs->remove($file);
            $this->stats['deleted']++;
        }

        $files = $this->getFinder()->in($this->syncPath)
            ->name('/sync.' . $server['id'] . '.*.php/')
            ->files();

        $this->fs->remove($files);
    }

    /**
     * Removes files for the provided server.
     *
     * @param array $server The server.
     */
    protected function emptyServer(array $server) : void
    {
        $path = $this->syncPath . '/' . $server['id'];

        $this->fs->remove($path);

        $files = $this->getFinder()->in($this->syncPath)
            ->name('/sync.' . $server['id'] . '.*.php/')
            ->files();

        $this->fs->remove($files);
    }

    /**
     * Returns a File object from a path.
     *
     * @param string $path The path to the file.
     *
     * @return SplFileInfo The file.
     */
    protected function getFile(string $path) : SplFileInfo
    {
        return new SplFileInfo($path, $path, $path);
    }

    /**
     * Returns the list of files for the server.
     *
     * @param array $server The server.
     *
     * @return array The list of files.
     */
    protected function getFiles(array $server) : array
    {
        $path  = $this->syncPath . '/' . $server['id'];
        $paths = [];

        $files = $this->getFinder()->in($path)
            ->name('/.*\.xml/')
            ->files();

        foreach ($files as $file) {
            $paths[] = $file->getRealPath();
        }

        return $paths;
    }

    /**
     * Returns a new Finder.
     *
     * @return Finder The finder.
     */
    protected function getFinder()
    {
        return new Finder();
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

        return array_values(array_map(function ($a) {
            return [
                'filename' => $a->file_name,
                'url'      => $a->file_path
            ];
        }, array_filter($contents, function ($a) use ($fs, $path) {
            return ($a->type === 'photo' || $a->type === 'video')
                && !$fs->exists($path . '/' . $a->file_name);
        })));
    }

    /**
     * Returns the path for the provided server and creates the directory if it
     * does not exist.
     *
     * @param array $server The server information
     *
     * @return string The path for the server.
     */
    protected function getServerPath(array $server) : string
    {
        $path = $this->syncPath . '/' . $server['id'];

        if (!$this->fs->exists($path)) {
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
    protected function isSyncEnvironmentReady() : bool
    {
        return $this->fs->exists($this->syncPath);
    }

    /**
     * Returns a XML object from a file.
     *
     * @param string $path The path to the file.
     *
     * @return \SimpleXMLElement The XML object.
     *
     * @codeCoverageIgnore
     */
    protected function loadXmlFile(string $path) : \SimpleXMLElement
    {
        $xml = simplexml_load_file($path, 'SimpleXMLElement', LIBXML_NOERROR);

        if (empty($xml)) {
            throw new \InvalidArgumentException();
        }

        return $xml;
    }

    /**
     * Creates a lock file to avoid concurrent synchronizations.
     */
    protected function lockSync()
    {
        $this->fs->touch($this->lockFilePath);
        $this->fs->chgrp($this->lockFilePath, 'www-data', true);
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
            if (!$this->fs->exists($file)) {
                continue;
            }

            try {
                $xml = $this->loadXmlFile($file);

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

                $this->stats['valid']++;
            } catch (\Exception $e) {
                $this->stats['invalid']++;
                $this->logger->notice('Cannot parse XML: ' . $file);
            }

            $this->stats['parsed']++;
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
     *
     * @codeCoverageIgnore
     */
    protected function removeInvalidContents(array $contents, string $path) : array
    {
        $valid = [];

        foreach ($contents as $content) {
            if ($content->type !== 'photo') {
                $valid[] = $content;

                continue;
            }

            $filePath = $path . '/' . $content->file_name;

            if ($this->fs->exists($filePath)) {
                $content->size = sprintf('%.2f', filesize($filePath) / 1024);
                $valid[]       = $content;
            }
        }

        return $valid;
    }

    /**
     * Configures the Synchronizer for the provided instance.
     *
     * @param Instance $instance The instance.
     *
     * @return Synchronizer The current Synchronizer.
     */
    public function setInstance(Instance $instance) : Synchronizer
    {
        $this->syncPath = sprintf(
            '%s/%s/importers',
            $this->cachePath,
            $instance->internal_name
        );

        $this->syncFilePath = $this->syncPath . '/.sync';
        $this->lockFilePath = $this->syncPath . '/.lock';

        $this->serverStats = $this->getServerStats();

        return $this;
    }

    /**
     * Sets up the environment for a new synchronization.
     */
    protected function setupSyncEnvironment() : void
    {
        if (!$this->fs->exists($this->syncPath)) {
            $this->fs->mkdir($this->syncPath);
        }
    }

    /**
     * Deletes the lock file.
     */
    protected function unlockSync() : void
    {
        if ($this->fs->exists($this->lockFilePath)) {
            $this->fs->remove($this->lockFilePath);
        }
    }

    /**
     * Synchronizes contents for a server.
     *
     * @param array $server The server configuration.
     */
    protected function updateServer(array $server) : void
    {
        $date = new \DateTime();

        $date->setTimeZone(new \DateTimeZone('UTC'));
        $this->serverStats[$server['id']] = $date->format('Y-m-d H:i:s');

        $path   = $this->getServerPath($server);
        $source = $this->sf->get($server);

        $source->getRemoteFiles()->downloadFiles($path);
        $this->cleanServer($server);

        $files = $this->getFiles($server);

        $contents = $this->parseFiles($files, $server['id']);
        $missing  = $this->getMissingFiles($contents, $path);

        if (!empty($missing)) {
            $source->downloadFiles($path, $missing);
        }

        $this->fs->chgrp($path, 'www-data', true);

        $contents = $this->removeInvalidContents($contents, $path);
        $filePath = sprintf(
            '%s/sync.%s.%s.php',
            $this->syncPath,
            $server['id'],
            time()
        );

        $this->repository->remove($filePath)
            ->setContents($contents)
            ->write($filePath);

        $this->fs->chgrp($filePath, 'www-data', true);

        $this->stats['contents']   += count($contents);
        $this->stats['downloaded'] += $source->downloaded;
    }

    /**
     * Update statistics of synchronization file.
     */
    protected function updateSyncFile() : void
    {
        $this->fs->dumpFile(
            $this->syncFilePath,
            PhpSerializer::serialize($this->serverStats)
        );

        $this->fs->chgrp($this->syncFilePath, 'www-data', true);
    }
}
