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

class FrameworkCheckCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('framework:check')
            ->setDescription('Checks the application confirmation')
            ->setHelp(
                <<<EOF
The <info>framework:check</info> checks if the application can be executed with current configuration values.

<info>php app/console framework:check</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $frameworkStatus = $this->getContainer()->get('framework.status');
        var_dump($frameworkStatus);die();

        return true;
    }
}
