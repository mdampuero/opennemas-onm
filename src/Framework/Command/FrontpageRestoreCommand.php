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
use Symfony\Component\Filesystem\Filesystem;

class FrontpageRestoreCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('frontpage:restore')
            ->setDescription('Restores the frontpage contents.')
            ->setDefinition([
                new InputArgument('database', InputArgument::REQUIRED, 'database'),
                new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'The frontpage positions file'),
                new InputOption('category', 'c', InputOption::VALUE_REQUIRED, 'The frontpage positions file'),
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
        $databaseName = $input->getArgument('database');
        $category     = $input->getOption('category');
        $file         = $input->getOption('file');
        $oql          = "settings regexp '\"BD_DATABASE\";s:[0-9]+:\""
            . $databaseName . "\"'";

        // Load instance
        $loader = $this->getContainer()->get('core.loader');
        $loader->loadInstanceFromOql($oql);
        $loader->init();

        // TODO: Remove ASAP
        $this->getContainer()->get('core.security')->setCliUser();

        $conn = getService('orm.manager')->getConnection('instance');

        $conn->selectDatabase($databaseName);

        $conn->fetchAssoc('SELECT count(*) FROM contents');

        $positionsJson = file_get_contents($file);
        $positions     = json_decode($positionsJson, true);

        if (is_null($positions)) {
            $output->writeln('<error>File provided is not valid</error>');
            return 1;
        }

        $done = \ContentManager::saveContentPositionsForHomePage($category, $positions);

        if ($done) {
            $output->writeln('[DONE]');
        } else {
            $output->writeln('[FAILED]');
        }

        return false;
    }
}
