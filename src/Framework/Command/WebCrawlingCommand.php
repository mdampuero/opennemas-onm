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
use VDB\Spider\Spider;
use VDB\Spider\StatsHandler;

class WebCrawlingCommand extends Command
{
    /**
     * The array of predefined parameters.
     *
     * @var array
     */
    private $parameters;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->parameters = [
            'depth'     => 10,
            'limit'     => 1000,
            'instances' => [],
            'prod'      => false,
            'time'      => 3000
        ];

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

        list($depth, $limit, $instances, $prod, $time) = $this->getParameters($input);

        $this->overrideParameters($depth, $limit, $instances, $prod, $time);

        foreach ($instances as $instance) {
            $output->writeln(
                sprintf(
                    "\nExecute crawling domain: %s",
                    $instance->domains[0]
                )
            );

            $domain = $prod ?
                'http://' . $instance->domains[0] :
                'http://' . $instance->domains[0] . ':8080';
            $spider = new Spider($domain);
            $this->configureSpider($spider);
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
     * Configures spider based on the parameters passed as arguments.
     *
     * @param Spider        The spider to configure.
     *
     * @return StatsHandler The stats handler associated to the spider.
     */
    protected function configureSpider(Spider &$spider)
    {
        $spider->getDiscovererSet()->addFilter(new AllowedSchemeFilter([ 'http', 'https' ]));
        $spider->getDiscovererSet()->set(new XPathExpressionDiscoverer('//a'));
        $spider->getDiscovererSet()->maxDepth = $this->parameters['depth'];

        $spider->getQueueManager()->maxQueueSize = $this->parameters['limit'];

        $politenessPolicy = new PolitenessPolicyListener($this->parameters['time']);
        $spider->getDownloader()->getDispatcher()->addListener(
            SpiderEvents::SPIDER_CRAWL_PRE_REQUEST,
            [ $politenessPolicy, 'onCrawlPreRequest' ]
        );
    }

    /**
     * Overrides the predefined parameters of the crawling.
     *
     * @param array The array of parameters.
     */
    protected function overrideParameters($depth, $limit, $instances, $prod, $time)
    {
        $parameters = [
            'depth'     => $depth,
            'limit'     => $limit,
            'instances' => $instances,
            'time'      => $time,
            'prod'      => $prod
        ];

        foreach ($this->parameters as $key => $value) {
            $this->parameters[$key] = empty($parameters[$key]) ?
            $value :
            $parameters[$key];
        }
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
     * Returns the list of parameters for the command based on the input.
     *
     * @param InputInterface $input The input component.
     *
     * @return array The list of parameters.
     */
    protected function getParameters(InputInterface $input) : array
    {
        $depth     = $input->getOption('depth');
        $limit     = $input->getOption('limit');
        $instances = $input->getOption('instances');
        $prod      = $input->getOption('prod');
        $time      = $input->getOption('time');

        if (!empty($instances)) {
            $instances = preg_split('/\s*,\s*/', $instances);
        }

        $instances = $this->getInstances($instances);

        return [ $depth, $limit, $instances, $prod, $time ];
    }
}
