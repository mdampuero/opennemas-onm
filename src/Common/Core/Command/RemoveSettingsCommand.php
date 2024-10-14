<?php

namespace Common\Core\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveSettingsCommand extends Command
{
    /**
     * Configures the command options and description.
     */
    protected function configure()
    {
        $this
            ->setName('core:settings:remove')
            ->setDescription('Command to remove specific fields from settings in the settings table.')
            ->setHelp('This command removes fields from the settings based on the given parameters.')
            ->addOption(
                'keys',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Array of keys and fields to be removed (e.g. key=extraInfoContents.ARTICLE_MANAGER,fields=noindex)',
                null
            )
            ->addOption(
                'removeEmptyGroups',
                null,
                InputOption::VALUE_OPTIONAL,
                'Whether to remove the group if it becomes empty (default is false)',
                false
            );
    }

    /**
     * Executes the command logic for removing fields from settings.
     *
     * @param InputInterface $input The input interface.
     * @param OutputInterface $output The output interface.
     * @return int Exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->start();

        $searchKeysInput = $input->getOption('keys');

        if (!$searchKeysInput) {
            $output->writeln(sprintf(
                ' <fg=red;options=bold>FAIL - </> The parameters keys is mandatory'
            ));
            return;
        }

        $searchKeys = $this->processSearchKeys($searchKeysInput);

        $output->writeln(sprintf(
            str_pad('<options=bold>Starting command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $instances = $this->getInstances();

        $index        = 1;
        $coreLoader   = $this->getContainer()->get('core.loader');
        $coreSecurity = $this->getContainer()->get('core.security');
        $ormManager   = $this->getContainer()->get('orm.manager');

        foreach ($instances as $instance) {
            try {
                $coreLoader->load($instance->internal_name);
                $coreSecurity->setInstance($instance);

                $settingsManager = $ormManager->getDataSet('Settings');
                $output->writeln(str_pad(sprintf(
                    '<fg=blue;options=bold>==></><options=bold> (%s/%s) Processing instance %s</>',
                    $index++,
                    count($instances),
                    $instance->internal_name
                ), 50, '.'));

                $this->processSettings($searchKeys, $settingsManager, $input);

                $output->writeln(str_pad(sprintf('<fg=green;options=bold>DONE</>'), 50, '.'));
            } catch (\Exception $e) {
                $output->writeln(sprintf(
                    ' <fg=red;options=bold>FAIL</> <fg=blue;options=bold>(%s)</>',
                    $e->getMessage()
                ));
                continue;
            }
        }

        $output->writeln(sprintf(
            str_pad('<options=bold>Ending command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>'
                . ' <fg=yellow;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->ended),
            $this->getDuration()
        ));

        return 0;
    }

    /**
     * Processes the settings by removing specified fields and optionally removing empty groups.
     *
     * @param array $searchKeys The keys and fields to be processed.
     * @param object $settingsManager The settings manager instance.
     * @param InputInterface $input The input interface.
     */
    private function processSettings($searchKeys, $settingsManager, $input)
    {
        $removeEmptyGroups = $input->getOption('removeEmptyGroups');

        foreach ($searchKeys as $searchKey) {
            $originalGroups = $settingsManager->get($searchKey['key']) ?? [];
            $groups         = $originalGroups;
            $hasChanges     = false;

            foreach ($groups as $key => $group) {
                $updatedFields = array_diff_key($group['fields'], array_flip($searchKey['fields']));

                if ($updatedFields !== $group['fields']) {
                    $hasChanges             = true;
                    $groups[$key]['fields'] = $updatedFields;
                }

                if (empty($groups[$key]['fields']) && $removeEmptyGroups) {
                    unset($groups[$key]);
                    $hasChanges = true;
                }
            }

            if ($hasChanges) {
                $settingsManager->set($searchKey['key'], $groups);
            }
        }
    }

    /**
     * Converts the searchKeys input into an array format.
     *
     * @param array $searchKeysInput The search keys input array.
     * @return array Processed search keys.
     */
    private function processSearchKeys(array $searchKeysInput): array
    {
        $searchKeys = [];

        if ($searchKeysInput) {
            foreach ($searchKeysInput as $searchKeyInput) {
                $parts  = explode(',', $searchKeyInput, 2);
                $key    = str_replace('key=', '', $parts[0]);
                $fields = explode(',', str_replace('fields=', '', $parts[1]));

                $searchKeys[] = [
                    'key' => $key,
                    'fields' => $fields
                ];
            }
        }

        return $searchKeys;
    }

    /**
     * Returns the list of active instances.
     *
     * @return array The list of instances.
     */
    protected function getInstances(): array
    {
        $oql = sprintf(
            'activated = 1',
        );

        return $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->findBy($oql);
    }
}
