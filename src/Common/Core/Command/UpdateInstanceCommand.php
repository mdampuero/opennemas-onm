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

class UpdateInstanceCommand extends Command
{
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
                'instances',
                'i',
                InputOption::VALUE_REQUIRED,
                'The list of instances to update (e.g. norf, quux)'
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
        $this->getContainer()->get('core.security')->setCliUser();

        $this->start();
        $output->writeln(sprintf(
            '<options=bold>'
                . str_pad('(1/3) Starting command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        list($instances, $media, $stats, $views) = $this->getParameters($input);

        $output->writeln(sprintf(
            '<options=bold>'
                . str_pad('(2/3) Processing instances', 50, '.')
                . '<fg=yellow;options=bold>IN PROGRESS</>'
                . ' <fg=blue;options=bold>(%s instances)</></>',
            count($instances)
        ));

        $helper = $this->getContainer()->get('core.helper.instance');
        $i      = 1;

        foreach ($instances as $instance) {
            $output->writeln(str_pad(
                sprintf(
                    '<fg=yellow;options=bold>====></><options=bold> (%d/%d) Updating instance %s</>',
                    $i++,
                    count($instances),
                    $instance->internal_name
                ),
                50,
                '.'
            ));

            try {
                if (!empty($stats)) {
                    $output->write(str_pad('- Checking creation date', 50, '.'));
                    $instance->created = $helper->getCreated($instance);
                    $output->writeln('<fg=green;options=bold>DONE</>');

                    $output->write(str_pad('- Checking last activity', 50, '.'));
                    $instance->last_login = $helper->getLastActivity($instance);
                    $output->writeln('<fg=green;options=bold>DONE</>');

                    $output->write(str_pad('- Counting active users', 50, '.'));
                    $instance->users = $helper->countUsers($instance);
                    $output->writeln('<fg=green;options=bold>DONE</>');

                    $output->write(str_pad('- Counting contents', 50, '.'));
                    $contents = $helper->countContents($instance);

                    foreach ($contents as $type => $total) {
                        $instance->{$type . 's'} = $total;
                    }

                    $instance->contents = array_sum($contents);
                    $output->writeln('<fg=green;options=bold>DONE</>');
                }

                if (!empty($views)) {
                    $output->write(str_pad('- Requesting page views', 50, '.'));
                    $instance->page_views = $helper->getPageViews($instance);
                    $output->writeln('<fg=green;options=bold>DONE</>');
                }

                if (!empty($media)) {
                    $output->write(str_pad('- Calculating media folder size', 50, '.'));
                    $instance->media_size = $helper->getMediaSize($instance);
                    $output->writeln('<fg=green;options=bold>DONE</>');
                }
            } catch (\Exception $e) {
                $output->writeln('<fg=red;options=bold>FAIL</>');
            }

            try {
                $output->write(str_pad('- Saving instance', 50, '.'));
                $this->getContainer()->get('orm.manager')->persist($instance);
                $output->writeln('<fg=green;options=bold>DONE</>');
            } catch (Exception $e) {
                $output->writeln('<fg=red;options=bold>FAIL</>');
            }
        }

        $this->end();
        $output->writeln(sprintf(
            '<options=bold>'
                . str_pad('(3/3) Ending command', 50, '.')
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
        $oql = 'order by id asc';

        if (!empty($names)) {
            $oql = sprintf('internal_name in ["%s"] ', implode('","', $names));
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
        $instances = $input->getOption('instances');

        if (!empty($instances)) {
            $instances = preg_split('/\s*,\s*/', $instances);
        }

        $instances = $this->getInstances($instances);
        $media     = $input->getOption('media');
        $stats     = $input->getOption('stats');
        $views     = $input->getOption('views');

        if (empty($media) && empty($stats) && empty($views)) {
            throw new \InvalidArgumentException('No option specified');
        }

        return [ $instances, $media, $stats, $views ];
    }
}
