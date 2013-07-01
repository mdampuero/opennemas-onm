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

class UnifyAuthorsAndUsersCommand extends Command
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
            ->setName('merge:authors')
            ->setDescription('Merges authors and authors images tables with users')
            ->setHelp(
                <<<EOF
The <info>merge:authors</info> command unifies author and authors_img tables with users.

<info>php bin/console merge:authors user [-p pass] database</info>

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

        // Init script
        $output->writeln("\tMerging authors with users in Database: ".$dataBaseName);

        // Merge
        $this->updateUsersTable($input, $output);
        $this->createAuthorsGroup($input, $output);
        $this->addAuthorsOnUsers($input, $output);
        $this->deleteAuthorAndAuthorImgTables($input, $output);

        // Finish script
        $output->writeln("\n\tMerge finished for Database: ".$dataBaseName);

    }

    protected function updateUsersTable($input, $output)
    {
        $sqls   = array();
        $sqls[] = "ALTER TABLE `users` CHANGE `login` `username` VARCHAR( 100 ) ".
                    "CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
        $sqls[] = "ALTER TABLE `users` CHANGE `authorize` `activated` TINYINT( 1 ) ".
                    "NOT NULL DEFAULT '1' COMMENT '1 activated - 0 deactivated'";
        $sqls[] = "ALTER TABLE `users` CHANGE `fk_user_group` `fk_user_group` VARCHAR( 100 ) NULL";
        $sqls[] = "ALTER TABLE `users` ADD `url` VARCHAR( 255 ) CHARACTER ".
                    "SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' AFTER `sessionexpire`";
        $sqls[] = "ALTER TABLE `users` ADD `bio` TEXT CHARACTER ".
                    "SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' AFTER `url`";
        $sqls[] = "ALTER TABLE `users` ADD `avatar_img_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `bio`";

        foreach ($sqls as $sql) {
            $rs =  $GLOBALS['application']->conn->Execute($sql);
            if (!$rs) {
                $output->writeln("\t<error>[Database Error] Executing: ".$sql."</error>");
                return false;
            }
        }

        $output->writeln("\t<info>Updated users table successfully</info>");
    }

    protected function createAuthorsGroup($input, $output)
    {
        // Create authors group
        $sql = "INSERT INTO user_groups (`pk_user_group`, `name`) VALUES (3, 'autores')";
        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            $output->writeln("\t<error>[Database Error] Executing: ".$sql."</error>");
            return false;
        }

        $output->writeln("\t<info>Authors group created successfully</info>");
    }

    protected function addAuthorsOnUsers($input, $output)
    {
        // Fetch and store all users before inserts all authors
        $sql = 'SELECT * FROM `users`';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs !== false) {
            while (!$rs->EOF) {
                $user                = new \stdClass();
                $user->id            = $rs->fields['pk_user'];
                $user->username      = $rs->fields['username'];
                $user->password      = $rs->fields['password'];
                $user->sessionexpire = $rs->fields['sessionexpire'];
                $user->email         = $rs->fields['email'];
                $user->name          = $rs->fields['name'];
                $user->type          = $rs->fields['type'];
                $user->token         = $rs->fields['token'];
                $user->activated     = $rs->fields['activated'];
                $user->id_user_group = $rs->fields['fk_user_group'];


                $sqlAux = 'SELECT * FROM usermeta WHERE `user_id` ='.$user->id;

                $GLOBALS['application']->conn->fetchMode = ADODB_FETCH_ASSOC;
                $rsAux = $GLOBALS['application']->conn->Execute($sqlAux);

                foreach ($rsAux as $value) {
                    $user->meta[$rsAux->fields['meta_key']] = $rsAux->fields['meta_value'];
                }

                $allUsers[] = $user;

                $rs->MoveNext();
            }
        } else {
            $output->writeln("\t<error>[Database Error] Executing: ".$sql."</error>");
            return false;
        }

        // Delete all users
        $sql = "TRUNCATE TABLE `users`";
        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            $output->writeln("\t<error>[Database Error] Executing: ".$sql."</error>");
            return false;
        }

        // Change pk_user field to id
        $sql = "ALTER TABLE `users` CHANGE `pk_user` `id` INT (10) UNSIGNED NOT NULL AUTO_INCREMENT ";
        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            $output->writeln("\t<error>[Database Error] Executing: ".$sql."</error>");
            return false;
        }

        // Fetch all authors
        $sql = 'SELECT * FROM `authors`';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs !== false) {
            while (!$rs->EOF) {
                $author          = new \stdClass();
                $author->id      = $rs->fields['pk_author'];
                $author->fk_user = $rs->fields['fk_user'];
                $author->url     = $rs->fields['blog'];
                $author->name    = $rs->fields['name'];
                $author->bio     = $rs->fields['condition'];
                $author->params  = unserialize($rs->fields['params']);
                $allAuthors[] = $author;

                $rs->MoveNext();
            }
        } else {
            $output->writeln("\t<error>[Database Error] Executing: ".$sql."</error>");
            return false;
        }

        // Insert all authors in the users table
        foreach ($allAuthors as $author) {
            $sql = "INSERT INTO users "
                ."(`id`, `username`, `password`, `sessionexpire`, `url`, `bio`, `avatar_img_id`,"
                ."`email`, `name`,  `type`, `token`, `activated`, `fk_user_group`) "
                ."VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";

            // Get authors img id for $author['avatar_img_id'] and opinion fk_author_img
            $sqlPhoto = 'SELECT max(fk_photo) as img_id FROM author_imgs WHERE fk_author=?';
            $rsPhoto  = $GLOBALS['application']->conn->Execute($sqlPhoto, array($author->id));

            if (!$rsPhoto) {
                $output->writeln(
                    "\t<error>[Database Error] Fetching author image info: author id ".$author->id."</error>\n".
                    "\t\t<error>[Sql]".$sql."</error>\n"
                );
            }
            $avatar_img_id = $rsPhoto->fields['img_id'];
            if (!$avatar_img_id) {
                $avatar_img_id = 0;
            }

            $values = array(
                $author->id,
                'autor'.$author->id,
                md5(generateRandomString(15)),
                15,
                (!empty($author->url)) ? $author->url : '',
                (!empty($author->bio)) ? $author->bio : '',
                $avatar_img_id,
                'autor'.$author->id.'@opennemas.com',
                $author->name,
                0,
                null,
                0,
                3
            );

            if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                $output->writeln(
                    "\t<error>[Database Error] Inserting author: ".$author->id." -> ".$author->name."</error>\n".
                    "\t\t<error>[Sql]".$sql."</error>\n"
                );
                $output->writeln($GLOBALS['application']->conn->ErrorMsg());
            }

            // Update fk_author_img with avatar_img_id on opinions table
            $sqlOp = "UPDATE `opinions` SET `fk_author_img` =? WHERE `fk_author` =?";
            if ($GLOBALS['application']->conn->Execute($sqlOp, array($avatar_img_id, $author->id)) === false) {
                $output->writeln(
                    "\t<error>[Database Error] Updating fk_author_img on author: "
                    .$author->id." -> ".$author->name."</error>\n".
                    "\t\t<error>[Sql]".$sqlOp."</error>\n"
                );
            }
        }

        // Insert back all users without the old id
        foreach ($allUsers as $user) {
            $sql = "INSERT INTO users "
              ."(`username`, `password`, `sessionexpire`, `url`, `bio`, `avatar_img_id`,"
              ."`email`, `name`,  `type`, `token`, `activated`, `fk_user_group`) "
              ."VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

            $values = array(
                $user->username,
                $user->password,
                $user->sessionexpire,
                '',
                '',
                0,
                $user->email,
                $user->name,
                $user->type,
                $user->token,
                $user->activated,
                $user->id_user_group
            );

            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if ($rs === false) {
                $output->writeln(
                    "\t<error>[Database Error] Inserting user: ".$user->id." -> ".$user->name."</error>"
                );
            } else {
                // Get new user id
                $sql1 = "SELECT `id` FROM `users` WHERE `email` LIKE '%".$user->email."%'";
                $rs1  = $GLOBALS['application']->conn->Execute($sql1);

                if ($rs1 === false) {
                    $output->writeln(
                        "\t<error>[Database Error] Getting user id: ".$sql1."</error>"
                    );
                } else {
                    // Replace user id on user_content_categories table with new id
                    $id = $rs1->fields['id'];
                    $sql2 = "UPDATE `users_content_categories` SET `pk_fk_user`=? WHERE `pk_fk_user`=?";

                    $rs2 = $GLOBALS['application']->conn->Execute($sql2, array($id, $user->id));
                    if ($rs2 === false) {
                        $output->writeln(
                            "\t<error>[Database Error] Updating user categories: ".$sql2."</error>"
                        );
                    }

                    // Replace usermeta id for this user too
                    $sql3 = "UPDATE `usermeta` SET `user_id` =? WHERE `user_id`=?";
                    $rs3 = $GLOBALS['application']->conn->Execute($sql3, array($id, $user->id));
                    if ($rs3 === false) {
                        $output->writeln(
                            "\t<error>[Database Error] Updating user categories: ".$sql3."</error>"
                        );
                    }

                    // Replace content fk_author, fk_publisher and fk_user_last_editor for this user too
                    $sql4 = "UPDATE `contents` SET `fk_author` =? WHERE `fk_author` =?";
                    $sql5 = "UPDATE `contents` SET `fk_publisher` =? WHERE `fk_publisher` =?";
                    $sql6 = "UPDATE `contents` SET `fk_user_last_editor` =? WHERE `fk_user_last_editor` =?";
                    $rs4 = $GLOBALS['application']->conn->Execute($sql4, array($id, $user->id));
                    $rs5 = $GLOBALS['application']->conn->Execute($sql5, array($id, $user->id));
                    $rs6 = $GLOBALS['application']->conn->Execute($sql6, array($id, $user->id));
                    if ($rs4 === false) {
                        $output->writeln(
                            "\t<error>[Database Error] Updating user content info: ".$sql4."</error>"
                        );
                    }
                    if ($rs5 === false) {
                        $output->writeln(
                            "\t<error>[Database Error] Updating user content info: ".$sql5."</error>"
                        );
                    }
                    if ($rs6 === false) {
                        $output->writeln(
                            "\t<error>[Database Error] Updating user content info: ".$sql6."</error>"
                        );
                    }
                }
            }
        }

        foreach ($allAuthors as $author) {

            if (isset($author->params['twitter'])) {
                $sql1 = 'REPLACE INTO usermeta (`user_id`, `meta_key`, `meta_value`) VALUES (?, ?, ?)';
                $values1 = array(
                    $author->id,
                    'twitter',
                    $author->params['twitter']
                );
                $rs1 = $GLOBALS['application']->conn->Execute($sql1, $values1);

                if (!$rs1) {
                    $output->writeln(
                        "\t<error>[Database Error] Setting twitter to author: ".$author->id."</error>"
                    );
                }
            }

            if (isset($author->params['inrss'])) {
                $sql2 = 'REPLACE INTO usermeta (`user_id`, `meta_key`, `meta_value`) VALUES (?, ?, ?)';
                $values2 = array(
                    $author->id,
                    'inrss',
                    $author->params['inrss']
                );
                $rs2 = $GLOBALS['application']->conn->Execute($sql2, $values2);

                if (!$rs2) {
                    $output->writeln(
                        "\t<error>[Database Error] Setting inrss to author: ".$author->id."</error>"
                    );
                }
            }
        }

        $output->writeln("\t<info>Merged authors into users successfully</info>");
    }

    protected function deleteAuthorAndAuthorImgTables($input, $output)
    {
        // Delete authors table
        $sql1 = "DROP TABLE authors";
        // Delete author_imgs table
        $sql2 = "DROP TABLE author_imgs";
        // Delete fk_author_img_widget from opinions table
        $sql3 = "ALTER TABLE `opinions` DROP `fk_author_img_widget`";
        // Execute all sql's
        $rs1 = $GLOBALS['application']->conn->Execute($sql1);
        $rs2 = $GLOBALS['application']->conn->Execute($sql2);
        $rs3 = $GLOBALS['application']->conn->Execute($sql3);
        if (!$rs1) {
            $output->writeln("\t<error>[Database Error] Failed deleting: ".$sql1."</error>");
        }
        if (!$rs2) {
            $output->writeln("\t<error>[Database Error] Failed deleting: ".$sql2."</error>");
        }
        if (!$rs3) {
            $output->writeln("\t<error>[Database Error] Failed deleting: ".$sql3."</error>");
        }

        $output->writeln(
            "\t<info>Deleted authors, author_imgs tables and dropped fk_author_img_widget from opinions</info>".
            "\n\t<info>Finished script successfully</info>"
        );
    }
}
