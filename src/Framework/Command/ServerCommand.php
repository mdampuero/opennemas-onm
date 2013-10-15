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

class ServerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('address', InputArgument::OPTIONAL, 'Address:port', 'localhost:8000'),
                    new InputOption('docroot', 'd', InputOption::VALUE_REQUIRED, 'Document root', 'web/'),
                    new InputOption('router', 'r', InputOption::VALUE_REQUIRED, 'Path to custom router script'),
                )
            )
            ->setName('server:run')
            ->setDescription('Runs PHP built-in web server')
            ->setHelp(
<<<EOF
The <info>%command.name%</info> runs PHP built-in web server:

  <info>%command.full_name%</info>

To change default bind address and port use the <info>address</info> argument:

  <info>%command.full_name% 127.0.0.1:8080</info>

To change default docroot directory use the <info>--docroot</info> option:

  <info>%command.full_name% --docroot=htdocs/</info>

If you have custom docroot directory layout, you can specify your own
router script using <info>--router</info> option:

  <info>%command.full_name% --router=app/config/router.php</info>

See also: http://www.php.net/manual/en/features.commandline.webserver.php
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("Server running on <info>%s</info>\n", $input->getArgument('address')));

        $webPath = APPLICATION_PATH."/public/";

        $command = implode(' ', array(PHP_BINARY, '-S', $input->getArgument('address'), $webPath, $webPath."app.php"));

        exec($command);
    }
}
