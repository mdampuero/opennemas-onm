<?php

namespace Framework\Command;

use Common\Core\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VDB\Spider\Discoverer\XPathExpressionDiscoverer;
use VDB\Spider\Event\SpiderEvents;
use VDB\Spider\EventListener\PolitenessPolicyListener;
use VDB\Spider\Filter\Prefetch\AllowedSchemeFilter;
use VDB\Spider\Filter\Prefetch\AllowedHostsFilter;
use VDB\Spider\Spider;
use VDB\Spider\StatsHandler;

class WebCrawlingCommand extends Command
{
    /**
     * The predefined depth.
     *
     * @var integer
     */
    const DEPTH = 10;

    /**
     * The predefined limit.
     *
     * @var integer
     */
    const LIMIT = 1000;

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
    const PROD = false;

    /**
     * The predefined time between requests.
     *
     * @var integer
     */
    const TIME = 3000;

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
                'instances',
                'i',
                InputOption::VALUE_OPTIONAL,
                'The list of instances to execute crawling in'
            )
            ->addOption(
                'prod',
                'p',
                InputOption::VALUE_NONE,
                'If the command is executed in prod or not'
            )
            ->addOption(
                'time',
                't',
                InputOption::VALUE_OPTIONAL,
                'The time between requests in ms'
            );
    }

    /**
     * Configures spider based on the parameters passed as arguments.
     *
     * @param string  The domain to execute the crawling.
     * @param array   The array of parameters.
     *
     * @return Spider The configured spider ready to crawl.
     */
    protected function configureSpider(array $parameters)
    {
        $domain = $parameters['prod'] ?
                'http://' . $instance->domains[0] :
                'http://' . $instance->domains[0] . ':8080';
        $spider = new Spider($domain);
        $spider->getDiscovererSet()->set(new XPathExpressionDiscoverer('//a'));
        $spider->getDiscovererSet()->addFilter(new AllowedSchemeFilter([ 'http', 'https' ]));
        $spider->getDiscovererSet()->addFilter(new AllowedHostsFilter([ $domain ], false));
        $spider->getDiscovererSet()->maxDepth = $parameters['depth'];

        $spider->getQueueManager()->maxQueueSize = $parameters['limit'];

        $politenessPolicy = new PolitenessPolicyListener($parameters['time']);
        $spider->getDownloader()->getDispatcher()->addListener(
            SpiderEvents::SPIDER_CRAWL_PRE_REQUEST,
            [ $politenessPolicy, 'onCrawlPreRequest' ]
        );

        return $spider;
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->start();

        $output->writeln(sprintf(
            str_pad('<options=bold>(1/3) Starting command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $parameters = $this->getParameters($input);

        foreach ($parameters['instances'] as $instance) {
            $output->writeln(
                sprintf(
                    "\nExecute crawling domain: %s",
                    $instance->domains[0]
                )
            );

            $spider = $this->configureSpider($parameters);
            $spider->crawl();
            $this->printReport($output, $spider);
        }

        $this->end();
        $output->writeln(sprintf(
            str_pad('<options=bold>(3/3) Ending command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>'
                . ' <fg=yellow;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->ended),
            $this->getDuration()
        ));
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
        $instances = $this->getNotEmptyParameter($input, 'instances');
        $prod      = $this->getNotEmptyParameter($input, 'prod');
        $time      = $this->getNotEmptyParameter($input, 'time');

        if (!empty($instances)) {
            $instances = preg_split('/\s*,\s*/', $instances);
        }

        $instances = $this->getInstances($instances);

        return [
            'depth'     => $depth,
            'limit'     => $limit,
            'instances' => $instances,
            'prod'      => $prod,
            'time'      => $time
        ];
    }

    /**
     * Prints a report with the results of the crawling.
     *
     * @param OutputInterface $output       The output interface to write on console.
     * @param Spider          $statsHandler The spider that performs the crawling.
     */
    protected function printReport(OutputInterface $output, Spider $spider)
    {
        $crawlResults = '';
        foreach ($spider->getDownloader()->getPersistenceHandler() as $resource) {
            $crawlResults .= sprintf(
                "URL: %s\nRESPONSE_STATUS: %s\n",
                $resource->getUri(),
                $resource->getResponse()->getStatusCode()
            );
        }

        $output->writeln($crawlResults);
    }

}
