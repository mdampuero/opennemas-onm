<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FrontpageRestoreCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('frontpage:restore')
            ->setDescription('Restores the frontpage contents.')
            ->setDefinition([
                new InputArgument('instance', InputArgument::REQUIRED, 'The instance internal name'),
                new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'The frontpage positions file'),
                new InputOption('category', 'c', InputOption::VALUE_REQUIRED, 'The frontpage positions file'),
                new InputOption(
                    'frontpageVersionId',
                    'x',
                    InputArgument::OPTIONAL,
                    'The frontpage version id'
                )
            ])
            ->setHelp(
                <<<EOF
The <info>cache:clear</info> cache the app.

<info>php app/console frontpage:restore</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instanceName       = $input->getArgument('instance');
        $category           = $input->getOption('category');
        $file               = $input->getOption('file');
        $frontpageVersionId = $input->getOption('frontpageVersionId');

        // TODO: Remove ASAP
        $this->getContainer()->get('core.security')->setCliUser();

        $instance = $this->getContainer()->get('core.loader')
            ->loadInstanceFromInternalName($instanceName);
        $conn     = $this->getContainer()->get('dbal_connection');
        $conn->selectDatabase($instance->getDatabaseName());

        $conn->fetchAssoc('SELECT count(*) FROM contents');

        $positionsJson = file_get_contents($file);
        $positions     = json_decode($positionsJson, true);

        if (is_null($positions)) {
            $output->writeln('<error>File provided is not valid</error>');
            return 1;
        }

        if (empty($frontpageVersionId)) {
            $frontpageVersionId = $this->getContainer()
                ->get('api.service.frontpage_version')
                ->getCurrentVersionDB($category);
        }

        $done = \ContentManager::saveContentPositionsForHomePage($category, $frontpageVersionId, $positions);

        if ($done) {
            $output->writeln('[DONE]');
        } else {
            $output->writeln('[FAILED]');
        }

        return false;
    }
}
