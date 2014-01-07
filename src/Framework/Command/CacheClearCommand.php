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

class CacheClearCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Cleans all the Symfony generated files')
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
        $fileSystem = $this->getContainer()->get('filesystem');

        $basePath = realpath($this->getContainer()->get('kernel')->getCacheDir());
        if (file_exists($basePath)) {
            $fileSystem->remove($basePath);
            $output->writeln($basePath.' removed succesfully');
        } else {
            $output->writeln($basePath.' doesn\'t exists');
        }

        return false;
    }
}
