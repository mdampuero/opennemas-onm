<?php
/**
 * Defines the Onm\Import\Synchronizer\Synchronizer class
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_Import
 **/
namespace Onm\Import\Synchronizer;

use Onm\Import\Synchronizer\LockException;

/**
 * Handles all the common methods in the importers
 *
 * @package Onm_Import
 **/
class Synchronizer
{
    /**
     * The path where to save the downloaded files
     *
     * @var string
     **/
    public $syncPath = '';

    /**
     * File path used for locking purposes
     *
     * @var string
     **/
    protected $lockFilePath = '';

    /**
     * Initializes the object and initializes configuration
     *
     * @param array $config the configuration for the synchronizer (cache_path, importers)
     *
     * @return void
     */
    public function __construct($config = array())
    {
        $this->syncPath     = $config['cache_path']. "/importers";

        $this->syncFilePath = $this->syncPath."/.sync";
        $this->lockFilePath = $this->syncPath."/.lock";
    }

    /**
     * Creates a lock for avoid concurrent sync by multiple users
     *
     * @return void
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
     * Delete the lock for avoid concurrent sync by multiple users
     *
     * @return void
     */
    public function unlockSync()
    {
        if (file_exists($this->lockFilePath)) {
            unlink($this->lockFilePath);
        }
    }

    /**
     * Fetch the statistics of last synchronization
     *
     * @return array array('lastimport' => Date, 'imported_elements' => array())
     */
    public function getSyncParams()
    {
        if (file_exists($this->syncFilePath)) {
            return unserialize(file_get_contents($this->syncFilePath));
        } else {
            return array(
                'lastimport'        => '',
                'imported_elements' => array(),
            );
        }
    }

    /**
     * Gets the minutes from last synchronization of elements
     *
     * @param array $params misc params that alteres function behaviour
     *
     * @return integer minutes from last synchronization of elements
     */
    public function minutesFromLastSync($params = array())
    {
        $params    = $this->getSyncParams();

        $toTime   = strtotime(date('c'));
        $fromTime = strtotime($params['lastimport']);

        return round((abs($toTime - $fromTime) / 60), 0);
    }

    /**
     * Returns true if the syncPath exists and is writable
     *
     * @return boolean true if syncPath is present and writable
     */
    public function isSyncEnvironmetReady()
    {
        return (
            file_exists($this->syncFilePath)
            && is_writable($this->syncPath)
            && is_writable($this->syncFilePath)
        );
    }

    /**
     * Creates the syncPath, to allow to work with it
     *
     * @param array $params the parameters to manipulate the behavior of this function
     */
    public function setupSyncEnvironment($params = array())
    {
        if (!file_exists($this->syncPath)) {
            mkdir($this->syncPath);
        }

        if (!file_exists($this->syncFilePath)) {
            return touch($this->syncFilePath);
        }

        return false;
    }

    /**
     * Update statistics of synchronization file
     *
     * @param array $importedElements ids of new imported elements
     *
     * @return array array('lastimport' => Date, 'imported_elements' => array())
     */
    public function updateSyncFile($importedElements = array())
    {
        $syncParams = $this->getSyncParams();

        if (is_string($importedElements)) {
            $importedElements = array($importedElements);
        }

        // Clean previously imported files that are not present in local cache
        $localElements  = $this->getLocalFileList($this->syncPath);
        $previousImportedElements      = $syncParams['imported_elements'];
        $previousImportedElementsCount = count($previousImportedElements);
        $elements = array();
        for ($i=0; $i < $previousImportedElementsCount; $i++) {
            if (in_array($previousImportedElements[$i], $localElements)) {
                $elements []= $previousImportedElements[$i];
            }
        }

        // Include new importedElements with old ones
        $newImportedelements = array_merge($importedElements, $elements);

        $newSyncParams = array(
            'lastimport'        => date('c'),
            'imported_elements' => $newImportedelements,
        );

        file_put_contents($this->syncFilePath, serialize($newSyncParams));

        return $newSyncParams;
    }

