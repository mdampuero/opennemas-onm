<?php
/**
 * This file is part of the Onm package.
 *
 * (c) OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Framework\Command;

use Common\Core\Command\Command;
use Common\Core\Component\Session\RedisSessionKeyHelper;
use Common\Model\Entity\Instance;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

/**
 * Console command to inspect and manage Redis sessions using the redis-cli binary.
 */
class RedisSessionsCommand extends Command
{
    /**
     * @var Instance|null
     */
    private $loadedInstance;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('core:redis:sessions')
            ->setDescription('List and optionally delete Redis session keys using redis-cli output.')
            ->addOption(
                'pattern',
                'P',
                InputOption::VALUE_REQUIRED,
                'Pattern used while scanning for session keys.',
                '*'
            )
            ->addOption(
                'instance',
                'I',
                InputOption::VALUE_OPTIONAL,
                'Instance internal name used to match session keys.'
            )
            ->addOption(
                'delete',
                null,
                InputOption::VALUE_NONE,
                'Delete the sessions that match the filters instead of only listing them.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $totalSteps = 5 + ($input->getOption('delete') ? 1 : 0);

        $this->steps = [$totalSteps];
        $this->step  = [1, 1, 1];
    }

    /**
     * {@inheritdoc}
     */
    protected function do()
    {
        $this->loadedInstance = null;

        $pattern  = $this->input->getOption('pattern');
        $delete   = (bool) $this->input->getOption('delete');
        $instance = $this->normaliseInstanceName($this->input->getOption('instance'));

        $connection = $this->resolveRedisConnection();
        $host       = $connection['host'];
        $port       = $connection['port'];
        $database   = $connection['database'];
        $portInfo   = $port === null ? '' : sprintf(', port: %d', $port);

        $this->writeStatus(
            'info',
            sprintf('Redis connection (host: %s, database: %d%s)', $host, $database, $portInfo),
            true
        );

        $this->writeStep('Preparing key pattern');
        $pattern = $this->normalisePatternForInstance($pattern, $instance);
        $this->writeStatus('info', sprintf(' (%s)', $pattern));
        $this->writeStatus('success', ' DONE', true);

        $this->writeStep('Scanning redis for session keys');
        $sessionIds = $this->scanSessions($host, $port, $database, $pattern);
        $this->writeStatus('info', sprintf(' (%d keys)', count($sessionIds)));
        $this->writeStatus('success', ' DONE', true);

        if (empty($sessionIds)) {
            $this->writeStep('No sessions matched the provided filters', true);
            return;
        }

        $this->writeStep('Matched sessions', true);
        foreach ($sessionIds as $sessionId) {
            $this->output->writeln(sprintf('<info>%s</info>', $sessionId));
        }

        if ($delete) {
            $this->writeStep('Deleting matched sessions');
            $deleted = $this->deleteSessions($host, $port, $database, $sessionIds);
            $this->writeStatus('info', sprintf(' (%d removed)', $deleted));
            $this->writeStatus('success', ' DONE', true);
        }
    }

    /**
     * Resolves the redis host, port and database from the connections configuration file.
     */
    private function resolveRedisConnection() : array
    {
        $path = APPLICATION_PATH . '/app/config/connections.yml';

        if (!is_file($path) || !is_readable($path)) {
            throw new \RuntimeException(sprintf('Unable to read redis configuration from %s', $path));
        }

        $parsed = Yaml::parse(file_get_contents($path));
        if (!is_array($parsed)) {
            throw new \RuntimeException('The connections.yml file does not contain valid YAML data.');
        }

        $savePath = $this->extractSessionSavePath($parsed);
        if (!is_string($savePath) || trim($savePath) === '') {
            throw new \RuntimeException('The session_handler_savepath entry is missing or empty in connections.yml.');
        }

        return $this->parseRedisSavePath($savePath);
    }

    /**
     * Extracts the session handler save path from the parsed YAML contents.
     *
     * @param array $config
     *
     * @return string|null
     */
    private function extractSessionSavePath(array $config) : ?string
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

    /**
     * Parses the redis save path into host, port and database components.
     */
    private function parseRedisSavePath(string $savePath) : array
    {
        $savePath = trim($savePath);

        if ($savePath === '') {
            throw new \RuntimeException('The redis session save path is empty.');
        }

        if (strpos($savePath, '://') === false) {
            $savePath = 'tcp://' . ltrim($savePath, '/');
        }

        $parts = parse_url($savePath);
        if ($parts === false || !isset($parts['host'])) {
            throw new \RuntimeException(sprintf('Unable to parse redis session save path "%s".', $savePath));
        }

        $query = [];
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $database = 0;
        foreach (['database', 'db'] as $key) {
            if (isset($query[$key])) {
                $database = (int) $query[$key];
                break;
            }
        }

        if (isset($parts['path']) && trim($parts['path'], '/') !== '') {
            $database = (int) trim($parts['path'], '/');
        }

        return [
            'host'     => $parts['host'],
            'port'     => isset($parts['port']) ? (int) $parts['port'] : null,
            'database' => $database,
        ];
    }

    /**
     * Normalises the provided instance option so only valid internal names are used.
     */
    private function normaliseInstanceName($raw) : ?string
    {
        if (!is_string($raw)) {
            return null;
        }

        $name = trim($raw);

        return $name === '' ? null : $name;
    }

    /**
     * Normalises the provided pattern so it only targets the loaded instance.
     */
    private function normalisePatternForInstance(string $pattern, ?string $instance) : string
    {
        if ($instance === null) {
            return $pattern;
        }

        $entity = $this->loadedInstance instanceof Instance
            ? $this->loadedInstance
            : $this->loadInstanceByName($instance);

        if (!$entity instanceof Instance) {
            return $pattern;
        }

        $this->loadedInstance = $entity;

        $identifier = RedisSessionKeyHelper::extractInstanceIdentifier($entity);
        $prefix     = RedisSessionKeyHelper::buildInstancePrefix($identifier);

        return RedisSessionKeyHelper::normalisePatternWithPrefix($pattern, $prefix);
    }

    private function loadInstanceByName(string $name) : ?Instance
    {
        $instanceLoader = $this->getContainer()->get('core.loader.instance');

        try {
            $instanceLoader->loadInstanceByName($name);
        } catch (\Exception $exception) {
            return null;
        }

        return $instanceLoader->getInstance();
    }

    /**
     * Uses redis-cli to retrieve the list of session ids.
     */
    private function scanSessions(string $host, ?int $port, int $database, string $pattern) : array
    {
        $command = ['redis-cli', '-h', $host];

        if ($port !== null) {
            $command[] = '-p';
            $command[] = (string) $port;
        }

        $command[] = '-n';
        $command[] = (string) $database;
        $command[] = '--scan';
        $command[] = '--pattern';
        $command[] = $pattern;

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(trim($process->getErrorOutput()) ?: $process->getOutput());
        }

        return array_values(array_filter(array_map('trim', explode("\n", $process->getOutput()))));
    }

    /**
     * Deletes sessions using redis-cli.
     */
    private function deleteSessions(string $host, ?int $port, int $database, array $sessionIds) : int
    {
        if (empty($sessionIds)) {
            return 0;
        }

        $command = ['redis-cli', '-h', $host];

        if ($port !== null) {
            $command[] = '-p';
            $command[] = (string) $port;
        }

        $command[] = '-n';
        $command[] = (string) $database;
        $command[] = 'del';
        foreach ($sessionIds as $sessionId) {
            $command[] = $sessionId;
        }

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(trim($process->getErrorOutput()) ?: $process->getOutput());
        }

        return (int) trim($process->getOutput());
    }
}
