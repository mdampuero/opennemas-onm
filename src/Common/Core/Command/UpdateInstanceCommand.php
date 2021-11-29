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

use Common\Model\Entity\Instance;
use Symfony\Component\Console\Input\InputOption;

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
            );

        // Define 4 steps for level 1
        $this->steps[] = 4;
    }

    /**
     * Executes the command.
     */
    protected function do()
    {
        $this->writeStep('Checking parameters');
        list($ids, $media, $stats) = $this->getParameters($this->input);
        $this->writeStatus('success', 'DONE', true);

        // Define as many steps as instances for level 2
        $this->steps[] = count($ids);

        $this->writeStep('Processing instances');
        $this->writeStatus('warning', 'IN PROGRESS');
        $this->writeStatus('info', sprintf(' (%s instances)', count($ids)), true);

        foreach ($ids as $id) {
            try {
                $instance = $this->getContainer()->get('orm.manager')
                    ->getRepository('Instance')->find($id);
            } catch (\Exception $e) {
                $this->getContainer()->get('error.log')->error($e->getMessage());
                $this->writeStep('Updating instance', false, 2);
                $this->writeStatus('error', 'FAIL', true);
                continue;
            }

            $this->getContainer()->get('core.globals')->setInstance($instance);
            $this->writeStep("Updating instance $instance->internal_name", true, 2);

            foreach ([ 'stats', 'media' ] as $stage) {
                $method = 'get' . $stage;

                if (!method_exists($this, $method) || empty($$stage)) {
                    continue;
                }

                try {
                    $this->{$method}($instance);
                } catch (\InvalidArgumentException $e) {
                    $this->writeStatus('warning', sprintf('SKIP (%s)', $e->getMessage()), true);
                } catch (\Exception $e) {
                    $this->getContainer()->get('error.log')->error($e->getMessage());
                    $this->writeStatus('error', sprintf('FAIL (%s)', $e->getMessage()), true);
                }
            }

            try {
                $this->writePad('- Saving instance');
                $this->getContainer()->get('orm.manager')->persist($instance);
                $this->writeStatus('success', 'DONE', true);
            } catch (\Exception $e) {
                $this->getContainer()->get('error.log')->error($e->getMessage());
                $this->writeStatus('error', sprintf('FAIL (%s)', $e->getMessage()), true);
            }
        }
    }

    /**
     * Returns the list of instances to synchronize.
     *
     * @param array $names The list of instance names
     *
     * @return array The list of instance ids.
     */
    protected function getInstances(?array $names = []) : array
    {
        $sql = 'select id from instances';

        if (!empty($names)) {
            $sql .= sprintf(' where internal_name in ("%s")', implode('","', $names));
        }

        $sql .= ' order by id asc';

        $ids = $this->getContainer()->get('orm.manager')->getConnection('manager')
            ->fetchAll($sql);

        return array_map(function ($a) {
            return $a['id'];
        }, $ids);
    }

    /**
     * Updates the media size for the provided instance.
     *
     * @param Instance $instance The instance to update.
     */
    protected function getMedia(Instance &$instance) : void
    {
        $this->writePad('- Calculating media folder size');

        $instance->media_size = $this->getContainer()
            ->get('core.helper.instance')
            ->getMediaSize($instance);

        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', sprintf(
            ' (%-.2f MB)',
            $instance->media_size / 1024
        ), true);
    }

    /**
     * Returns the list of parameters for the command based on the input.
     *
     * @return array The list of parameters.
     */
    protected function getParameters() : array
    {
        $media = $this->input->getOption('media');
        $stats = $this->input->getOption('stats');

        if (empty($media) && empty($stats)) {
            throw new \InvalidArgumentException('No option specified');
        }

        $instances = $this->input->getOption('instances');

        if (!empty($instances)) {
            $instances = preg_split('/\s*,\s*/', $instances);
        }

        $instances = $this->getInstances($instances);

        return [ $instances, $media, $stats ];
    }

    /**
     * Updates the statistics for the provided instance.
     *
     * @param Instance $instance The instance to update.
     */
    protected function getStats(Instance &$instance) : void
    {
        $helper = $this->getContainer()->get('core.helper.instance');

        $this->writePad('- Checking creation date');
        $instance->created = $helper->getCreated($instance);
        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', $instance->created->format('(Y-m-d H:i:s)'), true);

        $this->writePad('- Checking last activity');
        $instance->last_login = $helper->getLastActivity($instance);
        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', $instance->last_login->format('(Y-m-d H:i:s)'), true);

        $this->writePad('- Counting comments');
        $instance->comments = $helper->countComments($instance);
        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', sprintf(
            ' (%s)',
            $instance->comments
        ), true);

        $this->writePad('- Counting active users');
        $instance->users = $helper->countUsers($instance);
        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', sprintf(
            ' (%s)',
            $instance->users
        ), true);

        $this->writePad('- Counting contents');
        $contents = $helper->countContents($instance);

        foreach ($contents as $type => $total) {
            $instance->{$type . 's'} = $total;
        }

        $instance->contents = array_sum($contents);

        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', sprintf(
            ' (%s)',
            $instance->contents
        ), true);
    }
}
