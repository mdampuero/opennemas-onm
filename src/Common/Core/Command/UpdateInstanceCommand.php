<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateInstanceCommand extends ContainerAwareCommand
{
    /**
     * If true, debug messages will be shown during importing.
     *
     * @var boolean
     */
    protected $debug;

    /**
     * Array of database settings to use in migration process.
     *
     * @var array
     */
    protected $settings;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('core:instance:update')
            ->setDescription('Updates onm-instances database counters')
            ->setHelp(
                "Updates the counters in instances table in onm-instances by collecting data from different sources."
            )
            ->addOption(
                'media',
                'm',
                InputOption::VALUE_NONE,
                'If set, the command will gather the media sixe for the instance.'
            )
            ->addOption(
                'stats',
                's',
                InputOption::VALUE_NONE,
                'If set, the command will gather content, users, sent emails info.'
            )
            ->addOption(
                'views',
                'p',
                InputOption::VALUE_NONE,
                'If set, the command will gather the page views from Piwik.'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loader = $this->getContainer()->get('core.loader');
        $loader->init();

        $instances = $this->getInstances();

        if (empty($instances)) {
            $output->writeln('No instances');
            return 1;
        }

        $helper = $this->getContainer()->get('core.helper.instance');
        $stats  = [];

        foreach ($instances as $instance) {
            $stats[$instance->internal_name]['created']    = $helper->getCreated($instance);
            $stats[$instance->internal_name]['last_login'] = $helper->getLastActivity($instance);
            $stats[$instance->internal_name]['users']      = $helper->countUsers($instance);
            $stats[$instance->internal_name]['page_views'] = $helper->getPageViews($instance);

            $contents = $helper->countContents($instance);

            foreach ($contents as $type => $total) {
                $stats[$instance->internal_name][$type . 's'] = $total;
            }

            $stats[$instance->internal_name]['contents'] = array_sum($contents);

            if (array_key_exists($instance->internal_name, $sizes)) {
                $stats[$instance->internal_name]['media_size'] =
                    $sizes[$instance->internal_name];
            }
        }

        foreach ($instances as $instance) {
            foreach ($stats[$instance->internal_name] as $key => $value) {
                $instance->{$key} = $value;
            }

            $this->getContainer()->get('orm.manager')->persist($instance);
        }
    }

    /**
     * Returns the list of instances to process.
     *
     * @return array The list of instances to proccess.
     */
    protected function getInstances() : array
    {
        try {
            return $this->getContainer()->get('orm.manager')
                ->getRepository('Instance')
                ->findBy('order by id asc');
        } catch (\Exception $e) {
            return [];
        }
    }
}
