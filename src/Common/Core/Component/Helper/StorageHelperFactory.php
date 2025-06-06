<?php

namespace Common\Core\Component\Helper;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class StorageHelperFactory
{
    private $logApp;
    private $logError;

    public function __construct($logApp, $logError)
    {
        $this->logApp   = $logApp;
        $this->logError = $logError;
    }

    public function create(array $config): StorageHelper
    {
        $client = new S3Client([
            'credentials'             => [
                'key'    => $config['key'],
                'secret' => $config['secret'],
            ],
            'region'                  => $config['region'],
            'version'                 => 'latest',
            'endpoint'                => $config['endpoint'],
            'use_path_style_endpoint' => $config['path_style'] ?? false,
        ]);

        $adapter    = new AwsS3Adapter($client, $config['bucket']);
        $filesystem = new Filesystem($adapter);

        return new StorageHelper($filesystem, $this->logApp, $this->logError);
    }
}
