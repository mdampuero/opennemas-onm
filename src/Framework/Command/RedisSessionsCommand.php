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
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('core:redis:sessions')
            ->setDescription('List, filter and delete Redis sessions using redis-cli output.')
            ->addOption(
                'pattern',
                'P',
                InputOption::VALUE_REQUIRED,
                'Pattern used while scanning for session keys.',
                '*'
            )
            ->addOption(
                'emails',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Email or comma separated list of emails to match inside the session payload.'
            )
            ->addOption(
                'instance',
                'I',
                InputOption::VALUE_OPTIONAL,
                'Instance internal name used to load its users and match their sessions.'
            )
            ->addOption(
                'delete',
                null,
                InputOption::VALUE_NONE,
                'Delete the sessions that match the filters instead of only listing them.'
            )
            ->addOption(
                'limit',
                'L',
                InputOption::VALUE_OPTIONAL,
                'Maximum number of session keys inspected from the SCAN output (0 for no limit).',
                0
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->steps = [7 + ($input->getOption('delete') ? 1 : 0)];
        $this->step  = [1, 1, 1];
    }

    /**
     * {@inheritdoc}
     */
    protected function do()
    {
        $pattern  = $this->input->getOption('pattern');
        $limit    = (int) $this->input->getOption('limit');
        $delete   = (bool) $this->input->getOption('delete');
        $instance = $this->input->getOption('instance');

        $this->writeStep('Resolving redis connection');
        $connection = $this->resolveRedisConnection();
        $host       = $connection['host'];
        $port       = $connection['port'];
        $database   = $connection['database'];

        $portInfo = $port === null ? '' : sprintf(', port: %d', $port);
        $this->writeStatus('info', sprintf(' (host: %s, database: %d%s)', $host, $database, $portInfo));
        $this->writeStatus('success', ' DONE', true);

        $this->writeStep('Collecting filters');
        $emails = $this->normaliseEmails((array) $this->input->getOption('emails'));
        $this->writeStatus('info', sprintf(' (%d emails from option)', count($emails)));

        if (!empty($instance)) {
            $instanceEmails = $this->getInstanceEmails($instance);
            $emails         = array_values(array_unique(array_merge($emails, $instanceEmails)));
            $this->writeStatus('info', sprintf(' (%d emails after loading instance %s)', count($emails), $instance));
        }

        $this->writeStatus('success', ' DONE', true);

        $this->writeStep('Scanning redis for sessions');
        $sessionIds = $this->scanSessions($host, $port, $database, $pattern, $limit);
        $this->writeStatus('info', sprintf(' (%d keys)', count($sessionIds)));
        $this->writeStatus('success', ' DONE', true);

        $matched = [];
        $this->writeStep('Inspecting session payloads');
        foreach ($sessionIds as $sessionId) {
            $payload = $this->getSessionPayload($host, $port, $database, $sessionId);

            if (empty($emails) || $this->payloadMatchesEmails($payload, $emails)) {
                $matched[$sessionId] = [
                    'payload' => $payload,
                    'emails'  => $this->emailsFoundInPayload($payload, $emails),
                ];
            }
        }
        $this->writeStatus('info', sprintf(' (%d matches)', count($matched)));
        $this->writeStatus('success', ' DONE', true);

        if (empty($matched)) {
            $this->writeStep('No sessions matched the provided filters', true);
            return;
        }

        $this->writeStep('Matched sessions', true);
        foreach ($matched as $sessionId => $data) {
            $emailsFound = empty($data['emails']) ? 'N/A' : implode(', ', $data['emails']);
            $excerpt     = $this->truncatePayload($data['payload']);

            $this->output->writeln(sprintf(
                '<info>%s</info> | emails: [%s] | %s',
                $sessionId,
                $emailsFound,
                $excerpt
            ));
        }

        if ($delete) {
            $this->writeStep('Deleting matched sessions');
            $deleted = 0;
            foreach (array_keys($matched) as $sessionId) {
                if ($this->deleteSession($host, $port, $database, $sessionId)) {
                    $deleted++;
                }
            }
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
     * Normalises the emails provided via the CLI option.
     */
    private function normaliseEmails(array $raw) : array
    {
        $emails = [];
        foreach ($raw as $chunk) {
            if (!is_string($chunk)) {
                continue;
            }

            $parts = preg_split('/[,;\s]+/', $chunk, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($parts as $email) {
                $email = trim($email);
                if (!empty($email)) {
                    $emails[] = strtolower($email);
                }
            }
        }

        return array_values(array_unique($emails));
    }

    /**
     * Retrieves all emails that belong to the given instance.
     */
    private function getInstanceEmails(string $instance) : array
    {
        $loader = $this->getContainer()->get('core.loader');
        $loader->load($instance)->onlyEnabled();

        $connection = $this->getContainer()->get('dbal_connection');
        $emails     = [];

        try {
            $rows = $connection->fetchAll('SELECT email FROM users WHERE email IS NOT NULL AND email != ""');
        } catch (\Exception $exception) {
            throw new \RuntimeException(sprintf(
                'Unable to fetch users for instance "%s": %s',
                $instance,
                $exception->getMessage()
            ));
        }

        foreach ($rows as $row) {
            if (empty($row['email'])) {
                continue;
            }

            $emails[] = strtolower(trim($row['email']));
        }

        return array_values(array_unique($emails));
    }

    /**
     * Uses redis-cli to retrieve the list of session ids.
     */
    private function scanSessions(string $host, ?int $port, int $database, string $pattern, int $limit) : array
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

        $sessions = array_values(array_filter(array_map('trim', explode("\n", $process->getOutput()))));

        if ($limit > 0) {
            $sessions = array_slice($sessions, 0, $limit);
        }

        return $sessions;
    }

    /**
     * Returns the payload stored in redis for a given session id.
     */
    private function getSessionPayload(string $host, ?int $port, int $database, string $sessionId) : string
    {
        $command = ['redis-cli', '-h', $host];

        if ($port !== null) {
            $command[] = '-p';
            $command[] = (string) $port;
        }

        $command[] = '-n';
        $command[] = (string) $database;
        $command[] = 'get';
        $command[] = $sessionId;

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf(
                'Unable to fetch session %s: %s',
                $sessionId,
                trim($process->getErrorOutput()) ?: $process->getOutput()
            ));
        }

        return rtrim($process->getOutput(), "\r\n");
    }

    /**
     * Deletes a session using redis-cli.
     */
    private function deleteSession(string $host, ?int $port, int $database, string $sessionId) : bool
    {
        $command = ['redis-cli', '-h', $host];

        if ($port !== null) {
            $command[] = '-p';
            $command[] = (string) $port;
        }

        $command[] = '-n';
        $command[] = (string) $database;
        $command[] = 'del';
        $command[] = $sessionId;

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->output->writeln(sprintf(
                '<error>Failed to delete %s: %s</error>',
                $sessionId,
                trim($process->getErrorOutput()) ?: $process->getOutput()
            ));

            return false;
        }

        return (int) trim($process->getOutput()) === 1;
    }

    /**
     * Checks whether a payload contains any of the provided emails.
     */
    private function payloadMatchesEmails(string $payload, array $emails) : bool
    {
        if (empty($emails)) {
            return true;
        }

        $haystack = strtolower($payload);
        foreach ($emails as $email) {
            if (strpos($haystack, $email) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the list of emails found in the payload.
     */
    private function emailsFoundInPayload(string $payload, array $emails) : array
    {
        $found = [];
        $haystack = strtolower($payload);

        foreach ($emails as $email) {
            if (strpos($haystack, $email) !== false) {
                $found[] = $email;
            }
        }

        return $found;
    }

    /**
     * Creates a single-line representation of the payload content.
     */
    private function truncatePayload(string $payload, int $length = 120) : string
    {
        $singleLine = preg_replace('/\s+/', ' ', trim($payload));
        if (mb_strlen($singleLine) <= $length) {
            return $singleLine;
        }

        return mb_substr($singleLine, 0, $length - 3) . '...';
    }
}
