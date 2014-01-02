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
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Resets the Zend OpCache registers')
            ->setDefinition(
                array(
                    new InputOption('no-warmup', 'w', InputOption::VALUE_OPTIONAL, 'The database password'),
                )
            )
            ->setHelp(
                <<<EOF
The <info>cache:clear</info> cache the app.

<info>php app/console cache:clear</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // var_dump(APPLICATION_DIR.'/cache/*');die();

        // rmdir(APPLICATION_DIR.'/cache/*');

        return false;
    }
}
