<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Cache\Command;

use Common\Cache\Core\Cache;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RedisCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('cache:redis')
            ->setDescription('Removes entries from cache')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'The redis action to execute (exists, get, remove)'
            )->addOption(
                'key',
                false,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'The key for exists, get, remove and set actions'
            )->addOption(
                'pattern',
                false,
                InputOption::VALUE_REQUIRED,
                'A redis-prepared pattern for remove action'
            )->addOption(
                'manager',
                false,
                InputOption::VALUE_NONE,
                'Whether to use the manager-configured cache connection'
            )->addOption(
                'namespace',
                false,
                InputOption::VALUE_REQUIRED,
                'The cache namespace'
            )->addOption(
                'old',
                false,
                InputOption::VALUE_NONE,
                'Whether to use the old cache connection'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $name   = empty($input->getOption('manager')) ?
            'instance' : 'manager';

        if (!in_array($action, [ 'exists', 'get', 'remove' ])) {
            throw new \InvalidArgumentException("Invalid action '$action'");
        }

        $this->input = $input;
        $this->cache = $this->getCache($name);

        $check   = "check{$action}";
        $execute = "execute{$action}";

        $output->write("<options=bold>Checking arguments for $action action...</>");
        $this->checkNamespace();
        $this->{$check}();
        $output->writeln("<info>DONE</>");

        $output->writeln("<options=bold>Executing $action action...</>");
        $response = $this->{$execute}();

        if (!empty($response)) {
            $table = new Table($output);
            $table->setHeaders(array_keys($response[0]));
            $table->setRows($response);
            $table->render();
        }

        $output->writeln("<fg=green;options=bold>Action completed!</>");
    }

    /**
     * Checks if the action 'exists' have all needed arguments.
     *
     * @throws InvalidArgumentException If any parameter is missing.
     */
    protected function checkExists()
    {
        if (empty($this->input->getOption('key'))) {
            throw new \InvalidArgumentException(
                "Missing argument 'key' for 'exists' action"
            );
        }
    }

    /**
     * Checks if the action 'get' have all needed arguments.
     *
     * @throws InvalidArgumentException If any parameter is missing.
     */
    protected function checkGet()
    {
        if (empty($this->input->getOption('key'))) {
            throw new \InvalidArgumentException(
                "Missing argument 'key' for 'get' action"
            );
        }
    }

    /**
     * Checks if the namespace option is provided.
     *
     * @throws InvalidArgumentException If the namespace option is not provided.
     */
    protected function checkNamespace()
    {
        if (($this->input->getArgument('action') !== 'remove' ||
            empty($this->input->getOption('pattern')))
            && empty($this->input->getOption('namespace'))
        ) {
            throw new \InvalidArgumentException("Missing argument 'namespace'");
        }
    }

    /**
     * Checks if the action 'remove' have all needed arguments.
     *
     * @throws InvalidArgumentException If any parameter is missing.
     */
    protected function checkRemove()
    {
        if (empty($this->input->getOption('key'))
            && empty($this->input->getOption('pattern'))
        ) {
            throw new \InvalidArgumentException(
                "Missing arguments 'key' and 'pattern' for 'remove' action"
            );
        }
    }

    /**
     * Returns the cache connection to use basing on the input options.
     *
     * @param string $name The cache connection name.
     *
     * @return mixed The cache connection.
     */
    protected function getCache($name)
    {
        if ($this->input->getOption('old')) {
            return $this->getContainer()->get('cache');
        }

        return $this->getContainer()->get('cache.manager')->getConnection($name);
    }

    /**
     * Executes a exists action.
     *
     * @return boolean True if the key exists. False otherwise.
     */
    protected function executeExists()
    {
        $keys     = $this->input->getOption('key');
        $response = [];

        if (!is_array($keys)) {
            $keys = [ $keys ];
        }

        $this->cache->setNamespace($this->input->getOption('namespace'));

        foreach ($keys as $key) {
            $result = $this->cache instanceof Cache ?
                $this->cache->exists($key) : $this->cache->contains($key);

            $response[] = [ 'key' => $key, 'value' => $result ];
        }

        return $response;
    }

    /**
     * Executes a get action.
     *
     * @return array The list of keys and values from cache.
     */
    protected function executeGet()
    {
        $keys     = $this->input->getOption('key');
        $response = [];

        if (!is_array($keys)) {
            $keys = [ $keys ];
        }

        $this->cache->setNamespace($this->input->getOption('namespace'));

        foreach ($keys as $key) {
            $result = $this->cache instanceof Cache ?
                $this->cache->get($key) : $this->cache->fetch($key);

            $result = is_object($result) || is_array($result) ?
                json_encode($result) : $result;

            $response[] = [ 'key' => $key, 'value' => $result ];
        }

        return $response;
    }

    /**
     * Executes a remove action.
     *
     * @return mixed The result of the remove action.
     */
    protected function executeRemove()
    {
        if ($this->input->getOption('pattern')) {
            return $this->removeByPattern();
        }

        return $this->removeByKeys();
    }

    /**
     * Remove a list of keys from cache.
     *
     * @return array The result of the remove action for each provided key.
     */
    protected function removeByKeys()
    {
        $keys     = $this->input->getOption('key');
        $response = [];

        if (!is_array($keys)) {
            $keys = [ $keys ];
        }

        $this->cache->setNamespace($this->input->getOption('namespace'));

        foreach ($keys as $key) {
            $result = $this->cache instanceof Cache ?
                $this->cache->remove($key) : $this->cache->delete($key);

            $response[] = [ 'key' => $key, 'value' => $result ];
        }

        return $response;
    }

    /**
     * Removes entries from cache that match a provided pattern.
     *
     * @return integer The result of the action.
     */
    protected function removeByPattern()
    {
        if (!($this->cache instanceof Cache)) {
            throw new \InvalidArgumentException(
                "This cache does not support the 'pattern' parameter"
            );
        }

        $pattern = $this->input->getOption('pattern');

        $this->cache->setNamespace($this->input->getOption('namespace'));

        return $this->cache->removeByPattern($pattern);
    }
}
