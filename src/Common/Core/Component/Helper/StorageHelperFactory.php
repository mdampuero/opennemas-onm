<?php

namespace Common\Core\Component\Helper;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class StorageHelperFactory
{
    private $logApp;
    private $logError;
    private $s3Client;

    public function __construct($logApp, $logError)
    {
        $this->logApp   = $logApp;
        $this->logError = $logError;
    }

    public function create(array $config): StorageHelper
    {
        $this->s3Client = new S3Client([
            'credentials'             => [
                'key'    => $config['key'],
                'secret' => $config['secret'],
            ],
            'region'                  => $config['region'],
            'version'                 => 'latest',
            'endpoint'                => $config['endpoint'],
            'use_path_style_endpoint' => $config['path_style'] ?? false,
        ]);

        $adapter    = new AwsS3Adapter($this->s3Client, $config['bucket']);
        $filesystem = new Filesystem($adapter);

        return new StorageHelper($filesystem, $this->logApp, $this->logError);
    }

    public function getS3Client(): S3Client
    {
        return $this->s3Client;
    }
}
