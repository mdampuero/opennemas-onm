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
/**
 * Files Manager
 *
 * @package    Onm_Utils
 **/
class FilesManager
{
    /**
     * Create the directories for each content, if they don't exists
     *
     * @return void
     */
    public function createAllDirectories()
    {
        $directoryDate = date("Y/m/d/");

        // /images/, /files/, /ads/, /opinion/
        // /media/images/año/mes/dia/
        $mediaDirectories = array(
            MEDIA_IMG_DIR, MEDIA_FILE_DIR, MEDIA_ADS_DIR, MEDIA_OPINION_DIR
        );

        foreach ($mediaDirectories as $directory) {
            $path = MEDIA_PATH.$directory.'/'.$directoryDate ;

            self::createDirectory($path);
        }
    }

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
            $GLOBALS['application']->logger->emerg(
                "Error creating directory: " . $path
            );
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
     *
     * @return void
     */
    public function deleteDirectory($path)
    {
        if (file_exists($path)) {
            if (rmdir($path)) {
                $GLOBALS['application']->logger->emerg(
                    "Error deleting directory: " . $path
                );
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
                    FilesManager::deleteDirectoryRecursively($path."/".$object);
                } else {
                    unlink($path."/".$object);
                }
            }
        }
        reset($objects);

        return rmdir($path);
    }

    /**
     * Creates an empty file in th given path
     *
     * @param string $filename Directory concat with filename
     *
     * @return boolean true if the file was created or already exists.
     */
    public static function mkFile($filename)
    {
        if (!is_file($filename)) {
            return self::writeInFile($filename, '');
        }

        return true;
    }

    /**
     * Write some content in a file
     *
     * @param string $file  the name of the file
     * @param string $content content to save in a file
     *
     * @return boolean true if the file was saved
     */
    public static function writeInFile($file, $content)
    {
        $bytesSaved = file_put_contents($file, $content);

        return ($bytesSaved !== false);
    }

    /**
     * Uncompress Zip archives and returns the list of files inside the archive
     *
     * @param string $filePath the
     *
     * @return string the list of files extracted
     */
    public static function decompressZIP($filePath)
    {
        $zip = new ZipArchive;

        // open archive
        if ($zip->open($filePath) !== true) {
            echo "Could not open archive";

            return;
        }

        $dataZIP = array();

        $numFiles = $zip->numFiles;
        for ($x=0; $x<$numFiles; $x++) {
            $file = $zip->statIndex($x);
            $dataZIP[$x] = $file['name'];
        }

        $uploaddir = APPLICATION_PATH .DS.'tmp'.DS.'instances'.DS.INSTANCE_UNIQUE_NAME.DS.'xml'.DS;

        if (!file_exists($uploaddir)) {
            mkdir($uploaddir, 0775);
        }

        $zip->extractTo($uploaddir);

        $zip->close();

        return $dataZIP;
    }

    /**
     * Compress archives in a Tgz
     *
     * @param string $compressFile the file compress
     * @param string $destination the target destionation
     *
     * @return boolean true if the file was compresed
     */
    public static function compressTgz($compressFile, $destination)
    {
        $command = "tar cpfz $compressFile $destination";

        exec($command, $output, $outputCode);

        if ($outputCode != 0) {
            return false;
        }

        return true;
    }

    /**
     * Decompress a tgz file into a destionation
     *
     * @param string $compressFile the original file to extract
     * @param string $destination the folder where extract files
     *
     * @return boolean true if the file was decompressed
     */
    public static function decompressTgz($compressFile, $destination)
    {
        $command = "tar xpfz $compressFile -C $destination";
        exec($command, $output, $return_var);

        if ($return_var != 0) {
            return false;
        }

        return true;
    }

     /**
     * Cleans special chars from a file name
     *
     * @param  string  $name the string to clean
     *
     * @return string the string cleaned
     **/
    public static function cleanFileName($name)
    {
        $name = html_entity_decode($name, ENT_COMPAT, 'UTF-8');
        $name = mb_strtolower($name, 'UTF-8');
        $name = mb_ereg_replace('[^a-z0-9\.\-]', '', $name);

        return $name;
    }
}
