<?php

namespace Common\Core\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OnmAIMaintenanceCommand extends Command
{
    /**
     * Configures the command options and description.
     */
    protected function configure()
    {
        $this
            ->setName('core:onmai:maintenance')
            ->setDescription('Command to perform maintenance and migration tasks in the ONMAI module.')
            ->setHelp('This command executes different maintenance or migration operations in the ONMAI module,
                depending on the provided --type parameter.')

            ->addOption(
                'type',
                null,
                InputOption::VALUE_REQUIRED,
                'Type of maintenance operation (e.g. migrate-instructions)'
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

        $type = $input->getOption('type');

        if (!$type) {
            $output->writeln(sprintf(
                ' <fg=red;options=bold>FAIL - </> The parameters type is mandatory'
            ));
            return;
        }

        $output->writeln(sprintf(
            str_pad('<options=bold>Starting command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        switch ($type) {
            case 'migrate-instructions':
                $response = $this->migrateInstructions();
                break;
            default:
                break;
        }

        $output->writeln(sprintf(
            str_pad('<options=bold>' . $response, 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        return 0;
    }


    private function migrateInstructions()
    {
        $ormManager         = $this->getContainer()->get('orm.manager');
        $serviceManager     = $ormManager->getDataSet('Settings', 'manager');
        $instructions       = $serviceManager->get('onmai_instructions') ?? [];
        $instructionsWithId = $this->assignUniqueIds($instructions);
        // Save ID
        $serviceManager->set('onmai_instructions', $instructionsWithId);

        $prompts           = $ormManager->getRepository('PromptManager')->findBy('');
        $instructionsGroup = $this->groupByTypeAndField($instructionsWithId);

        foreach ($prompts as $prompt) {
            $prompt->prompt = htmlspecialchars($prompt->prompt ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $instructionsIds      = [];
            $instructionsIds      = array_merge(
                $instructionsIds,
                $this->getIdsByTypeAndField($instructionsGroup, 'Both', 'all'),
                $this->getIdsByTypeAndField($instructionsGroup, $prompt->mode, 'all'),
                $this->getIdsByTypeAndField($instructionsGroup, 'Both', $prompt->field),
                $this->getIdsByTypeAndField($instructionsGroup, $prompt->mode, $prompt->field)
            );
            $prompt->instructions = array_unique($instructionsIds);
            $ormManager->persist($prompt);
        }

        return sprintf('Processed %d prompts', count($prompts));
    }

    private function assignUniqueIds(array $items): array
    {
        foreach ($items as $index => $item) {
            if (!isset($item['id'])) {
                $items[$index]['id'] = uniqid(true);
            }
        }
        return $items;
    }

    private function groupByTypeAndField(array $items): array
    {
        $grouped = [];

        foreach ($items as $item) {
            $type = $item['type'] ?? 'undefined';
            $field = $item['field'] ?? 'undefined';

            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }

            if (!isset($grouped[$type][$field])) {
                $grouped[$type][$field] = [];
            }

            $grouped[$type][$field][] = $item;
        }

        return $grouped;
    }

    protected function getIdsByTypeAndField(array $grouped, string $type, string $field): array
    {
        $ids = [];

        if (!isset($grouped[$type][$field])) {
            return $ids;
        }

        foreach ($grouped[$type][$field] as $item) {
            if (isset($item['id'])) {
                $ids[] = $item['id'];
            }
        }

        return $ids;
    }
}
