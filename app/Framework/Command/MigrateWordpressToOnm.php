<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Migrate one Category between two Openemas
 *
 *
 **/
namespace Framework\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateOnmCategory extends Command
{


    public $originalCategories = array();

    public $categoriesOpinion = array();

    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('originDB', InputArgument::REQUIRED, 'originDB'),
                    new InputArgument('finalDB', InputArgument::REQUIRED, 'finalDB'),

                )
            )
            ->setName('migrate:wordpress')
            ->setDescription('Migrate a wordpress database to Openemas')
            ->setHelp(
                <<<EOF
The <info>migrate:wordpress</info> command migrates one wordpress DB to new openenmas database.

<info>php bin/console migrate:wordpress originDB finalDB</info>

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
        $dataBaseUser = 'root';

        $originDataBaseName = $input->getArgument('originDB');
        $dataBaseName       = $input->getArgument('finalDB');

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
        define('ORIGIN_BD_DATABASE', $originDataBaseName);

        include_once $basePath.'/app/autoload.php';
        include_once 'Application.php';

        // Initialize internal constants for logger
        // Logger in content class when creating widgets
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('INSTANCE_UNIQUE_NAME', 'opennemas');

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();
        \Application::initLogger();

        $GLOBALS['application']->connOrigin = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->connOrigin->Connect(BD_HOST, BD_USER, BD_PASS, ORIGIN_BD_DATABASE);

        $_SESSION['username'] = 'script';
        $_SESSION['userid'] = 11;
        // Execute functions
        $output->writeln("\t<fg=blue;bg=white>Migrating ".$originCategory.": ".$originDataBaseName."->". $finalCategory."-".$dataBaseName."</fg=blue;bg=white>");

        $this->importUsers($input, $output);
        $this->importCategories($input, $output);
        $this->importImages($input, $output);
        $this->importArticles($input, $output);

        $output->writeln(
            "\n\t ***Migration finished for Database: ".$dataBaseName."***"
        );
    }


    /**
     * Fetches the original categories
     *
     **/
    public function importCategories()
    {



        foreach( $this->originalCategories as $originalID => $newID ) {

            if ($this->elementIsImported($originalID, 'category') ) {
                 $output->writeln("Category with id {$originalID} already imported\n");

            } else {
                if($newID==0) {
                    $newID = $this->insertNewCategory($originalID);
                    $this->originalCategories[$originalID]= $newID;
                }
                 $output->writeln("Importing category with id {$originalID} - ");
                 $this->insertRefactorID($originalID, $newID, 'category');
                 $output->writeln("new id {$newID} [DONE]\n");
            }

        }



    }

    /**
     * Fetches the original categories
     *
     **/
    protected function getOriginalCategories()
    {

    }

    /**
     * Maches category
     *
     **/
    public function matchCategory($category)
    {


    }

    /**
     * Insert category data
     *
     * @return void
     **/

    protected function insertNewCategory($originalID)
    {

        $categories = self::$configuration['newCategories'];
        foreach($categories as $key=>$categoryData) {
            if($key == $originalID) {
                $elem= explode(':',$categoryData);

                $data = array(
                    'title'=> $elem[0],
                    'name' => (StringUtils::get_title( $this->convertoUTF8($elem[0]))),
                    'inmenu'=> 1,
                    'internal_category' => 0,
                    'subcategory' => $this->matchCategory($elem[1]),
                    );

                $data['params']['title']  = $elem[0];
                $data['params']['inrss']  = 1;

                $category = new ContentCategory();

                if($category->create( $data )) {
                    $this->insertRefactorID($originalID, $category->pk_content_category,'category');
                    $output->writeln("Creating category with id {$originalID} - newid: {$category->pk_content_category}");

                    return $category->pk_content_category;
                }
            }
        }
    }

    /**
     * Read articles data and insert in new database
     *
     * @return void
     **/
    protected function importArticles($input, $output)
    {

        $where = ' `wp_term_relationships`.`term_taxonomy_id` IN ('.implode(', ', array_keys($this->originalCategories)).") ";
        $limit = '';

        $sql = "SELECT * FROM `wp_posts`, `wp_term_relationships` WHERE ".
            "`post_type` = 'post' AND `ID`=`object_id` AND post_status='publish' ".
            " AND ".$where." ".$limit;

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs = $GLOBALS['application']->connOrigin->Execute($request);

        if (!$rs) {
            $output->writeln('DB problem: '. $GLOBALS['application']->connOrigin->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current = 1;

            while (!$rs->EOF) {

                if ($this->elementIsImported($rs->fields['ID'], 'article') ) {
                     $output->writeln("[{$current}/{$totalRows}] Article with id {$rs->fields['ID']} already imported\n");
                } else {
                    $output->writeln("[{$current}/{$totalRows}] Importing article with id {$rs->fields['ID']} - ");

                    $originalArticleID = $rs->fields['ID'];
                    $data = $this->clearLabelsInBodyArticle($rs->fields['post_content']);
                    $values = array(
                            'title' => $this->convertoUTF8($rs->fields['post_title']),
                            'category' => $this->matchCategory($rs->fields['term_taxonomy_id']),
                            'with_comment' => 1,
                            'available' => 1,
                            'content_status' => 0,
                            'frontpage' => 0,
                            'in_home' => 0,
                            'title_int' => $this->convertoUTF8($rs->fields['post_title']),
                            'metadata' => StringUtils::get_tags($this->convertoUTF8($rs->fields['post_title'])),
                            'subtitle' => '',
                            'agency' => self::$configuration['agency']['name'],
                            'summary' => substr($data['body'],0, 250 . '...'),
                            'description' => strip_tags(substr($data['body'], 0,150)),
                            'body' => $data['body'],
                            'posic' => 0,
                            'id' => 0,
                            'img1' =>$data['img'],
                            'img2' =>$data['img'],
                            'created' => $rs->fields['post_date'],
                            'starttime' => $rs->fields['post_date'],
                            'changed' => $rs->fields['post_modified'],
                            'fk_user' => self::$configuration['idUser']['id'],
                            'fk_publisher' => self::$configuration['idUser']['id'],
                        );

                    $article = new Article();
                    $newArticleID = $article->create($values);

                    if (!empty($articleID)) {
                        $this->insertRefactorID($originalArticleID, $newArticleID,'article');
                        $output->writeln('-'. $originalArticleID.'->'. $newArticleID. ' article ok');
                    } else {
                        $output->writeln('Problem inserting article '.$originalArticleID.' - '.$title .'\n');
                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $output->writeln('Imported  '.$current.' articles \n');
        }
        $rs->Close();
        return true;
    }

    /**
     * Read images data and insert this in new database
     *
     * @return void
     **/
    protected function importImages($input, $output)
    {

        $sql = "SELECT * FROM `wp_posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs = $GLOBALS['application']->connOrigin->Execute($request);

        $IDCategory=1; //assign category for media elements

        if (!$rs) {
            $output->writeln(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            $photo = new \Photo();

            while (!$rs->EOF) {
                if ($this->elementIsImported($rs->fields['ID'], 'image')) {
                    $output->writeln("[{$current}/{$totalRows}] Image already imported");
                } else {

                    $originalImageID = $rs->fields['ID'];

                    ///http://mundiario.com/wp-content/uploads/2013/06/Brasil-360x225.png
                    $local_file = str_replace(ORIGINAL_MEDIA,'',$rs->fields['guid']);
                  //  $local_file = str_replace(self::$originMedia['originUrl'],'',$rs->fields['guid']);

                    $imageData = array(
                            'title' => $this->convertoUTF8(strip_tags($rs->fields['post_title'])),
                            'category' => $IDCategory,
                            'fk_category' => $IDCategory,
                            'category_name'=> '',
                            'content_status' => 1,
                            'frontpage' => 0,
                            'in_home' => 0,
                            'metadata' => StringUtils::get_tags($this->convertoUTF8($rs->fields['post_title'].$rs->fields['post_excerpt'])),
                            'description' => $this->convertoUTF8(strip_tags(substr($rs->fields['post_excerpt'], 0, 150))),
                            'id' => 0,
                            'created' => $rs->fields['post_date'],
                            'starttime' => $rs->fields['post_date'],
                            'changed' => $rs->fields['post_modified'],
                            'fk_user' => self::$configuration['idUser']['id'],
                            'fk_author' => self::$configuration['idUser']['id'],
                            'local_file' => self::$originMedia.$local_file,
                        );

                    $imageID = $photo->create($imageData);

                    if (!empty($imageID)) {
                        $this->insertRefactorID($rs->fields['pk_content'], $imageID, 'image');
                        //$this->updateFields('`available` ='.$rs->fields['available'], $rs->fields['pk_content']);
                        $output->writeln('- Image '. $imageID. ' ok');
                    } else {
                        $output->writeln('Problem inserting image '.$rs->fields['pk_content'].' - '.$rs->fields['title'] .'\n');
                    }
                }
                $current++;
                $rs->MoveNext();
            }

            $rs->Close();
        }
    }

    /**
     *  insert the correspondence between identifiers
     *
     * @return void
     **/
    protected function insertRefactorID($contentID, $newID, $type)
    {
        $sql = 'INSERT INTO translation_ids (`pk_content_old`, `pk_content`, `type`)
                VALUES (?, ?, ?)';
        $values = array($contentID, $newID, $type);

        $stmt = $GLOBALS['application']->conn->Prepare($sql);
        $rss = $GLOBALS['application']->conn->Execute($stmt, $values);

        if (!$rss) {
            //$output->writeln('\n insertRefactorID: '.$GLOBALS['application']->conn->ErrorMsg());
        }

    }

    /**
     * Read the correspondence between identifiers
     *
     * @return void
     **/
    protected function elementIsImported($contentID, $contentType)
    {
        if (isset($contentID) && isset($contentType)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=?';

            $values = array($contentID, $contentType);

            $stmt = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($stmt, $values);

            if (!$rss) {
                //$output->writeln($GLOBALS['application']->conn->ErrorMsg());
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            //$output->writeln("There is imported {$contentID} - {$contentType}\n.");
        }
    }

    /**
     * update some fields in content table
     *
     * @param int $contentId the content id
     * @param string $params new values for the content table
     *
     * @return void
     **/
    protected  function updateFields($contentID, $params)
    {
        if (isset($contentID) && isset($params)) {
            $sql = 'UPDATE `contents` SET {$params}  WHERE pk_content=?';
            $values = array($params, $contentID);

            $stmt = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($stmt, $values);
            if (!$rss) {
                //$output->writeln($GLOBALS['application']->conn->ErrorMsg());
            }

        } else {
            //$output->writeln("Please provide a contentID and views to update it.");
        }
    }

    /**
     * Converts a given string to UTF-8 codification
     *
     * @return string
     **/
    protected public function convertoUTF8($string)
    {
        return mb_convert_encoding($string, 'UTF-8');
    }

    /**
     * Read users data and insert this in new database
     *
     * @return void
     **/
    protected function importUsers($input, $output)
    {

        $sql = "SELECT `ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, "
            ." `user_url`, `user_registered`, `user_activation_key`, `user_status`, "
            ." `display_name`, `spam`, `deleted` FROM `wp_users` ";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs = $GLOBALS['application']->connOrigin->Execute($request);

        $IDCategory=1; //assign category for media elements

        if (!$rs) {
            $output->writeln(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            $user = new \User();

            while (!$rs->EOF) {
                if ($this->elementIsImported($rs->fields['ID'], 'user')) {
                    $output->writeln("[{$current}/{$totalRows}] Image already imported");
                } else {

                    $originalID = $rs->fields['ID'];

                    $data = array(
                        'username' => $rs->fields['user_nicename'],
                        'password' => $rs->fields['user_pass'],
                        'sessionexpire' => $rs->fields['user_login'],
                        'email' => $rs->fields['user_email'],
                        'name' => $rs->fields['user_login'],
                        'type' => '',
                        'deposit' => '',
                        'token' => '',
                        'activated' => $rs->fields['user_status'],
                        'fk_user_group' => '',
                        'avatar_img_id' => '',
                        'bio' => $rs->fields['user_login'],
                    );

                    $userID = $user->create($data);

                    if (!empty($userID)) {
                        $this->insertRefactorID($originalID, $userID, 'user');
                        $output->writeln('- User '. $userID. ' ok');
                    } else {
                        $output->writeln('Problem inserting image '.$originalID.' - '.$rs->fields['user_login'] .'\n');
                    }
                }
                $current++;
                $rs->MoveNext();
            }

            $rs->Close();
        }
    }
}
