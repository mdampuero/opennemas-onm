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

class MigrateCommunity extends Command
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
            ->setName('community:migrate')
            ->setDescription('Migrates an old community DB to the new format')
            ->setHelp(
                <<<EOF
The <info>community:migrate</info> command migrates an old community DB to the new format with new schema and tables.

<info>php bin/console community:migrate host user pass type database</info>

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
        define('INSTANCE_UNIQUE_NAME', 'community');

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();
        \Application::initLogger();

        // Execute functions
        $output->writeln("\t<fg=blue;bg=white>Migrating: ".$dataBaseName."</fg=blue;bg=white>");
        // Migrate database
        $this->updateContentsStarttime($input, $output);
        $this->updateFrontpageArticles($input, $output);
        $this->updateDataBaseSchema($input, $output);
        $this->updateSubmenuItems($input, $output);
        $this->createNewWidgets($input, $output);
        $this->renameDatabase($input, $output);

        $output->writeln(
            "\n\t<fg=yellow;bg=white>Migration finished for Database: ".$dataBaseName."</fg=yellow;bg=white>"
        );

    }

    protected function updateContentsStarttime($input, $output)
    {

        $sql="UPDATE `contents` SET `starttime`=created WHERE `starttime`='0000-00-00 00:00:00'";

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            $output->writeln("\t<error>Can't update contents starttime</error>");
            return false;
        }

        $output->writeln("\t<info>Updated contents starttime successfully</info>");
    }


    protected function updateFrontpageArticles($input, $output)
    {
        $sql = "SELECT pk_content, pk_fk_content_category, placeholder, position
                FROM contents, contents_categories
                WHERE frontpage=1 AND contents.fk_content_type=1
                AND pk_content = pk_fk_content AND content_status=1 AND available=1";

        $rs =  $GLOBALS['application']->conn->Execute($sql);

        $values= array();

        // contents in section frontpages
        while (!$rs->EOF) {
            $values[] =  array(
                $rs->fields['pk_content'],
                $rs->fields['pk_fk_content_category'],
                $rs->fields['placeholder'],
                $rs->fields['position'],
                null,
                'Article'
            );

            $rs->MoveNext();
        }

        // contents for home frontpage
        $sql = "SELECT pk_content, pk_fk_content_category, home_placeholder, home_pos
                FROM contents, contents_categories
                WHERE in_home=1 AND frontpage=1  AND contents.fk_content_type=1
                AND pk_content = pk_fk_content AND content_status=1 AND available=1";

        $rs =   $GLOBALS['application']->conn->Execute($sql);

        while (!$rs->EOF) {
            $values[] =  array(
                $rs->fields['pk_content'],
                0,
                $rs->fields['home_placeholder'],
                $rs->fields['home_pos'],
                null,
                'Article',
            );

            $rs->MoveNext();
        }

        $rs->Close();

        //Insert articles in table content_positions
        $sql = "INSERT INTO `content_positions`
                (`pk_fk_content`, `fk_category`, `placeholder`, `position`, `params`, `content_type`)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $GLOBALS['application']->conn->Prepare($sql);
        $rss = $GLOBALS['application']->conn->Execute($stmt, $values);

        if (!$rss) {
            $output->writeln("\t<error>[Database Error] Executing".$stmt."</error>");
            return false;
        }

        $output->writeln("\t<info>Updated articles in frontpage successfully</info>");
    }

    protected function updateDataBaseSchema($input, $output)
    {
        $sqls = array();
        $sqls[] = "CREATE TABLE IF NOT EXISTS `contentmeta` (
                    `fk_content` bigint(20) NOT NULL,
                    `meta_name` varchar(255) NOT NULL,
                    `meta_value` text,
                    PRIMARY KEY (`fk_content`,`meta_name`),
                    KEY `fk_content` (`fk_content`)
                ) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `orders` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `user_id` bigint(20) NOT NULL,
                    `content_id` bigint(20) NOT NULL,
                    `created` datetime NOT NULL,
                    `payment_id` varchar(50) NOT NULL,
                    `payment_status` varchar(150) NOT NULL,
                    `payment_amount` decimal(10,2) NOT NULL,
                    `payment_method` varchar(200) NOT NULL,
                    `params` longtext NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `usermeta` (
                    `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                    `meta_key` varchar(255) DEFAULT NULL,
                    `meta_value` longtext,
                    PRIMARY KEY (`user_id`, `meta_key`),
                    KEY `user_id` (`user_id`),
                    KEY `meta_key` (`meta_key`)
                ) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";


        $sqls[] = "UPDATE users SET name = CONCAT(name, ' ', firstname, ' ', lastname)";
        $sqls[] = "DROP TABLE privileges";
        $sqls[] = "DROP TABLE  `articles_clone`";


        $sqls[] = "ALTER TABLE `kioskos` ADD `type` TINYINT NOT NULL DEFAULT '0' COMMENT '0-item, 1-subscription'";
        $sqls[] = "ALTER TABLE `menues` ADD  `position` VARCHAR( 50 ) AFTER  `type`";
        $sqls[] = "ALTER TABLE `menues` DROP INDEX  `pk_menu`";
        $sqls[] = "ALTER TABLE `menues` DROP INDEX  `name_2`";
        $sqls[] = "ALTER TABLE `menues` ADD INDEX  `position` (  `position` ( 50 ) )";
        $sqls[] = "ALTER TABLE `kioskos` DROP `favorite`";
        $sqls[] = "ALTER TABLE `kioskos` ADD `price` DECIMAL NOT NULL DEFAULT '0'";
        $sqls[] = "ALTER TABLE `users` DROP  `firstname` , DROP  `lastname`";
        $sqls[] = "ALTER TABLE `users` ADD `type` TINYINT NOT NULL DEFAULT '0' COMMENT '0-backend, 1-frontend'";
        $sqls[] = "ALTER TABLE `users` ADD `token` VARCHAR( 50 ) NULL DEFAULT NULL AFTER `type`";
        $sqls[] = "ALTER TABLE `users` ADD `deposit` DECIMAL NOT NULL DEFAULT '0' AFTER  `type`";
        $sqls[] = "ALTER TABLE `users` DROP  `address` , DROP  `phone`, DROP `online`";
        $sqls[] = "ALTER TABLE `newsletter_archive` ADD `title` VARCHAR( 255 ) NOT NULL AFTER `pk_newsletter`";
        $sqls[] = "ALTER TABLE `newsletter_archive` ADD `sent` VARCHAR( 255 ) NOT NULL";
        $sqls[] = "ALTER TABLE `newsletter_archive` ADD `html` LONGTEXT NULL DEFAULT NULL";
        $sqls[] = "ALTER TABLE `author_imgs` DROP PRIMARY KEY , ADD PRIMARY KEY (  `pk_img` ,  `fk_author` )";
        $sqls[] = "ALTER TABLE `menu_items` DROP PRIMARY KEY , ADD PRIMARY KEY (  `pk_item` ,  `pk_menu` )";
        $sqls[] = "ALTER TABLE `authors` ADD `params` TEXT NULL DEFAULT NULL";
        $sqls[] = "ALTER TABLE `books` ADD `file_img` VARCHAR( 255 ) NULL";

        foreach ($sqls as $sql) {
            $rs =  $GLOBALS['application']->conn->Execute($sql);
            if (!$rs) {
                $output->writeln("\t<error>[Database Error] Executing: ".$sql."</error>");
                return false;
            }
        }

        $output->writeln("\t<info>Updated DataBase schema successfully</info>");
    }


    protected function updateSubmenuItems($input, $output)
    {
        $aux = "SELECT pk_father FROM menues WHERE pk_father <> 0";
        $sql = "UPDATE `menu_items` SET `pk_menu`=1 WHERE `pk_father` IN ($aux)";

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            $output->writeln("\t<error>Can't update submenu items</error>");
            return false;
        }

        $output->writeln("\t<info>Updated submenu items successfully</info>");
    }

    protected function createNewWidgets($input, $output)
    {
        $widgetData = array(
            array(
                'title'       => 'Widget More news in category',
                'available'   => 1,
                'renderlet'   => 'intelligentwidget',
                'metadata'    => 'widget, more, news, category',
                'description' => 'widget more news in category',
                'content'     => 'MoreNewsInCategory',
                'fk_user'     => 4,
            ),
            array(
                'title'       => 'Widget Facebook Like Box',
                'available'   => 1,
                'renderlet'   => 'intelligentwidget',
                'metadata'    => 'widget, facebook, like, box',
                'description' => 'widget facebook like box',
                'content'     => 'Facebook',
                'fk_user'     => 4,
            ),
            array(
                'title'       => 'Widget Facebook Button Box',
                'available'   => 1,
                'renderlet'   => 'intelligentwidget',
                'metadata'    => 'widget, facebook, button, box',
                'description' => 'widget facebook button box',
                'content'     => 'FacebookButton',
                'fk_user'     => 4,
            ),
            array(
                'title'       => 'Widget Other Opinions By Author',
                'available'   => 1,
                'renderlet'   => 'intelligentwidget',
                'metadata'    => 'widget, opinion, by, author',
                'description' => 'widget opinion by author',
                'content'     => 'OpinionByauthor',
                'fk_user'     => 4,
            ),
            array(
                'title'       => 'Widget Latest Opinions',
                'available'   => 1,
                'renderlet'   => 'intelligentwidget',
                'metadata'    => 'widget, opinion, latest',
                'description' => 'widget latest opinions',
                'content'     => 'LastestOpinions',
                'fk_user'     => 4,
            ),
            array(
                'title'       => 'Widget Latest Comments new',
                'available'   => 1,
                'renderlet'   => 'intelligentwidget',
                'metadata'    => 'widget, comments, new, latest',
                'description' => 'widget latest comments new',
                'content'     => 'LatestCommentsNew',
                'fk_user'     => 4,
            ),
        );

        // Initialize required session vars for the logger
        $_SESSION['userid'] = 4;
        $_SESSION['username'] = 'alex';

        // Create widgets on database
        $widget = new \Widget();
        foreach ($widgetData as $item) {
            if (!$widget->create($item)) {
                $output->writeln("\t<error>Cannot create widget: ".$item."</error>");
                return false;
            }
        }

        $output->writeln("\t<info>Created all widgets successfully</info>");
    }

    protected function renameDatabase($input, $output)
    {
         // Init connection to onm-instances Database
        $GLOBALS['application']->connInstaces = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->connInstaces->Connect(BD_HOST, BD_USER, BD_PASS, 'onm-instances');

        // Get database name and host from command line
        $dataBaseName = $input->getArgument('database');
        $dataBaseHost = $input->getArgument('host');

        $sql = "SELECT `id`, `settings` FROM `instances`";
        $rs = $GLOBALS['application']->connInstaces->Execute($sql);
        if (!$rs) {
            $output->writeln("\t<error>[Database Error] Couldn't fetch instaces settings ".$sql."</error>");
            return false;
        }

        foreach ($rs as $value) {
            $settings = unserialize($value['settings']);

            if ($settings['BD_DATABASE'] == $dataBaseName) {

                // Rename user for this database
                $sql = "RENAME USER `".$settings['BD_USER']."`@".$dataBaseHost.
                       " TO `".$value['id']."`@".$dataBaseHost;
                $rs = $GLOBALS['application']->conn->Execute($sql);
                if (!$rs) {
                    $output->writeln("\t<error>[Database Error] Couldn't rename user: ".$sql."</error>");
                    return false;
                }

                // Rename database name on instance settings
                $settings['BD_DATABASE'] = $value['id'];

                // Rename user name on instance settings
                $settings['BD_USER'] = $value['id'];

                // Set external media to empty
                $settings['MEDIA_URL'] = '';

                // Set the password for this user
                $sql = "SET PASSWORD FOR `".$settings['BD_USER']."`@`".$dataBaseHost.
                        "` = PASSWORD('".$settings['BD_PASS']."')";
                $rs = $GLOBALS['application']->conn->Execute($sql);
                if (!$rs) {
                    $sql = "GRANT USAGE ON *.* TO '".$settings['BD_USER']."'@'".$dataBaseHost.
                            "' IDENTIFIED BY '".$settings['BD_PASS']."'";
                    $rs = $GLOBALS['application']->conn->Execute($sql);
                    if (!$rs) {
                        $output->writeln("\t<error>[Database Error] Couldn't set user password: ".$sql."</error>");
                        return false;
                    }
                }

                // Update instace database settings
                $sql = "UPDATE instances SET settings='".serialize($settings)."' WHERE id=".$value['id'];
                $rs = $GLOBALS['application']->connInstaces->Execute($sql);
                if (!$rs) {
                    $output->writeln("\t<error>Cannot update instace database settings : ".$sql."</error>");
                    return false;
                }

                // Create new Database
                $sql = "CREATE database `".$value['id']."`";
                $rs = $GLOBALS['application']->conn->Execute($sql);
                if (!$rs) {
                    $output->writeln("\t<error>Cannot create database : ".$sql."</error>");
                    return false;
                }

                // Fetch all tables given a database
                $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE table_schema='".$dataBaseName."'";
                $rs = $GLOBALS['application']->conn->Execute($sql);
                if (!$rs) {
                    $output->writeln("\t<error>Cannot fetch database tables : ".$sql."</error>");
                    return false;
                }

                // Rename all database tables
                foreach ($rs as $table) {
                    $sql = "RENAME TABLE `".$dataBaseName."`.`".$table["TABLE_NAME"].
                           "` TO `".$value['id']."`.`".$table["TABLE_NAME"]."`";
                    $rss = $GLOBALS['application']->conn->Execute($sql);
                    if (!$rss) {
                        $output->writeln("\t<error>Cannot rename table : ".$sql."</error>");
                        return false;
                    }
                }

                // Grant usage on new database
                $sql = "GRANT USAGE ON `".$value['id']."` . * TO '".$value['id']."'@'".$dataBaseHost."'";
                $rs = $GLOBALS['application']->conn->Execute($sql);
                if (!$rs) {
                    $output->writeln("\t<error>Cannot grant usage on ".$value['id']." : ".$sql."</error>");
                    return false;
                }

                // Grant all privileges on new database
                $sql = "GRANT ALL PRIVILEGES ON `".$value['id']."` . * TO '".$value['id']."'@'".$dataBaseHost."'";
                $rs = $GLOBALS['application']->conn->Execute($sql);
                if (!$rs) {
                    $output->writeln("\t<error>Cannot grant all privileges on ".$value['id']." : ".$sql."</error>");
                    return false;
                }

                // Revoke all privileges on old database
                $sql = "REVOKE ALL PRIVILEGES ON `".$dataBaseName."` . * FROM '".$value['id']."'@'".$dataBaseHost."'";
                $rs = $GLOBALS['application']->conn->Execute($sql);
                if (!$rs) {
                    $output->writeln("\t<error>Cannot revoke all privileges on ".$dataBaseName." : ".$sql."</error>");
                    return false;
                }

                // Revoke grant option on old database
                // $sql = "REVOKE GRANT OPTION ON `".$dataBaseName."` . * FROM '".$value['id']."'@'".$dataBaseHost."'";
                // $rs = $GLOBALS['application']->conn->Execute($sql);
                // if (!$rs) {
                //     $output->writeln("\t<error>Cannot revoke grant option on ".$dataBaseName." : ".$sql."</error>");
                //     return false;
                // }

                // Drop old database
                $sql = "DROP database `".$dataBaseName."`";
                $rs = $GLOBALS['application']->conn->Execute($sql);
                if (!$rs) {
                    $output->writeln("\t<error>Cannot delete old database : ".$sql."</error>");
                    return false;
                }
            }
        }

        $output->writeln("\t<info>Database '".$dataBaseName."' renamed successfully</info>");
    }
}
