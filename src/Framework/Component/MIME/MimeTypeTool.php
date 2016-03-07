<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\MIME;

class MimeTypeTool
{
    protected static $ext2Mime = [
        'txt'  => 'text/plain',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'php'  => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'swf'  => 'application/x-shockwave-flash',
        'flv'  => 'video/x-flv',
        // images
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpe'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp',
        'ico'  => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif'  => 'image/tiff',
        'svg'  => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip'  => 'application/zip',
        'rar'  => 'application/x-rar-compressed',
        'exe'  => 'application/x-msdownload',
        'msi'  => 'application/x-msdownload',
        'cab'  => 'application/vnd.ms-cab-compressed',
        // audio/video
        'mp3'  => 'audio/mpeg',
        'qt'   => 'video/quicktime',
        'mov'  => 'video/quicktime',
        // adobe
        'pdf'  => 'application/pdf',
        'psd'  => 'image/vnd.adobe.photoshop',
        'ai'   => 'application/postscript',
        'eps'  => 'application/postscript',
        'ps'   => 'application/postscript',
        // ms office
        'doc'  => 'application/msword',
        'rtf'  => 'application/rtf',
        'xls'  => 'application/vnd.ms-excel',
        'ppt'  => 'application/vnd.ms-powerpoint',
        // open office
        'odt'  => 'application/vnd.oasis.opendocument.text',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
    ];

    /**
     * Returns the extension for a file.
     *
     * @param string $file The path to file.
     *
     * @return string The file extension.
     */
    public static function getExtension($file)
    {
        $mime = self::getMimeType($file);

        if (false !== ($ext = array_search($mime, self::$ext2Mime))) {
            return $ext;
        }

        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }

    /**
     * Returns the MIME type for a file.
     *
     * @param string $file The path to file.
     *
     * @return string The MIME type.
     */
    public static function getMimeType($file)
    {
        $mime = 'application/octet-stream';

        if (extension_loaded('fileinfo')
            && 'http://' !== substr($file, 0, 7)
            && 'https://' !== substr($file, 0, 8)
        ) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            if (false !== ($m = finfo_file($finfo, $file))) {
                $mime = $m;
            }

            finfo_close($finfo);

            return $mime;
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (array_key_exists($ext, self::$ext2Mime)) {
            $mime = self::$ext2Mime[$ext];
        }

        return $mime;
    }
}
