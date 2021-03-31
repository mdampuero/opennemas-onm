<?php

namespace Common\Core\Command;

use Common\Core\Command\Command;
use Common\Core\Component\VDB\Spider\CrawlLoggingListener;
use Common\Core\Component\VDB\Spider\LinkCheckRequestHandler;
use Common\Model\Entity\Instance;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;
use VDB\Spider\Discoverer\XPathExpressionDiscoverer;
use VDB\Spider\Event\SpiderEvents;
use VDB\Spider\EventListener\PolitenessPolicyListener;
use VDB\Spider\Filter\Prefetch\AllowedSchemeFilter;
use VDB\Spider\Filter\Prefetch\AllowedHostsFilter;
use VDB\Spider\QueueManager\InMemoryQueueManager;
use VDB\Spider\Spider;

class WebCrawlingCommand extends Command
{
    /**
     * The predefined depth.
     *
     * @var integer
     */
    const DEPTH = 3;

    /**
     * The predefined limit.
     *
     * @var integer
     */
    const LIMIT = 3000;

    /**
     * The predefined instances.
     *
     * @var array
     */
    const INSTANCES = [];

    /**
     * The predefined environment.
     *
     * @var boolean
     */
    const PORT = '';

    /**
     * The predefined time between requests.
     *
     * @var integer
     */
    const TIME = 3000;

    /**
     * The flag to check if is random or not.
     *
     * @var boolean
     */
    const RANDOM = false;

