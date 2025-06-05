<?php

namespace Common\Core\Component\Helper;

use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Psr\Log\LoggerInterface;

class StorageHelperFactory
{
    public static function create(array $config, LoggerInterface $logApp, LoggerInterface $logError): StorageHelper
    {
        $client = new S3Client([
            'credentials'             => [
                'key'                 => $config['key'],
                'secret'              => $config['secret'],
            ],
            'region'                  => $config['region'],
            'version'                 => 'latest',
            'endpoint'                => $config['endpoint'],
            'use_path_style_endpoint' => $config['path_style'] ?? false,
        ]);

        $adapter    = new AwsS3Adapter($client, $config['bucket']);
        $filesystem = new Filesystem($adapter);

        return new StorageHelper($filesystem, $logApp, $logError);
    }
}
