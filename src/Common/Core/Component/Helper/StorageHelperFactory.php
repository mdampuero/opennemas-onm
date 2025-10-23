<?php

namespace Common\Core\Component\Helper;

use Aws\S3\S3Client;
use Common\Core\Component\Helper\BunnyStorageHelper;
use Common\Core\Service\Bunny\BunnyStreamService;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class StorageHelperFactory
{
    private $logApp;
    private $logError;
    private $s3Client;
    private $providerType = 's3';
    private $bunnyService;
    protected $container;
    protected $config;

    public function __construct($container, $logApp, $logError)
    {
        $this->container = $container;
        $this->logApp    = $logApp;
        $this->logError  = $logError;
    }

    public function create($instance = null)
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
        $provider     = $config['provider'] ?? [];
        $this->providerType = $provider['type'] ?? 's3';

        if ($this->providerType === 'bunny') {
            $service = clone $this->container->get('common.core.bunny_stream.service');
            if ($service instanceof BunnyStreamService) {
                $service->configure([
                    'api_base_url'   => $provider['api_base_url'] ?? null,
                    'embed_base_url' => $provider['embed_base_url'] ?? null,
                    'library_id'     => $provider['library_id'] ?? null,
                    'api_key'        => $provider['api_key'] ?? null,
                ]);
            }
            $this->bunnyService = $service;
            $this->s3Client     = null;

            return new BunnyStorageHelper($service, $this->logApp, $this->logError);
        }

        $this->s3Client = new S3Client([
            'credentials'             => [
                'key'    => $provider['key'] ?? null,
                'secret' => $provider['secret'] ?? null,
            ],
            'region'                  => $provider['region'] ?? null,
            'version'                 => 'latest',
            'endpoint'                => $provider['endpoint'] ?? null,
            'use_path_style_endpoint' => $provider['path_style'] ?? false,
        ]);

        $adapter    = new AwsS3Adapter($this->s3Client, $provider['bucket'] ?? '');
        $filesystem = new Filesystem($adapter);

        return new StorageHelper($filesystem, $this->logApp, $this->logError);
    }


    public function getS3Client(): ?S3Client
    {
        return $this->s3Client;
    }

    public function getBunnyService(): ?BunnyStreamService
    {
        return $this->bunnyService;
    }

    /**
     * Get the value of config
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function getProviderType(): string
    {
        return $this->providerType;
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
