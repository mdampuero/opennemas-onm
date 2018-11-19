<?php
/**
 * Defines the FilesManager class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Utils
 */
namespace Onm;

/**
 * Files Manager
 *
 * @package    Onm_Utils
 */
class FilesManager
{
    /**
     * Creates a new directory, if it don't exists
     *
     * @param string $path Directory to create
     *
     * @return boolean true if the directory was created
     */
    public static function createDirectory($path)
    {
        if (!is_dir($path)) {
            $created =  mkdir($path, 0775, true);
        } else {
            $created = true;
        }

        chmod($path, 0755);
        if (!$created) {
            $logger = getService('logger');
            $logger->notice("Error creating directory: " . $path);
        }

        return $created;
    }

    /**
     * Copies a folder and all its contents into a destination
     *
     * @param string $source      fullpath for the copy
     * @param string $destination path to the new destination
     *
     * @return boolean true if the copy was done
     */
    public static function recursiveCopy($source, $destination)
    {
        // Delete destination if exists
        if (file_exists($destination)) {
            self::deleteDirectoryRecursively($destination);
        }

        // if is dir try to recursive copy it, if is a file copy it directly
        if (is_dir($source)) {
            if (!file_exists($destination)) {
                if (!is_writable(dirname($destination))
                    || !mkdir($destination, 0775, true)
                ) {
                    return false;
                }
            }
            $files = scandir($source);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    self::recursiveCopy(
                        $source.DIRECTORY_SEPARATOR.$file,
                        $destination.DIRECTORY_SEPARATOR.$file
                    );
                }
            }
        } elseif (file_exists($source)) {
            return copy($source, $destination);
        };

        return true;
    }

    /**
     * Deletes a directory is it exists
     *
     * @param string $path the path to remove
     */
    public function deleteDirectory($path)
    {
        if (file_exists($path)) {
            if (rmdir($path)) {
                $logger = getService('logger');
                $logger->notice("Error deleting directory: " . $path);
            }
        }
    }

    /**
     * Deletes a directory and all its contents.
     *
     * @param string $path Directory to delete
     *
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public static function deleteDirectoryRecursively($path)
    {
        if (!is_dir($path)) {
            return false;
        }

        $objects = scandir($path);

        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($path."/".$object)) {
                    self::deleteDirectoryRecursively($path."/".$object);
                } else {
                    unlink($path."/".$object);
                }
            }
        }
        reset($objects);

        return rmdir($path);
    }
}