    /**
     * The max time in hours to perform the crawling.
     *
     * @var integer
     */
    const MAXTIME = 0;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('crawling:execute')
            ->setDescription('Executes a crawling')
            ->addOption(
                'depth',
                'd',
                InputOption::VALUE_OPTIONAL,
                'The depth of the crawling'
            )
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'The limit of items to follow'
            )
            ->addOption(
                'maxtime',
                'm',
                InputOption::VALUE_OPTIONAL,
                'The limit of items to follow'
            )
            ->addOption(
                'instances',
                'i',
                InputOption::VALUE_OPTIONAL,
                'The list of instances to execute crawling in'
            )
            ->addOption(
                'port',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The port to send the request to'
            )
            ->addOption(
                'time',
                't',
                InputOption::VALUE_OPTIONAL,
                'The time between requests in ms'
            )
            ->addOption(
                'random',
                'r',
                InputOption::VALUE_NONE,
                'If random flag is enabled, only performs crawling over a random instance'
            );
    }

    /**
     * Configures spider based on the parameters passed as arguments.
     *
     * @param array    The array of parameters.
     * @param Instance The instance to configure spider.
     *
     * @return Spider The configured spider ready to crawl.
     */
    protected function configureSpider(array $parameters, Instance $instance)
    {
        $protocol = in_array(
            'es.openhost.module.frontendSsl',
            $instance->activated_modules
        ) ? 'https' : 'http';

        $port   = empty($parameters['port']) ? $parameters['port'] : ':' . $parameters['port'];
        $domain = sprintf('%s://%s%s', $protocol, $instance->domains[0], $port);

        $queueManager = new InMemoryQueueManager();
        $queueManager->setTraversalAlgorithm(InMemoryQueueManager::ALGORITHM_BREADTH_FIRST);

        $spider = new Spider($domain);
        $spider->setQueueManager($queueManager);
        $spider->getDiscovererSet()->set(new XPathExpressionDiscoverer('//a'));
        $spider->getDiscovererSet()->addFilter(new AllowedSchemeFilter([ 'http', 'https' ]));
        $spider->getDiscovererSet()->addFilter(new AllowedHostsFilter([ $domain ], false));
        $spider->getDiscovererSet()->maxDepth = $parameters['depth'];

        $customRequestHandler = new LinkCheckRequestHandler();
        $customRequestHandler->setDomain($domain);

        $spider->getDownloader()->setRequestHandler($customRequestHandler);

        $spider->getQueueManager()->maxQueueSize = $parameters['limit'];

        $politenessPolicy = new PolitenessPolicyListener($parameters['time']);
        $spider->getDownloader()->getDispatcher()->addListener(
            SpiderEvents::SPIDER_CRAWL_PRE_REQUEST,
            [ $politenessPolicy, 'onCrawlPreRequest' ]
        );

        if ($this->output->isDebug()) {
            $loggingListener = new CrawlLoggingListener($spider, $this->output);
            $spider->getDownloader()->getDispatcher()->addListener(
                SpiderEvents::SPIDER_CRAWL_POST_REQUEST,
                [ $loggingListener, 'onCrawlPostRequest' ]
            );
        }

        $spider->getDispatcher()->addListener(
            SpiderEvents::SPIDER_CRAWL_USER_STOPPED,
            /**
             * @codeCoverageIgnore
             */
            function (Event $event) {
                $this->output->writeln("Crawling Stoped");
                exit();
            }
        );

        return $spider;
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->start();

        $this->output = $output;

        $this->output->writeln(sprintf(
            '<options=bold>'
            . str_pad('(1/3) Starting command', 200, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $parameters = $this->getParameters($input);

        $parameters['instances'] = $this->filterInstancesByParameters($parameters);

        if ($this->output->isVerbose() && !$this->output->isVeryVerbose()) {
            $this->output->writeln(sprintf(
                '<options=bold>'
                . str_pad('(2/3) Executing crawling', 200, '.')
                    . '<fg=yellow;options=bold>IN PROGRESS</>'
            ));
        }

        foreach ($parameters['instances'] as $instance) {
            if ($this->output->isVeryVerbose()) {
                $this->output->writeln(
                    '<options=bold>'
                    . str_pad(
                        sprintf('(2/3) Executing crawling domain %s', $instance->domains[0]),
                        200,
                        '.'
                    )
                    . '<fg=yellow;options=bold>IN PROGRESS</> '
                );
            }

            $spider = $this->configureSpider($parameters, $instance);
            $spider->crawl();

            if ($this->output->isVeryVerbose()) {
                $this->output->writeln(
                    '<options=bold>'
                    . str_pad(
                        sprintf('(2/3) Executing crawling domain %s', $instance->domains[0]),
                        200,
                        '.'
                    )
                    . '<fg=green;options=bold>DONE</> '
                );
            }
        }

        $this->output->writeln(sprintf(
            '<options=bold>'
            . str_pad('(2/3) Executing crawling', 200, '.')
            . '<fg=green;options=bold>DONE</>'
        ));

        $this->end();
        $this->output->writeln(sprintf(
            '<options=bold>'
            . str_pad('(3/3) Ending command', 200, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>'
                . ' <fg=yellow;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->ended),
            $this->getDuration()
        ));
    }

    /**
     * Returns an estimation of the number of instances that adjust max time.
     *
     * @param array $parameters The array of parameters.
     *
     * @return integer The number of instances that adjust to specified time.
     */
    protected function estimateInstancesByMaxTime(int $maxTime, int $timeByRequest, int $limit)
    {
        return floor($maxTime * 3600 / (($timeByRequest / 1000) * $limit));
    }

    /**
     * Returns the list of instances to crawl based on maxtime and random parameters.
     *
     * @param array $parameters The array of parameters.
     *
     * @return array The list of filtered instances.
     */
    protected function filterInstancesByParameters(array $parameters)
    {
        if ($parameters['random']) {
            return [ $this->randomizeInstance($parameters['instances']) ];
        }

        if ($parameters['maxtime'] != 0) {
            return $this->filterInstancesByTime($parameters);
        }

        return $parameters['instances'];
    }

    /**
     * Returns a list of random instances based on time to perform the crawl.
     *
     * @param array $parameters The array of parameters.
     *
     * @return array An array of random instances filtered by max time.
     */
    protected function filterInstancesByTime(array $parameters)
    {
        $instancesNumber = $this->estimateInstancesByMaxTime(
            $parameters['maxtime'],
            $parameters['time'],
            $parameters['limit']
        );

        if ($instancesNumber > count($parameters['instances'])) {
            return $parameters['instances'];
        }

        $instances = [];
        $domains   = [];

        while (count($instances) < $instancesNumber) {
            $instance = $this->randomizeInstance($parameters['instances']);
            $domain   = $instance->domains[0];

            if (in_array($instance->domains[0], $domains)) {
                continue;
            }

            $domains[]   = $domain;
            $instances[] = $instance;
        }

        return $instances;
    }

    /**
     * Returns the list of instances to synchronize.
     *
     * @param array $names The list of instance names
     *
     * @return array The list of instances.
     */
    protected function getInstances(?array $names = []) : array
    {
        $oql = 'activated = 1';

        if (!empty($names)) {
            $oql .= sprintf(
                ' and internal_name in ["%s"]',
                implode('","', $names)
            );
        }

        return $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->findBy($oql);
    }

    /**
     * Returns the parameter if is not empty, otherwise return the predefined value.
     *
     * @param InputInterface The input interface.
     * @param string         The name of the parametern to retrieve.
     *
     * @return mixed The parameter value.
     */
    protected function getNotEmptyParameter(InputInterface $input, string $name)
    {
        return !empty($input->getOption($name)) ?
            $input->getOption($name) :
            constant('self::' . strtoupper($name));
    }

    /**
     * Returns the list of parameters for the command based on the input.
     *
     * @param InputInterface $input The input component.
     *
     * @return array The list of parameters.
     */
    protected function getParameters(InputInterface $input) : array
    {
        $depth     = $this->getNotEmptyParameter($input, 'depth');
        $limit     = $this->getNotEmptyParameter($input, 'limit');
        $maxTime   = $this->getNotEmptyParameter($input, 'maxtime');
        $instances = $this->getNotEmptyParameter($input, 'instances');
        $port      = $this->getNotEmptyParameter($input, 'port');
        $time      = $this->getNotEmptyParameter($input, 'time');
        $random    = $this->getNotEmptyParameter($input, 'random');

        if (!empty($instances)) {
            $instances = preg_split('/\s*,\s*/', $instances);
        }

        $instances = $this->getInstances($instances);

        return [
            'depth'     => $depth,
            'limit'     => $limit,
            'maxtime'   => $maxTime,
            'instances' => $instances,
            'port'      => $port,
            'time'      => $time,
            'random'    => $random
        ];
    }

    /**
     * Returns a random instance from the array of instances.
     *
     * @param array The array of instances.
     *
     * @return Instance A random instance.
     */
    protected function randomizeInstance(array $instances)
    {
        $randomNumber = rand(0, count($instances) - 1);

        return $instances[$randomNumber];
    }
}
