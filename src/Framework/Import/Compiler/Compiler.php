<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Compiler;

/**
 * Compiles contents to files and manages compile files.
 */
class Compiler
{
    /**
     * The path for compiles.
     *
     * @var string
     */
    public $path = '';

    /**
     * Initializes a Compiler.
     *
     * @param string $path The synchronization path.
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Creates a binary file with resources from server.
     *
     * @param array $id       The server id.
     * @param array $contents The list of contents to compile.
     */
    public function compile($id, $contents)
    {
        $this->cleanCompileForServer($id);

        // Sort contents by created
        usort($contents, function ($a, $b) {
            return $a->created_time < $b->created_time ? 1 : -1;
        });

        $syncFile = $this->path . DS . 'sync.' . $id . '.'
            . time() . '.php';

        file_put_contents($syncFile, serialize($contents));
    }

    /**
     * Removes all compiles.
     */
    public function cleanCompiles()
    {
        $files = glob($this->path . DS . 'sync.*.*.php');

        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * Removes compiles for contents from server.
     *
     * @param integer $id The server id.
     */
    public function cleanCompileForServer($id)
    {
        // Remove all compiles for this server
        $compiles = glob($this->path . DS . 'sync.' . $id . '*.php');

        foreach ($compiles as $compile) {
            unlink($compile);
        }
    }

    /**
     * Removes source files from server.
     *
     * @param integer $id The server id.
     */
    public function cleanSourceFilesForServer($id)
    {
        // Remove files from server
        $directory = realpath($this->path . DS . $id);

        if (!empty($directory)) {
            \Onm\FilesManager::deleteDirectoryRecursively($directory);
        }
    }

    /**
     * Get elements from the compiled file.
     *
     * @return array The unserialized resources.
     */
    public function getContentsFromCompiles()
    {
        $files = glob($this->path . DS . 'sync.*.*.php');

        $contents = [];
        foreach ($files as $file) {
            $c = @unserialize(file_get_contents($file));

            if (is_array($c) && !empty($c)) {
                $contents = array_merge($contents, $c);
            }
        }

        return $contents;
    }
}
