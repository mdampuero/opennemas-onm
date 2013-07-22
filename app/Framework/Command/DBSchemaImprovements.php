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

class DBSchemaImprovements extends Command
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
            ->setName('improve:db')
            ->setDescription('Improves an old format database for new one')
            ->setHelp(
                <<<EOF
The <info>improve:db</info> command improves an old format DB to the new format with new schema.

<info>php bin/console improve:db user [-p pass] database</info>

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
                'What is the database user password?',
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

        require_once $basePath.'/app/autoload.php';
        require_once 'Application.php';

        // Initialize internal constants for logger
        // Logger in content class when creating widgets
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('INSTANCE_UNIQUE_NAME', 'community');

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();
        \Application::initLogger();

        // Start executing script
        $output->writeln("\tImproving: ".$dataBaseName." DB\n");

        $this->updateContentType($input, $output);
        $this->updateCommentsTable($input, $output);

        $output->writeln(
            "\n\tImprovements finished for : ".$dataBaseName." DB"
        );

    }

    protected function updateContentType($input, $output)
    {
        // Add column content_type to contents table
        $sql = "ALTER TABLE `contents` ADD `content_type_name` VARCHAR( 20 ) NOT NULL AFTER `fk_content_type`";
        $rs  = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            $output->writeln("\t<error>Can't add content_type_name column to contents table</error>");
            return false;
        }

        // Update content_type with fk_content_type transformed values
        $sqls = array();
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'article' WHERE fk_content_type = 1";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'advertisement' WHERE fk_content_type = 2";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'attachment' WHERE fk_content_type = 3";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'opinion' WHERE fk_content_type = 4";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'event' WHERE fk_content_type = 5";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'comment' WHERE fk_content_type = 6";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'album' WHERE fk_content_type = 7";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'photo' WHERE fk_content_type = 8";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'video' WHERE fk_content_type = 9";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'special' WHERE fk_content_type = 10";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'poll' WHERE fk_content_type = 11";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'widget' WHERE fk_content_type = 12";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'static_page' WHERE fk_content_type = 13";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'kiosko' WHERE fk_content_type = 14";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'book' WHERE fk_content_type = 15";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'schedule' WHERE fk_content_type = 16";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'letter' WHERE fk_content_type = 17";
        $sqls[] = "UPDATE `contents` SET `content_type_name` = 'frontpage' WHERE fk_content_type = 18";

        foreach ($sqls as $sql) {
            $rs = $GLOBALS['application']->conn->Execute($sql);
            if (!$rs) {
                $output->writeln("\t<error>Can't update content_type_name: ".$sql."</error>");
            }
        }

        $output->writeln("\t<info>Added and Updated content_type_name column on contents table</info>");
    }

    protected function updateCommentsTable($input, $output)
    {
        // Add column content_type to contents table
        $sql = "ALTER TABLE `comments` ADD `content_type_referenced` VARCHAR( 20 ) NOT NULL";
        $rs  = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            $output->writeln("\t<error>Can't add content_type_referenced column to comments table</error>");
            return false;
        }

        // Fetch all comments
        $sql = "SELECT * FROM `comments`";
        $rs  = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            $output->writeln("\t<error>Can't fetch comments</error>");
        }

        while (!$rs->EOF) {
            // Get id of this comment
            $id = $rs->fields['id'];

            // Get id of the content commented
            $contentId = $rs->fields['content_id'];

            // Get fk_content_type from content id
            $sql2 = "SELECT fk_content_type FROM contents WHERE pk_content=".$contentId;
            $rs2  = $GLOBALS['application']->conn->Execute($sql2);
            if (!$rs2) {
                $output->writeln("\t<error>Can't fetch fk_content_type from content: ".$contentId."</error>");
            }

            $contentTypeName = \ContentManager::getContentTypeNameFromId($rs2->fields['fk_content_type']);

            $sqlAux = "UPDATE `comments` SET `content_type_referenced` = '".$contentTypeName."' WHERE `id` = ".$id;
            $rsAux = $GLOBALS['application']->conn->Execute($sqlAux);
            if (!$rsAux) {
                $output->writeln("\t<error>Can't update content_type_referenced on comment id".$id."</error>");
            }

            $rs->MoveNext();
        }

        $output->writeln("\t<info>Added and Updated content_type_referenced column on comments table</info>");
    }
}
