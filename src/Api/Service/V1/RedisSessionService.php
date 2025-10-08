<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Api\Service\V1;

use Api\Exception\DeleteItemException;
use Symfony\Component\Yaml\Yaml;

class RedisSessionService
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var object
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $connectionsPath;

    /**
     * @var callable|null
     */
    private $clientFactory;

    /**
     * @var object|null
     */
    private $redis;

    /**
     * @var array|null
     */
    private $connectionConfig;

    public function __construct($container, ?string $connectionsPath = null, ?callable $clientFactory = null)
    {
        $this->container       = $container;
        $this->dispatcher      = $container->get('core.dispatcher');
        $this->connectionsPath = $connectionsPath ?: APPLICATION_PATH . '/app/config/connections.yml';
        $this->clientFactory   = $clientFactory;
    }

    /**
     * Deletes redis sessions that match the provided pattern.
     */
    public function deleteByPattern(string $pattern): int
    {
        $pattern = trim($pattern);

        if ($pattern === '') {
            throw new DeleteItemException('Invalid redis pattern', 400);
        }

        try {
            $redis    = $this->getRedis();
            $iterator = null;
            $deleted  = 0;

            do {
                $keys = $redis->scan($iterator, $pattern, 1000);

                if ($keys === false) {
                    break;
                }

                if (!empty($keys)) {
                    $deleted += (int) $redis->del($keys);
                }
            } while ($iterator > 0);

            $this->dispatcher->dispatch($this->getEventName('deleteByPattern'), [
                'pattern' => $pattern,
                'deleted' => $deleted,
            ]);

            return $deleted;
        } catch (DeleteItemException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $code = (int) $exception->getCode();

            if ($code <= 0) {
                $code = 500;
            }

            throw new DeleteItemException($exception->getMessage(), $code);
        }
    }

    private function getRedis()
    {
        if ($this->redis) {
            return $this->redis;
        }

        $config = $this->resolveRedisConnection();

        if ($this->clientFactory) {
            $client = call_user_func($this->clientFactory, $config);
        } else {
            if (!class_exists('Redis')) {
                throw new DeleteItemException('The Redis extension is not installed', 500);
            }

            $client = new \Redis();
        }

        $timeout = $config['timeout'] ?? 0;
        $client->connect($config['host'], $config['port'], $timeout ?: 0.0);

        if (!empty($config['auth'])) {
            $client->auth($config['auth']);
        }

        if (array_key_exists('database', $config)) {
            $client->select((int) $config['database']);
        }

        $this->redis = $client;

        return $this->redis;
    }

    private function resolveRedisConnection(): array
    {
        if ($this->connectionConfig !== null) {
            return $this->connectionConfig;
        }

        if (!is_file($this->connectionsPath) || !is_readable($this->connectionsPath)) {
            throw new DeleteItemException(sprintf('Unable to read redis conf %s', $this->connectionsPath), 500);
        }

        $contents = file_get_contents($this->connectionsPath);
        $parsed   = Yaml::parse($contents);

        if (!is_array($parsed)) {
            throw new DeleteItemException('The connections.yml file does not contain valid YAML data.', 500);
        }

        $savePath = $this->extractSessionSavePath($parsed);

        if (!is_string($savePath) || trim($savePath) === '') {
            throw new DeleteItemException('The session_handler_savepath entry is missing.', 500);
        }

        $this->connectionConfig = $this->parseRedisSavePath($savePath);

        return $this->connectionConfig;
    }

    private function extractSessionSavePath(array $config): ?string
    {
        if (array_key_exists('session_handler_savepath', $config)) {
            return $config['session_handler_savepath'];
        }

        if (array_key_exists('parameters', $config)
            && is_array($config['parameters'])
            && array_key_exists('session_handler_savepath', $config['parameters'])
        ) {
            return $config['parameters']['session_handler_savepath'];
        }

        return null;
    }

    private function parseRedisSavePath(string $savePath): array
    {
        $savePath = trim($savePath);

        if ($savePath === '') {
            throw new DeleteItemException('The redis session save path is empty.', 500);
        }

        $endpoints = array_filter(array_map('trim', explode(',', $savePath)));

        if (empty($endpoints)) {
            throw new DeleteItemException('The redis session save path is malformed.', 500);
        }

        $endpoint = $endpoints[0];

        if (strpos($endpoint, '://') === false) {
            throw new DeleteItemException('The redis session save path is invalid.', 500);
        }

        $parts = parse_url($endpoint);

        if ($parts === false || !is_array($parts) || !array_key_exists('host', $parts)) {
            throw new DeleteItemException('Unable to parse redis session save path.', 500);
        }

        $config = [
            'host' => $parts['host'],
            'port' => $parts['port'] ?? 6379,
        ];

        $query = [];

        if (array_key_exists('query', $parts)) {
            parse_str($parts['query'], $query);
        }

        if (array_key_exists('database', $query)) {
            $config['database'] = (int) $query['database'];
        }

        if (array_key_exists('timeout', $query)) {
            $config['timeout'] = (float) $query['timeout'];
        }

        if (array_key_exists('auth', $query)) {
            $config['auth'] = (string) $query['auth'];
        } elseif (array_key_exists('password', $query)) {
            $config['auth'] = (string) $query['password'];
        }

        return $config;
    }

    private function getEventName(string $action): string
    {
        return 'redis_session.' . $action;
    }
}
