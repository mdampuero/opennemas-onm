<?php
/**
 * Implements the DataSourceFactory class
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm\Import\DataSouce
 **/
namespace Onm\Import\DataSource;

/**
 * Class for initialize a FileImporter class handler
 *
 * @package Onm\Import\DataSouce
 **/
class DataSourceFactory
{
    /**
     * Returns an instance of the element to import
     *
     * @param string $filePath the file path to initialize the element
     *
     * @return FormatInterface
     **/
    public static function get($filePath)
    {
        $baseFormatClassPath = __DIR__.'/Format';
        $availableFormats = glob($baseFormatClassPath.'/*.php');

        $dataSource = null;
        foreach ($availableFormats as $value) {
            $formatName = basename($value, '.php');
            $formatClass = __NAMESPACE__."\Format\\".$formatName;
            try {
                $dataSource = new $formatClass($filePath);
                break;
            } catch (\Exception $e) {
                continue;
            }
        }

        return $dataSource;
    }
}
