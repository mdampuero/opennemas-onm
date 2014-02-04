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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class RepositoryStatusCommand extends Command
{
    protected function configure()
    {
        $this
            // ->setDefinition(
            //     array(
            //         new InputOption('password', 'p', InputOption::VALUE_OPTIONAL, 'The database password'),
            //     )
            // )
            ->setName('repository:status')
            ->setDescription('Shows the latest repository status for code and themes.')
            ->setHelp(
                <<<EOF
The <info>repository:status</info> command shows the current status of all the code,
from application code to themes.

<info>php bin/console repository:status [-p]</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Repository status at '.date('Y/m/d H:i:s'));

        $out = exec('git log --format="%H" -1');
        $repoHashes []= array('Onm code', $out);

        $themes = glob(SITE_PATH.'/themes/*');
        foreach ($themes as $themeFolder) {
            $name = basename($themeFolder);
            chdir($themeFolder);

            $out = exec('git log --format="%H" -1');
            $repoHashes []= array('Theme '.$name, $out);
        }

        $table = $this->getHelperSet()->get('table');
        $table
            ->setHeaders(array('Repo name', 'Commit hash'))
            ->setRows($repoHashes);
        $table->render($output);
    }
}
