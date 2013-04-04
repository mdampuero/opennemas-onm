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

class OpinionSummaryGenerator extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('user', InputArgument::REQUIRED, 'user'),
                    new InputArgument('database', InputArgument::REQUIRED, 'database'),
                )
            )
            ->setName('opinion:generator')
            ->setDescription('Generate opinion new fields in DB to the new format')
            ->setHelp(
                <<<EOF
The <info>opinion:generator</info> command for generate summary and other fields in database.

<info>php bin/console opinion:generator user [-p pass] database</info>

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

        $dialog = $this->getHelperSet()->get('dialog');

        $validator = function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The password can not be empty');
            }
        };

        $dataBasePass = $password = $dialog->askHiddenResponse(
            $output,
            'What is the database user password?',
            false
        );

        define('BD_HOST', $dataBaseHost);
        define('BD_USER', $dataBaseUser);
        define('BD_PASS', $dataBasePass);
        define('BD_TYPE', $dataBaseType);
        define('BD_DATABASE', $dataBaseName);

        require_once $basePath.'/app/autoload.php';
        require_once 'Application.php';

        // Initialize internal constants for logger
        // Logger in content class when creating widgets
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('INSTANCE_UNIQUE_NAME', 'opennemas');

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();
        \Application::initLogger();

        // Execute functions

        $this->updateOpinionFields($input, $output);

    }

    protected function updateOpinionFields($input, $output)
    {
        $sql = "SELECT pk_content, body FROM contents, opinions "
            ." WHERE pk_content=pk_opinion";

        $rs =  $GLOBALS['application']->conn->Execute($sql);

        $values= array();
        while (!$rs->EOF) {
            $summary = substr($rs->fields['body'], 0, 500);
            $pos = strripos($summary, ".");
            if ($pos > 100) {
                $summary = substr($summary, 0, $pos).".";
            } else {
                $summary = substr($summary, 0, strripos($summary, " "));
            }
            $values[] =  array(
                $rs->fields['pk_content'],
                'summary',
                strip_tags($summary),
            );

            $rs->MoveNext();
        }

        $rs->Close();

        //Insert articles in table content_positions
        $sql = "INSERT INTO contentmeta (`fk_content`, `meta_name`, `meta_value`)"
                    ." VALUES (?, ? ,? )";

        $stmt = $GLOBALS['application']->conn->Prepare($sql);
        $rss = $GLOBALS['application']->conn->Execute($stmt, $values);

        if (!$rss) {
            $output->writeln("\t[Database Error] Executing ".$stmt);
            return false;
        }

        $output->writeln("\tUpdated opinion in frontpage successfully");
    }
}
