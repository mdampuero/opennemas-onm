<?php
/**
 * File explanation
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Compress;

/**
 * class explanation
 *
 * @package Onm\Compress
 */
class Compress
{
    /**
     * Uncompress Zip archives and returns the list of files inside the archive
     *
     * @param string $filePath the
     *
     * @return null|array the list of files extracted
     */
    public static function decompressZIP($filePath)
    {
        $zip = new \ZipArchive;

        // open archive
        if ($zip->open($filePath) !== true) {
            $logger = getService('logger');
            $logger->notice("Could not open archive");

            return null;
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

        // Unused var $output
        unset($output);

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

        // Unused var $output
        unset($output);

        if ($return_var != 0) {
            return false;
        }

        return true;
    }

    /**
     * Compress archives in a Tgz
     *
     * @param string $compressFile the file compress
     * @param string $destination the target destionation
     *
     * @return boolean true if the file was compresed
     */
    public static function compressOnlyTar($compressFile, $destination)
    {
        $command = "tar cpf $compressFile $destination";

        exec($command, $output, $outputCode);

        // Unused var $output
        unset($output);

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
    public static function decompressOnlyTar($compressFile, $destination)
    {
        $command = "tar xpf $compressFile -C $destination";
        exec($command, $output, $return_var);

        // Unused var $output
        unset($output);

        if ($return_var != 0) {
            return false;
        }

        return true;
    }
}
