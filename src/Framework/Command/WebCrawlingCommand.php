<?php

namespace Framework\Command;

use Common\Core\Command\Command;
use Common\Core\Component\EventDispatcher\Event;
use Common\Model\Entity\Instance;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VDB\Spider\Discoverer\XPathExpressionDiscoverer;
use VDB\Spider\Event\SpiderEvents;
use VDB\Spider\EventListener\PolitenessPolicyListener;
use VDB\Spider\Filter\Prefetch\AllowedSchemeFilter;
use VDB\Spider\Filter\Prefetch\AllowedHostsFilter;
use VDB\Spider\Spider;

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
    const PORT = '';

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

        $spider = new Spider($domain);
        $spider->getDiscovererSet()->set(new XPathExpressionDiscoverer('//a'));
        $spider->getDiscovererSet()->addFilter(new AllowedSchemeFilter([ 'http', 'https' ]));
        $spider->getDiscovererSet()->addFilter(new AllowedHostsFilter([ $domain ], false));
        $spider->getDiscovererSet()->maxDepth = $parameters['depth'];

        $spider->getDownloader()->setRequestHandler(new LinkCheckRequestHandler());

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
            . str_pad('(1/3) Starting command', 100, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $parameters = $this->getParameters($input);

        if ($this->output->isVerbose() && !$this->output->isVeryVerbose()) {
            $this->output->writeln(sprintf(
                '<options=bold>'
                . str_pad('(2/3) Executing crawling', 100, '.')
                    . '<fg=yellow;options=bold>IN PROGRESS</>'
            ));
        }

        foreach ($parameters['instances'] as $instance) {
            if ($this->output->isVeryVerbose()) {
                $this->output->writeln(
                    '<options=bold>'
                    . str_pad(
                        sprintf('(2/3) Executing crawling domain %s', $instance->domains[0]),
                        100,
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
                        100,
                        '.'
                    )
                    . '<fg=green;options=bold>DONE</> '
                );
            }
        }

        $this->output->writeln(sprintf(
            '<options=bold>'
            . str_pad('(2/3) Executing crawling', 100, '.')
            . '<fg=green;options=bold>DONE</>'
        ));

        $this->end();
        $this->output->writeln(sprintf(
            '<options=bold>'
            . str_pad('(3/3) Ending command', 100, '.')
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
        $port      = $this->getNotEmptyParameter($input, 'port');
        $time      = $this->getNotEmptyParameter($input, 'time');

        if (!empty($instances)) {
            $instances = preg_split('/\s*,\s*/', $instances);
        }

        $instances = $this->getInstances($instances);

        return [
            'depth'     => $depth,
            'limit'     => $limit,
            'instances' => $instances,
            'port'      => $port,
            'time'      => $time
        ];
    }
}
