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
    protected $container;
    protected $config;

    public function __construct($container, $logApp, $logError)
    {
        $this->container = $container;
        $this->logApp    = $logApp;
        $this->logError  = $logError;
    }

    public function create($instance = null): StorageHelper
    {
        if ($instance) {
            $config = $this->container->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('storage_settings', []);
        }

        if (empty($config)) {
            $config = $this->container->get('orm.manager')
                ->getDataSet('Settings', 'manager')
                ->get('storage_settings', []);
        }

        $this->config = $config;
        $this->s3Client = new S3Client([
            'credentials'             => [
                'key'    => $config['provider']['key'],
                'secret' => $config['provider']['secret'],
            ],
            'region'                  => $config['provider']['region'],
            'version'                 => 'latest',
            'endpoint'                => $config['provider']['endpoint'],
            'use_path_style_endpoint' => $config['provider']['path_style'] ?? false,
        ]);

        $adapter    = new AwsS3Adapter($this->s3Client, $config['provider']['bucket']);
        $filesystem = new Filesystem($adapter);

        return new StorageHelper($filesystem, $this->logApp, $this->logError);
    }


    public function getS3Client(): S3Client
    {
        return $this->s3Client;
    }

    /**
     * Get the value of config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the value of config
     *
     * @return  self
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }
}
