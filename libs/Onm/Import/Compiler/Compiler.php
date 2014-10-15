<?php
/**
 * Defines the Onm\Import\Compiler\Compiler class
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
namespace Onm\Import\Compiler;

use Onm\Import\DataSource\DataSourceFactory;
use Onm\Import\Synchronizer\Synchronizer;

/**
 * Handles all the common methods in the importers
 *
 * @package Onm_Import
 **/
class Compiler
{
    /**
     * The path where to save the downloaded files
     *
     * @var string
     **/
    public $syncPath = '';


    /**
     * Initializes the object
     *
     * @param array $config the configuration for the synchronizer (cache_path, importers)
     *
     * @return void
     */
    public function __construct($syncPath)
    {
        $this->syncPath = $syncPath;
    }

    /**
     * Creates a "binary" file containing the list of news from all the source servers
     *
     * @param  array $servers list of server data to build from
     **/
    public function compileServerContents($servers)
    {
        $elements = [];
        foreach ($servers as $server) {
            $files = Synchronizer::getLocalFileListForSource($this->syncPath, $server['id']);
            foreach ($files as $file) {
                $element = DataSourceFactory::get($this->syncPath.'/'.$file);

                if (is_object($element)) {
                    $element = $element->toArray();
                    $element['source_id']    = $server['id'];
                    $element['created_time'] = \DateTime::createFromFormat(
                        \DateTime::ISO8601,
                        $element['created_time']
                    );
                    $elements []= $element;
                }
            }
        }

        usort($elements, function ($a, $b) {
            return ($a['created_time'] < $b['created_time']) ? 1 : -1;
        });

        foreach ($elements as &$element) {
            $element['created_time'] = $element['created_time']->format(\DateTime::ISO8601);
        }

        $this->cleanOldCompiledServerContents();

        $now = time();
        $syncFile = $this->syncPath.'/serversync.'.$now.'.php';
        file_put_contents($syncFile, serialize($elements));
    }

    /**
     * Removes old compiled news files
     **/
    public function cleanOldCompiledServerContents()
    {
        $syncFile = $this->syncPath.'/serversync.*.php';
        $files = glob($syncFile);

        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * Removes the local files for a given source id and compiles the others
     *
     * @param int $id the source identification
     * @param string $servers all the configured servers
     *
     * @return boolean true if the files were deleted
     * @throws Exception If the files weren't deleted
     **/
    public function cleanCompileForSourceID($sourceId, $servers)
    {
        $path = realpath($this->syncPath.DIRECTORY_SEPARATOR.$sourceId);

        if (!empty($path)) {
            \FilesManager::deleteDirectoryRecursively($path);

            unset($servers[$sourceId]);

            $this->compileServerContents($servers);

            return true;
        }

        return false;
    }

    /**
     * Get elements from the compiled file
     *
     * @return array of unserialized elements
     **/
    public function getElementsFromCompileFile()
    {
        $fileListing = glob($this->syncPath.'/serversync.*.php');
        $serverFile = $fileListing[0];
        return unserialize(file_get_contents($serverFile));
    }
}