    /**
     * Fetches the files present in $cacheDir.
     *
     * @param string $cacheDir the directory where search files from.
     *
     * @return array the list of files
     */
    public static function getLocalFileList($cacheDir)
    {
        $fileListing = glob($cacheDir.DIRECTORY_SEPARATOR.'*/*.xml');

        usort(
            $fileListing,
            function ($a, $b) {
                return filemtime($a) < filemtime($b);
            }
        );

        foreach ($fileListing as &$file) {
            $file = str_replace($cacheDir.DIRECTORY_SEPARATOR, '', $file);
        }

        return $fileListing;
    }

    /**
     * Fetches the files present in $cacheDir with source.
     *
     * @param string $cacheDir the directory where search files from.
     * @param string $sourceId the source identificator
     * @param string $pattern the pattern of file to download
     *
     * @return array the list of files
     */
    public static function getLocalFileListForSource($cacheDir, $sourceId, $pattern = '*.xml')
    {
        $fileListing = glob($cacheDir.DIRECTORY_SEPARATOR.$sourceId.DIRECTORY_SEPARATOR.$pattern);

        usort(
            $fileListing,
            function ($a, $b) {
                return filemtime($a) < filemtime($b);
            }
        );

        foreach ($fileListing as &$file) {
            $file = str_replace($cacheDir.DIRECTORY_SEPARATOR, '', $file);
        }

        return $fileListing;
    }

    /**
     * Sync elements from news agency server and stores them into temporary dir
     *
     * @param array $params misc params that alters function behavior
     *
     * @return boolean, true if all goes well
     *
     * @throws <b>SyncronizationException</b> if something went wrong while sync
     */
    public function sync($params = array())
    {
        // Check if the folder where store elements is ready and writable
        if (!$this->isSyncEnvironmetReady()) {
            $this->setupSyncEnvironment();
        }

        $serverSyncPath = $this->syncPath.DIRECTORY_SEPARATOR.$params['id'];

        if (!is_dir($serverSyncPath)) {
            mkdir($serverSyncPath);
        }

        $this->lockSync();

        $excludedFiles = self::getLocalFileListForSource($this->syncPath, $params['id'], '*');

        $params['sync_path'] = $serverSyncPath;
        $params['excluded_files'] = $excludedFiles;
        // Needs an abstraction

        $synchronizer = \Onm\Import\SourceServer\ServerFactory::get($params);

        if (is_null($synchronizer)) {
            throw new \Exception(
                sprintf(_("Seems that the server configuration for %s is not correct."), $params['name'])
            );
        }

        $report = $synchronizer->downloadFilesToCacheDir($params);

        $this->unlockSync();

        return $report;
    }

    /**
     * Perform a synchronization over an array of servers
     *
     * @param array $servers a list of servers with its configuration
     *
     * @return void
     **/
    public function syncMultiple($servers)
    {
        $messages = array();
        foreach ($servers as $server) {
            try {
                if ($server['activated'] != '1') {
                    continue;
                }

                $server['allowed_file_extesions_pattern'] = '.*';

                $message = $this->sync($server);

                $messages []= sprintf(
                    _('Downloaded %d new articles and deleted %d old ones from "%s".'),
                    $message['downloaded'],
                    $message['deleted'],
                    $server['name']
                );

            } catch (\Exception $e) {
                $messages []= $e->getMessage();
                $this->unlockSync();

                throw $e;
            }
        }
        $this->updateSyncFile();

        return $messages;
    }

    /**
     * Removes the local files for a given source id
     *
     * @param int $id the source identification
     *
     * @return boolean true if the files were deleted
     * @throws Exception If the files weren't deleted
     **/
    public function deleteFilesForSource($id)
    {
        $path = realpath($this->syncPath.DIRECTORY_SEPARATOR.$id);

        if (!empty($path)) {
            return \FilesManager::deleteDirectoryRecursively($path);
        }
        return false;
    }
}
