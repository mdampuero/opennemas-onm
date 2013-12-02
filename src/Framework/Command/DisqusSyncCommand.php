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
use Symfony\Component\EventDispatcher\Event;
use Onm\Settings as s;

class DisqusSyncCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('user', InputArgument::REQUIRED, 'user'),
                    new InputOption('password', 'p', InputOption::VALUE_OPTIONAL, 'The database password'),
                    new InputArgument('database', InputArgument::REQUIRED, 'database'),
                )
            )
            ->setName('disqus:import')
            ->setDescription('Executes comments import action with Disqus Api')
            ->setHelp(
                <<<EOF
The <info>disqus:import</info> executes acomments import action with Disqus Api.

<info>php app/console disqus:import user [-p pass] database</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $basePath = APPLICATION_PATH;
        $phpBinPath = exec('which php');

        chdir($basePath);

        $dataBaseHost = 'localhost';
        $dataBaseType = 'mysqli';
        $dataBaseUser = $input->getArgument('user');
        $dataBaseName = $input->getArgument('database');
        $dataBasePass = $input->getOption('password');

        define('BD_HOST', $dataBaseHost);
        define('BD_USER', $dataBaseUser);
        define('BD_TYPE', $dataBaseType);
        define('BD_DATABASE', $dataBaseName);

        if (!$dataBasePass) {
            // Ask password
            $dialog = $this->getHelperSet()->get('dialog');

            $validator = function ($value) {
                if (trim($value) == '') {
                    throw new \Exception('The password can not be empty, please try again');
                } elseif (!$connect = @mysql_connect('localhost', BD_USER, $value)) {
                    throw new \Exception('The password is wrong, please try again');
                }

                // Close connection if opened
                if ($connect) {
                    mysql_close($connect);
                }

                return $value;
            };

            $dataBasePass = $dialog->askHiddenResponseAndValidate(
                $output,
                'What is the password?',
                $validator,
                5,
                false
            );
        }

        if (trim($dataBasePass) == '') {
            throw new \Exception('The password can not be empty, please try again');
        } elseif (!$connect = @mysql_connect('localhost', BD_USER, $dataBasePass)) {
            throw new \Exception('The password is wrong, please try again');
        }

        // Close connection if opened
        if ($connect) {
            mysql_close($connect);
        }

        define('BD_PASS', $dataBasePass);

        // Initialize internal constants for
        define('CACHE_PREFIX', 'disqus');
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('INSTANCE_UNIQUE_NAME', 'community');

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();

        // Init script
        $output->writeln("\tStart disqus comments import");

        // Import
        $this->fetchDisqusPosts($input, $output);

        // Finish script
        $output->writeln("\n\tFinished disqus comments import");

    }


    protected function fetchDisqusPosts($input, $output)
    {
        // Get service container and save disqus_last_sync cache time and uuid
        global $sc;
        $cache = $sc->get('cache');
        $cache->save(
            CACHE_PREFIX.'disqus_last_sync',
            array('time' => time(), 'uuid' => uniqid()),
            300
        );

        // Save disqus comments to database
        \Onm\DisqusSync::saveDisqusCommentsToDatabase();

        $output->writeln("\t<info>Disqus comments imported successfully</info>");
    }
}
