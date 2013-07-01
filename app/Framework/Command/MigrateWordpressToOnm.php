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

class MigrateWordpressToOnm extends Command
{

    protected $originalCategories = array();

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
        $output->writeln("\t<fg=blue;bg=white>Migrating: ".$originDataBaseName."->".$dataBaseName."</fg=blue;bg=white>");

        $this->output = $output;

        $this->prepareDatabase();

        $this->importUsers();

     /*   $this->importCategories();
        $this->loadCategories();

        $this->importImages();
        $this->importArticles();

        $this->importGalleries();
         $this->importVideos();
*/

        $output->writeln(
            "\n\t ***Migration finished for Database: ".$dataBaseName."***"
        );
    }

    protected function prepareDatabase()
    {

        $sql = "ALTER TABLE `translation_ids` ".
            "ADD `slug`  VARCHAR( 200 ) NOT NULL DEFAULT  '' ";

        $rss = $GLOBALS['application']->conn->Execute($sql);

    }


    /**
     * Read users data and insert this in new database
     *
     * @return void
     **/
    protected function importUsers()
    {
        $sql2 = "SELECT umeta_id, user_id, meta_key, meta_value  FROM `wp_usermeta` ".
            "WHERE  meta_key IN ('first_name', 'last_name', 'description', 'userphoto_image_file','twitter' )";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql2);
        $rs2 = $GLOBALS['application']->connOrigin->Execute($request);

        $usersMeta = $rs2->getArray();
        $data = array();
        foreach ($usersMeta as $user) {
            var_dump($user);
            $userID = $user['user_id'];
            $key = $user['meta_key'];
            $data[$userID][$key] = $user['meta_value'];
        }


        $sql = "SELECT `ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, "
            ." `user_url`, `user_registered`, `user_activation_key`, `user_status`, "
            ." `display_name`, `spam`, `deleted` FROM `wp_users` ";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs = $GLOBALS['application']->connOrigin->Execute($request);

        $IDCategory=1; //assign category for media elements

        if (!$rs) {
            $this->output->writeln(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            $user = new \User();

            while (!$rs->EOF) {
                if ($this->elementIsImported($rs->fields['ID'], 'user')) {
                    $this->output->writeln("[{$current}/{$totalRows}] Image already imported");
                } else {

                    $originalID = $rs->fields['ID'];

                    $data = array(
                        'username' => $rs->fields['user_nicename'],
                        'password' => $rs->fields['user_pass'],
                        'sessionexpire' =>'30',
                        'email' => $rs->fields['user_email'],
                        'name' => $data[$originalID]['first_name']." ".$data[$originalID]['last_name'],
                        'type' => '',
                        'deposit' => '',
                        'token' => '',
                        'activated' => $rs->fields['user_status'],
                        'fk_user_group' => '',
                        'avatar_img_id' => $data[$originalID]['userphoto_image_file'],
                        'bio' => $data[$originalID]['description'],
                        'url' => $rs->fields['user_url'],
                    );

                    $userID = $user->create($data);
                    $user->setMeta(array('twitter' => $data[$originalID]['twitter']));

                    if (!empty($userID)) {
                        $this->insertRefactorID($originalID, $userID, 'user');
                        $this->output->writeln('- User '. $userID. ' ok');
                    } else {
                        $this->output->writeln('Problem inserting image '.$originalID.' - '.$rs->fields['user_login'] .'\n');
                    }
                }
                $current++;
                $rs->MoveNext();
            }

            $rs->Close();
        }
    }

    protected function importCategories()
    {

        $rs = self::$originConn->Execute("SELECT * FROM wp_terms, wp_term_taxonomy ".
                "WHERE wp_terms.term_id = wp_term_taxonomy.term_id AND taxonomy='category'");

        $categories = $rs->getArray();

        foreach ($categories as $category) {

            $originalID = $category['term_id'];

            if ($this->elementIsImported($originalID, 'category')) {
                 $this->output->writeln("Category with id {$originalID} already imported\n");
            } else {
                $data = array(
                    'name'              => $category['slug'],
                    'title'             => $category['name'],
                    'inmenu'            => 1,
                    'subcategory'       => 0,
                    'internal_category' => 0,
                    'color'             => '',
                    'params'            => array(
                        'title'         => $elem[0],
                        'inrss'         => 1,
                    ),
                );

                $sql = "INSERT INTO content_categories
                    (`name`, `title`,`inmenu`,`fk_content_category`,
                    `internal_category`, `logo_path`,`color`, `params`)
                    VALUES (?,?,?,?,?,?,?,?)";
                $values = array(
                    $data['name'],
                    $data['title'],
                    $data['inmenu'],
                    $data['subcategory'],
                    $data['internal_category'],
                    $data['logo_path'],
                    $data['color'],
                    serialize($data['params']),
                );

                $rs = $GLOBALS['application']->conn->Execute($sql, $values);

                $newID = $GLOBALS['application']->conn->Insert_ID();

                $this->output->writeln("Importing category with id {$originalID} - ");
                $this->insertRefactorID($originalID, $newID, 'category', $category['slug']);
                $this->output->writeln("new id {$newID} [DONE]\n");
            }
        }

        return $this;
    }

    /**
     * Fetches the original categories
     *
     **/
    protected function loadCategories()
    {
        $this->categories = array();

        $sql = "SELECT * FROM translation_ids WHERE type='category'";

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        $categories = $rs->GetArray();

        foreach ($categories as $category) {
            $this->categories[$category['pk_content_old']] = $category['pk_content'];
        }

        return $this;
    }

    protected function matchCategory($categoryId)
    {
        return $this->categories[$categoryId];
    }

    /**
     * Read articles data and insert in new database
     *
     * @return void
     **/
    protected function importArticles()
    {

        $where = ' `wp_term_relationships`.`term_taxonomy_id` IN ('.implode(', ', array_keys($this->originalCategories)).") ";
        $limit = '';

        $sql = "SELECT * FROM `wp_posts`, `wp_term_relationships` WHERE ".
            "`post_type` = 'post' AND `ID`=`object_id` AND post_status='publish' ".
            " AND ".$where." ".$limit;

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs = $GLOBALS['application']->connOrigin->Execute($request);

        if (!$rs) {
            $this->output->writeln('DB problem: '. $GLOBALS['application']->connOrigin->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current = 1;

            while (!$rs->EOF) {
                $originalArticleID = $rs->fields['ID'];
                if ($this->elementIsImported($originalArticleID, 'article') ) {
                     $this->output->writeln("[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n");
                } else {
                    $this->output->writeln("[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ");

                    $data = $this->clearLabelsInBodyArticle($rs->fields['post_content']);
                    if (!empty($rs->fields['post_excerpt'])) {
                        $summary = $this->convertoUTF8($rs->fields['post_excerpt']);
                    } else {
                        $summary = $this->convertoUTF8(strip_tags(substr($data['body'], 0, 250)));
                    }
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
                        'slug' => $rs->fields['post_name'],
                        'agency' => self::$configuration['agency']['name'],
                        'summary' => $summary,
                        'description' => strip_tags(substr($summary, 0,150)),
                        'body' => $data['body'],
                        'posic' => 0,
                        'id' => 0,
                        'img1' =>$data['img'],
                        'img2' =>$data['img'],
                        'created' => $rs->fields['post_date_gmt'],
                        'starttime' => $rs->fields['post_date_gmt'],
                        'changed' => $rs->fields['post_modified'],
                        'fk_user' => $this->matchCategory($rs->fields['post_author']),
                        'fk_publisher' => $this->matchCategory($rs->fields['post_author']),
                    );

                    $article = new Article();
                    $newArticleID = $article->create($values);

                    if (!empty($articleID)) {
                        $this->insertRefactorID($originalArticleID, $newArticleID,'article', $rs->fields['post_name']);
                        $this->output->writeln('-'. $originalArticleID.'->'. $newArticleID. ' article ok');
                    } else {
                        $this->output->writeln('Problem inserting article '.$originalArticleID.' - '.$title .'\n');
                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $this->output->writeln('Imported  '.$current.' articles \n');
        }
        $rs->Close();
        return true;
    }

    /**
     * Read images data and insert this in new database
     *
     * @return void
     **/
    protected function importImages()
    {

        $sql = "SELECT * FROM `wp_posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs = $GLOBALS['application']->connOrigin->Execute($request);

        $IDCategory=1; //assign category for media elements

        if (!$rs) {
            $this->output->writeln(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            $photo = new \Photo();

            while (!$rs->EOF) {
                if ($this->elementIsImported($rs->fields['ID'], 'image')) {
                    $this->output->writeln("[{$current}/{$totalRows}] Image already imported");
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
                        $this->output->writeln('- Image '. $imageID. ' ok');
                    } else {
                        $this->output->writeln('Problem inserting image '.$rs->fields['pk_content'].' - '.$rs->fields['title'] .'\n');
                    }
                }
                $current++;
                $rs->MoveNext();
            }

            $rs->Close();
        }
    }

    protected function importGalleries()
    {
         /*[gallery link="file" ids="8727,8728,8729,8730,8731,8732"]*/
    }


     protected function importVideos()
    {
        //wp_postmeta  -> meta_key = usn_videolink
    }

    protected function importComments()
    {

        echo "IMPORTING COMMENTS\n";

        $sql = "SELECT * FROM `wp_comments`, `wp_posts` WHERE ".
        " `comment_approved`=1 AND `ID`=`comment_post_ID` ";

        // Fetch the list of Opinions available for one author in EditMaker
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);


        if(!$rs) {
            ImportHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                $originalCommentID = $rs->fields['comment_ID'];

                if ($ih->elementIsImported($originalCommentID, 'comment') ) {
                    echo "[{$current}/{$totalRows}] Comments with id {$originalCommentID} already imported\n";
                } else {

                    $articleID =$ih->elementIsImported($rs->fields['comment_post_ID'], 'article');
                    $ccm = ContentCategoryManager::get_instance();

                    if(!empty($articleID)) {
                        $article= new Article($articleID);
                        if(!empty($article->pk_content)) {

                        echo "[{$current}/{$totalRows}] Importing comment with id {$originalCommentID} - ";

                        $comment = ImportHelper::convertoUTF8(strip_tags($rs->fields['comment_content']));
                        $title =  ImportHelper::convertoUTF8(substr($comment,0,100));
                        $data =
                            array(
                                'title' => $title,
                                'category' => $article->category,
                                'category_name' =>$article->loadCategoryName($articleID),
                                'body' =>  strip_tags($rs->fields['comment_content']),
                                'metadata' => StringUtils::get_tags($title),
                                'description' => substr($comment, 0, 150),
                                'content_status' => 1,
                                'created' => $rs->fields['comment_date'],
                                'starttime' => $rs->fields['comment_date'],
                                'changed' => $rs->fields['comment_date'],
                                'published' => $rs->fields['comment_date'],
                                'fk_publisher' => self::$configuration['idUser']['id'],
                                'fk_user' => self::$configuration['idUser']['id'],
                                'author' => ImportHelper::convertoUTF8(strip_tags($rs->fields['comment_author'])),
                                'ip'=>$rs->fields['comment_author_IP'],
                                'email'=>ImportHelper::convertoUTF8($rs->fields['comment_author_email']),
                                'ciudad'=>'',
                            );

                        $values = array(
                            'id' =>   $articleID,
                            'data' => $data,
                            'ip' =>   $rs->fields['comment_author_IP'],
                        );

                        $comment = new Comment();
                        $newCommentID = $comment->create($values);

                        if(is_string($newCommentID)) {

                            $ih->logElementInsert($originalCommentID, $newCommentID, 'comment');

                    //     $ih->updateCreateDate($newOpinionID, $rs->fields['fecha']);

                        }
                        echo "new id {$newCommentID} [DONE]\n";
                        }
                    }

                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }
    }


    /**
     *  insert the correspondence between identifiers
     *
     * @return void
     **/
    protected function insertRefactorID($contentID, $newID, $type, $slug="")
    {
        $sql = 'INSERT INTO translation_ids (`pk_content_old`, `pk_content`, `type`, `slug`)
                VALUES (?, ?, ?, ?)';
        $values = array($contentID, $newID, $type, $slug);

        $stmt = $GLOBALS['application']->conn->Prepare($sql);
        $rss = $GLOBALS['application']->conn->Execute($stmt, $values);

        if (!$rss) {
            $this->output->writeln('\n insertRefactorID: '.$GLOBALS['application']->conn->ErrorMsg());
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
                $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            //$this->output->writeln("There is imported {$contentID} - {$contentType}\n.");
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
                $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
            }

        } else {
            //$this->output->writeln("Please provide a contentID and views to update it.");
        }
    }

    /**
     * Clear body for
     *
     * @return string
     **/

    protected function getOnmIdImage($guid) {
        $sql = "SELECT ID FROM `wp_posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ".
            " AND guid= '".$guid."'";


        // Fetch the list of Opinions available for one author in EditMaker
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        $imageID='';
        if(!$rs) {
            $this->output->writeln('- User '. $userID. ' ok');
        } else {
            $imageID = $this->elementIsImported($rs->fields['ID'], 'image');

        }
        return $imageID;

    }

    protected function clearLabelsInBodyArticle($body) {

        /*[gallery link="file" ids="8727,8728,8729,8730,8731,8732"]
        [caption id="attachment_9084" align="alignnone" width="400"]<a href="http://mundiario.com/wp-content/uploads/2013/06/alicante-2.jpg"><img class="size-full wp-image-9084" alt="EstaciÃ³n de Alicante, desde ahora enlazada por AVE." src="http://mundiario.com/wp-content/uploads/2013/06/alicante-2.jpg" width="400" height="225" /></a> EstaciÃ³n de Alicante, desde ahora enlazada por AVE.[/caption]
        <a href="http://mundiario.com/wp-content/uploads/2013/05/368875884_b4b5266888_z.jpg"><img class="alignnone size-medium wp-image-7398" alt="Angela Merkel - World Economic Forum Annual Meeting Davos 2007" src="http://mundiario.com/wp-content/uploads/2013/05/368875884_b4b5266888_z-420x275.jpg" width="420" height="275" /></a>
        */
        $result = array();
        #Deleted [caption id="attachment_2302" align="aligncenter" width="300" caption="El partido ultra Jobbik siembra el terror entre las minorías y los extranjeros en Hungría."][/caption]

        $newBody='';
        $img='';
        preg_match_all('/(<a .*?href="(.+?)".*?><img .*?src=".+?".*?><\/a>)/', $body, $result);
        if(!empty($result)) {
            $guid = $result[2][0];

            $newBody =preg_replace('/(<a .*?href="(.+?)".*?><img .*?src=".+?".*?><\/a>)/', '', $body);
            $newBody = ImportHelper::convertoUTF8(strip_tags($newBody, '<p><a><br>'));
            $img = $this->getOnmIdImage($guid);
        }
        $newBody= preg_replace('/(\[caption .*?\]\[\/caption\])/', '', $newBody);
        return array('img'=>$img, 'body'=>$newBody);

    }

    /**
     * Converts a given string to UTF-8 codification
     *
     * @return string
     **/
    protected function convertoUTF8($string)
    {
        return mb_convert_encoding($string, 'UTF-8');
    }
}

