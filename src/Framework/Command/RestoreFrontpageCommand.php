<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class RestoreFrontpageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('frontpage:restore')
            ->setDescription('Restores the frontpage contents.')
            ->setDefinition(
                array(
                    new InputArgument('database', InputArgument::REQUIRED, 'database'),
                    new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'The frontpage positions file'),
                    new InputOption('category', 'c', InputOption::VALUE_REQUIRED, 'The frontpage positions file'),
                )
            )
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

        $_SESSION['username'] = 'console';
        $_SESSION['userid'] = '0';

        $this->connection = $this->getContainer()->get('db_conn');
        $this->connection->selectDatabase($databaseName);

        $GLOBALS['application'] = new \Application();
        $GLOBALS['application']->conn = $this->connection;

        $positions = file_get_contents($input->getOption('file'));
        $positions = json_decode($positions, true);

        \ContentManager::saveContentPositionsForHomePage($category, $positions);

        return false;
    }
}
