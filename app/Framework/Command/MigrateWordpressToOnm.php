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
use Onm\Settings as s;

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

        $validator = function($value) {
            if (trim($value) == '') {
                throw new \Exception('The password can not be empty');
            }
        };

        $dataBasePass = $password = $dialog->askHiddenResponse(
            $output,
            'What is the database user password?',
            false
        );

        $dbPrefix = $dialog->ask(
            $output,
            'What is the prefix in database tables (Ex: wp_2_)?',
            'wp_'
        );
        $output->writeln("-: ".$dbPrefix);

        $originalUrl = $dialog->ask(
            $output,
            'What is the wordpress site URL?',
            'http://www.mundiario.com'
        );
        $output->writeln("-: ".$originalUrl);

        $originalDirectory = $dialog->ask(
            $output,
            'Where is the wordpress media directory?',
            '/opt/backup_opennemas/mundiario/wp-content/uploads/'
        );
        $output->writeln("-: ".$originalDirectory);

        $instanceName = $dialog->ask(
            $output,
            'Where is the instance name?',
            'mundiario'
        );
        $output->writeln("-: ".$instanceName);

        define('ORIGINAL_URL', $originalUrl);
        define('ORIGINAL_MEDIA', $originalDirectory);
        define('ORIGINAL_MEDIA_COMMON', '/opt/backup_opennemas/mundiario/wp-content/uploads/');

        define('CACHE_PREFIX', 'wordpress');
        define('BD_HOST', $dataBaseHost);
        define('BD_USER', $dataBaseUser);
        define('BD_PASS', $dataBasePass);
        define('BD_TYPE', $dataBaseType);
        define('BD_DATABASE', $dataBaseName);
        define('ORIGIN_BD_DATABASE', $originDataBaseName);
        define('PREFIX', $dbPrefix);

        // Initialize internal constants for logger
        // Logger
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('INSTANCE_UNIQUE_NAME', $instanceName);

        define('IMG_DIR', "images");
        define('MEDIA_PATH', SITE_PATH."media".DS.INSTANCE_UNIQUE_NAME);

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();
        \Application::initLogger();


        $GLOBALS['application']->connOrigin = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->connOrigin->Connect(BD_HOST, BD_USER, BD_PASS, ORIGIN_BD_DATABASE);

        $_SESSION['username'] = 'script';
        $_SESSION['userid']   = 11;
        // Execute functions
        $output->writeln("\t<fg=blue>Migrating: ".$originDataBaseName."->".$dataBaseName."</fg=blue>");

        $this->output = $output;

        $this->prepareDatabase();

        $this->importUsers();

        $this->importCategories();
        $this->loadCategories();

        if ($dbPrefix != 'wp_') {
            $this->importImages('wp_');
        }
        $this->importImages();

        $this->importArticles();

        $this->importGalleries();

        $output->writeln(
            "\n\t ***Migration finished for Database: ".$dataBaseName."***"
        );
        $this->printResults();
    }

    protected function prepareDatabase()
    {

        $sql = "ALTER TABLE `translation_ids` ".
            "ADD `slug`  VARCHAR( 200 ) NOT NULL DEFAULT  '' ";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        $sql = "INSERT INTO user_groups (`pk_user_group`, `name`) VALUES (3, 'autores')";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        $sql="DELETE FROM `wp-mundiario`.`wp_users` WHERE `wp_users`.`user_login` = 'macada'";
        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
    }


    /**
     * Read users data and insert this in new database
     *
     * @return void
     **/
    protected function importUsers()
    {
        $sql2 = "SELECT umeta_id, user_id, meta_key, meta_value  FROM `wp_usermeta` ".
            "WHERE  meta_key IN ('first_name', 'last_name', 'description', ".
            "'wp_biographia_short_bio', 'userphoto_image_file','twitter' )";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql2);
        $rs2     = $GLOBALS['application']->connOrigin->Execute($request);

        $usersMeta = $rs2->getArray();
        $data      = array();
        foreach ($usersMeta as $user) {
            $userID = $user['user_id'];

            $key                 = $user['meta_key'];
            $data[$userID][$key] = $user['meta_value'];

        }

        $sql = "SELECT `ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, "
            ." `user_url`, `user_registered`, `user_activation_key`, `user_status`, "
            ." `display_name`, `spam`, `deleted` FROM `wp_users` ";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);

        $IDCategory = 1; //assign category for media elements

        if (!$rs) {
            $this->output->writeln($GLOBALS['application']->connOrigin->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current   = 1;


            while (!$rs->EOF) {
                if (1!=1 && $this->elementIsImported($rs->fields['ID'], 'user')) {
                    $this->output->writeln("[{$current}/{$totalRows}] user already imported");
                } else {
                    $originalID = $rs->fields['ID'];
                    $photoId    = '';

                    if (!empty($data[$originalID]['userphoto_image_file'])
                         && !is_null($data[$originalID]['userphoto_image_file'])) {

                        $file    = ORIGINAL_MEDIA.'files/'.$data[$originalID]['userphoto_image_file'];
                        $photoId = $this->uploadUserAvatar($file, $rs->fields['user_nicename']);
                        if (empty($photoId)) {
                            $file    = ORIGINAL_MEDIA_COMMON.'userphoto/'.$data[$originalID]['userphoto_image_file'];
                            $photoId = $this->uploadUserAvatar($file, $rs->fields['user_nicename']);
                        }
                    }

                    $values = array(
                        'username'      => $rs->fields['user_nicename'],
                        'password'      => $rs->fields['user_pass'],
                        'sessionexpire' =>'30',
                        'email'         => $rs->fields['user_email'],
                        'name'          => $data[$originalID]['first_name']." ".$data[$originalID]['last_name'],
                        'type'          => 1,
                        'deposit'       => '',
                        'token'         => '',
                        'activated'     => 1,
                        'fk_user_group' => '',
                        'avatar_img_id' => $photoId,
                        'bio'           => $data[$originalID]['wp_biographia_short_bio'],
                        'url'           => $rs->fields['user_url'],
                        'id_user_group' => array('3'),
                    );

                    try {
                        $user   = new \User();
                        $user->create($values);
                        $userID = $user->id;

                        if (!empty($userID)) {
                            $user->updateUserPassword($userID, $rs->fields['user_pass']);
                            if (isset($data[$originalID])
                                && isset($data[$originalID]['twitter'])
                                && !empty($data[$originalID]['twitter'])) {
                                $user->setMeta(array('twitter' => $data[$originalID]['twitter']));
                            }
                            if (isset($data[$originalID])
                                && isset($data[$originalID]['description'])) {
                                    $user->setMeta(array('bio_description' => $data[$originalID]['description']));
                            }

                            $this->insertRefactorID($originalID, $userID, 'user', $rs->fields['user_login']);
                    //        $this->output->writeln('- User '. $userID. ' ok');
                        } else {
                            $this->output->writeln('Problem inserting id'.$originalID.'-'.$rs->fields['user_login'] .'\n');
                        }
                    } catch (\Exception $e) {

                    }
                }
                $current++;
                $rs->MoveNext();
            }

            $rs->Close();
            $this->output->writeln("Importer Users Finished");
        }
    }

    protected function importCategories()
    {

        $sql = "SELECT * FROM ".PREFIX."terms, ".PREFIX."term_taxonomy ".
               "WHERE ".PREFIX."terms.term_id = ".PREFIX."term_taxonomy.term_id AND taxonomy='category'";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs = $GLOBALS['application']->connOrigin->Execute($request);

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
                    'internal_category' => 1,
                    'logo_path'         => '',
                    'color'             => '',
                    'params'            => array(
                        'title'         => $category['name'],
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

                $rs    = $GLOBALS['application']->conn->Execute($sql, $values);
                $newID = $GLOBALS['application']->conn->Insert_ID();

                $this->output->writeln("Importing category with id {$originalID} - ");
                $this->insertRefactorID($originalID, $newID, 'category', $category['slug']);
              //  $this->output->writeln("new id {$newID} [DONE]\n");
            }

        }
        $this->output->writeln("Importer Categories Finished");
        return $this;
    }

    /**
     * Fetches the original categories
     *
     **/
    protected function loadCategories()
    {
        $this->categories = array();

        $sql = "SELECT * FROM translation_ids WHERE type ='category'";

        $rs = $GLOBALS['application']->conn->Execute($sql);

        $categories = $rs->GetArray();

        foreach ($categories as $category) {
            $this->categories[$category['pk_content_old']] = $category['pk_content'];
            $this->originalCategories[] = $category['pk_content_old'];
        }

        return $this;
    }

    protected function matchCategory($categoryId)
    {
        if (array_key_exists($categoryId, $this->categories)) {
            return $this->categories[$categoryId];
        } else {
            return 20;
        }
    }

    /**
     * Read articles data and insert in new database
     *
     * @return void
     **/
    protected function importArticles()
    {

        $where = " `".PREFIX."term_relationships`.`term_taxonomy_id` IN (".implode(', ', array_keys($this->originalCategories)).") ";
        $limit = '';

        $sql = "SELECT * FROM `".PREFIX."posts`, `".PREFIX."term_relationships` WHERE ".
            "`post_type` = 'post' AND `ID`=`object_id` AND post_status='publish' ".
            " AND ".$where." ".$limit;

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);

        if (!$rs) {
            $this->output->writeln('DB problem: '. $GLOBALS['application']->connOrigin->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current   = 1;

            while (!$rs->EOF) {
                $originalArticleID = $rs->fields['ID'];
                if ($this->elementIsImported($originalArticleID, 'article') ) {
                     $this->output->writeln("[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n");
                } else {
                   // $this->output->writeln("[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ");

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
                        'content_status' => 1,
                        'frontpage' => 0,
                        'in_home' => 0,
                        'title_int' => $this->convertoUTF8($rs->fields['post_title']),
                        'metadata' => \Onm\StringUtils::get_tags($this->convertoUTF8($rs->fields['post_title'])),
                        'subtitle' => '',
                        'slug' => $rs->fields['post_name'],
                        'agency' => '',
                        'summary' => $summary,
                        'description' => strip_tags(substr($summary, 0,150)),
                        'body' => $data['body'],
                        'posic' => 0,
                        'id' => 0,
                        'img1' =>$data['img'],
                        'img2' =>$data['img'],
                        'fk_video' => '',
                        'fk_video2' => '',
                        'footer_video2' => '',
                        'created' => $rs->fields['post_date_gmt'],
                        'starttime' => $rs->fields['post_date_gmt'],
                        'changed' => $rs->fields['post_modified_gmt'],
                        'fk_user' => $this->elementIsImported($rs->fields['post_author'], 'user'),
                        'fk_author' => $this->elementIsImported($rs->fields['post_author'], 'user'),
                        'fk_publisher' => $this->elementIsImported($rs->fields['post_author'], 'user'),
                        'fk_publisher' => $this->elementIsImported($rs->fields['post_author'], 'user'),
                    );

                    $article      = new \Article();
                    $newArticleID = $article->create($values);

                    if (!empty($newArticleID)) {
                        $this->insertRefactorID($originalArticleID, $newArticleID, 'article', $rs->fields['post_name']);
                    //  $this->output->writeln('-'. $originalArticleID.'->'.
                    //         $newArticleID. ' article ok');
                    $this->output->write('.');
                    } else {
                        $this->output->writeln('Problem inserting article '.$originalArticleID.
                            ' - '. $rs->fields['post_name'] .'\n');
                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $this->output->writeln('Imported  '.$current.' articles \n');

        $rs->Close();
        }
        return true;
    }

    /**
     * Read images data and insert this in new database
     *
     * @return void
     **/
    protected function importImages($prefix=null)
    {
        if (empty($prefix)) {
           $prefix = PREFIX;
        }
        $settings = array( 'image_thumb_size'=>'140',
                            'image_inner_thumb_size'=>'470',
                            'image_front_thumb_size'=>'350');
        foreach ($settings as $key => $value) {
            s::set($key, $value);
        }
        $sql = "SELECT * FROM `".$prefix."posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);

        $oldID = $this->elementIsImported('fotos', 'category');
        if (empty($oldID)) {
            $IDCategory ='1'; //fotografias
        } else {
           $IDCategory = $this->matchCategory($oldID); //assign category 'Fotos' for media elements
        }
        if (!$rs) {
            $this->output->writeln($GLOBALS['application']->connOrigin->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current   = 1;
            $photo     = new \Photo();

            while (!$rs->EOF) {
                if(!empty($rs->fields['guid'])) {
                    if ($this->elementIsImported($rs->fields['ID'], 'image')) {
                        $this->output->writeln("[{$current}/{$totalRows}] Image already imported");
                    } else {

                        $originalImageID = $rs->fields['ID'];

                        ///http://mundiario.com/wp-content/uploads/2013/06/Brasil-360x225.png
                        //http://mundiario.com/galicia/files/2013/07/6696140347_824d45603a_z-360x225.jpg
                        //http://mundiario.com/emprendedores/files/2013/07/6696140347_824d45603a_z-360x225.jpg
                        $local_file = str_replace(ORIGINAL_URL, ORIGINAL_MEDIA, $rs->fields['guid']);

                        $imageData = array(
                            'title' => $this->convertoUTF8(strip_tags($rs->fields['post_title'])),
                            'category' => $IDCategory,
                            'fk_category' => $IDCategory,
                            'category_name'=> '',
                            'content_status' => 1,
                            'frontpage' => 0,
                            'in_home' => 0,
                            'metadata' => \Onm\StringUtils::get_tags($this->convertoUTF8($rs->fields['post_name'].$rs->fields['post_excerpt'])),
                            'description' => $this->convertoUTF8(strip_tags(substr($rs->fields['post_excerpt'], 0, 150))),
                            'id' => 0,
                            'created' => $rs->fields['post_date_gmt'],
                            'starttime' => $rs->fields['post_date_gmt'],
                            'changed' => $rs->fields['post_modified_gmt'],
                            'fk_user' =>  $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_author' =>  $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_publisher' => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_user_last_editor' => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'local_file' => $local_file,
                            'author_name' => '',
                        );

                        $date = new \DateTime($rs->fields['post_date_gmt']);
                        $imageID = @$photo->createFromLocalFile($imageData, $date->format('/Y/m/d/'));

                        if (!empty($imageID)) {
                            $this->insertRefactorID($originalImageID, $imageID, 'image', $rs->fields['post_name']);
                            // $this->output->writeln('- Image '. $imageID. ' ok');
                            $this->output->write('.');
                        } else {
                            $imageData['local_file'] = str_replace(ORIGINAL_URL, ORIGINAL_MEDIA_COMMON, $rs->fields['guid']);

                            $imageID = @$photo->createFromLocalFile($imageData, $date->format('/Y/m/d/'));
                            if (!empty($imageID)) {
                                $this->insertRefactorID($originalImageID, $imageID, 'image', $rs->fields['post_name']);
                                // $this->output->writeln('- Image '. $imageID. ' ok');
                            } else {
                                $this->output->write('.');
                                $this->output->writeln('Problem image '.$originalImageID.
                                    "-". $rs->fields['guid'] .' -> '.$imageData['local_file'] ."\n");
                            }
                        }
                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $this->output->writeln("Importer Images Finished");
            $rs->Close();
        }
    }


    protected function importGalleries()
    {
        $sql = "SELECT * FROM `".PREFIX."posts` WHERE ".
            "`post_content` LIKE '%gallery%'  AND post_status !='trash' ";
         /*[gallery link="file" ids="8727,8728,8729,8730,8731,8732"]*/

        $request    = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs         = $GLOBALS['application']->connOrigin->Execute($request);
        $oldID = $this->elementIsImported('fotos', 'category');

        if (empty($oldID)) {
            $IDCategory ='3'; //galleries
        } else {
           $IDCategory = $this->matchCategory($oldID); //assign category 'Fotos' for media elements
        }


        if (!$rs) {
            $this->output->writeln($GLOBALS['application']->connOrigin->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current   = 1;
            $album     = new \Album();

            while (!$rs->EOF) {
                 $originalID = $rs->fields['ID'];
                if ($this->elementIsImported($originalID, 'gallery')) {
                    $this->output->writeln("[{$current}/{$totalRows}] Gallery already imported");
                } else {

                    preg_match_all('/\[gallery.*?ids="(.*)".*?\]/', $rs->fields['post_content'], $result);

                    if (!empty($result[0]) ) {
                        $ids = array();
                        $originIds = explode(',', $result[1][0]);
                        foreach ($originIds as $id) {
                            $ids[] =$this->elementIsImported($id, 'image');
                        }

                        $newBody = preg_replace('/\[gallery.*?ids="(.*)".*?\]/', '', $rs->fields['post_content']);
                        $newBody = $this->convertoUTF8(strip_tags($newBody, '<p><a><br>'));

                        $data = array(
                            'title'          => $this->convertoUTF8($rs->fields['post_title']),
                            'category'       => $IDCategory,
                            'with_comment'   => 1,
                            'content_status' => 1,
                            'available'      => 1,
                            'metadata'       => \Onm\StringUtils::get_tags($this->convertoUTF8($rs->fields['post_title'])),
                            'subtitle'       => '',
                            'agency'         => '',
                            'summary'        => $newBody,
                            'fuente'         => '',
                            'category_name'  => 'fotos',
                            'description'    => $newBody,
                            'created'        => $rs->fields['post_date_gmt'],
                            'starttime'      => $rs->fields['post_date_gmt'],
                            'changed'        => $rs->fields['post_modified_gmt'],
                            'fk_user'        => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_author'      => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_publisher'   => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_user_last_editor' => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'slug'           => $rs->fields['post_name'],
                            'album_photos_id' => $ids,
                            'album_photos_footer'=> null,
                            'album_frontpage_image' => $ids[0],
                        );

                        $album->cover_id = $ids[0];

                        $result  = $album->create($data);
                        $albumID = $result->id;
                        if (!empty($albumID)) {
                            $this->insertRefactorID($originalID, $albumID, 'gallery', $rs->fields['post_name']);
                            //$this->updateFields('`available` ='.$rs->fields['available'], $rs->fields['pk_content']);
                         //   $this->output->writeln('- Gallery '. $albumID. ' ok');
                        } else {
                            $this->output->writeln('Problem inserting album '.$originalID.' - '.$rs->fields['post_name'] ."\n");
                        }
                    }
                }

                $current++;
                $rs->MoveNext();
            }
        }
        $rs->Close(); # optional
        $this->output->writeln("Importer Galleries Finished");

    }

    /**
     *  insert the correspondence between identifiers
     *
     * @return void
     **/
    protected function insertRefactorID($contentID, $newID, $type, $slug="")
    {
        $sql    = 'INSERT INTO translation_ids (`pk_content_old`, `pk_content`, `type`, `slug`)
                VALUES (?, ?, ?, ?)';
        $values = array($contentID, $newID, $type, $slug);

        $stmt = $GLOBALS['application']->conn->Prepare($sql);
        $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);

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
            $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);

            if (!$rss) {
                $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            //$this->output->writeln("There is imported {$contentID} - {$contentType}\n.");
        }
        return 0;
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
            $sql    = 'UPDATE `contents` SET {$params}  WHERE pk_content=?';
            $values = array($params, $contentID);

            $stmt = $GLOBALS['application']->conn->Prepare($sql);
            $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);
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
        $sql = "SELECT ID FROM `".PREFIX."posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ".
            " AND guid= '".$guid."'";


        // Fetch the list of Opinions available for one author in EditMaker
        $request = $GLOBALS['application']->conn->Prepare($sql);
        $rs      = $GLOBALS['application']->conn->Execute($request);

        $imageID='';
        if (!$rs || empty($rs->fields['ID'])) {
            $sql = "SELECT ID FROM `wp_posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ".
            " AND guid= '".$guid."'";
            $request = $GLOBALS['application']->conn->Prepare($sql);
            $rs      = $GLOBALS['application']->conn->Execute($request);
            if (!$rs->fields['ID']) {
                 $this->output->writeln('- Image '. $guid. ' fault');
            } else {
                $imageID = $this->elementIsImported($rs->fields['ID'], 'image');
            }
        // Fetch the list of Opinions available for one author in EditMaker
        $request = $GLOBALS['application']->conn->Prepare($sql);
        $rs      = $GLOBALS['application']->conn->Execute($request);
            $this->output->writeln('- Image '. $guid. ' fault');
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
        //Allow!!<a title="Kobe Bryant" href="http://www.flickr.com/photos/42161969@N03/4067656449/" target="_blank"><img title="Kobe Bryant" alt="Kobe Bryant" src="http://farm3.staticflickr.com/2493/4067656449_a576ba8a59.jpg" /></a>

        $newBody = '';
        $img     = '';
        $gallery = '';
        $photo     = new \Photo();
        $allowed = '<i><b><p><a><br><ol><ul><li>';
        $patern  = '@<a .*?href=".+?".*?><img .*?src="?('.preg_quote(ORIGINAL_URL).'.+?)".*?><\/a>@';
        preg_match_all($patern, $body, $result);
        if (!empty($result[1])) {
            $guid    = $result[1][0];
            $img     = $this->getOnmIdImage($guid);
            $newBody = $body;
            if (empty($img)) {
                $this->output->writeln('- Image from Body '. $guid. ' fault');
                $date = new \DateTime();
                $date = $date->format('Y-m-d H:i:s');
                $local_file = str_replace(ORIGINAL_URL, ORIGINAL_MEDIA, $guid);
                $oldID = $this->elementIsImported('fotos', 'category');
                if(empty($oldID)) {
                    $oldID ='1';
                }
                $IDCategory = $this->matchCategory($oldID); //assign category 'Fotos' for media elements

                $imageData = array(
                        'title' => $this->convertoUTF8(strip_tags($guid)),
                        'category' => $IDCategory,
                        'fk_category' => $IDCategory,
                        'category_name'=> '',
                        'content_status' => 1,
                        'frontpage' => 0,
                        'in_home' => 0,
                        'metadata' => \Onm\StringUtils::get_tags($this->convertoUTF8($guid)),
                        'description' => \Onm\StringUtils::get_tags($this->convertoUTF8($guid)),
                        'id' => 0,
                        'created' => $rs->fields['post_date_gmt'],
                        'starttime' => $rs->fields['post_date_gmt'],
                        'changed' => $rs->fields['post_modified_gmt'],
                        'fk_user' =>  $this->elementIsImported(7, 'user'),
                        'fk_author' =>  $this->elementIsImported(7, 'user'),
                        'fk_publisher' => $this->elementIsImported(7, 'user'),
                        'fk_user_last_editor' => $this->elementIsImported(7, 'user'),
                        'local_file' => $local_file,
                        'author_name' => '',
                    );

                $img  = $photo->createFromLocalFile($imageData);
                $this->output->writeln('- Image from Body inserted'. $img. ' ');
            }
            $newBody = preg_replace($patern, '', $body);
            $newBody = $this->convertoUTF8(strip_tags($newBody, $allowed));
        }

        preg_match_all('@\[caption .*?id="attachment_(.*)" align=.*?\].*?\[\/caption\]@', $body, $result);
        if (!empty($result[1]) ) {
            $id      = $result[1][0];
            $img     = $this->elementIsImported($id, 'image');
            $newBody = preg_replace('/\[caption .*?\].*?\[\/caption\]/', '', $body);
            $newBody = $this->convertoUTF8(strip_tags($newBody, $allowed));
        }

        preg_match_all('@\[gallery.*?ids="(.*)".*?\]@', $body, $result);
        if (!empty($result[0]) ) {
            $id      = $result[1][0];
            $gallery = $this->elementIsImported($id, 'gallery');
            $newBody = preg_replace('/\[gallery.*?ids="(.*)".*?\]/', '', $body);
            $newBody = $this->convertoUTF8(strip_tags($newBody, $allowed));
        }

        return array('img' => $img, 'body' => $newBody, 'gallery' => $gallery);

    }

       /**
     * Process an uploaded photo for user
     *
     * @param Symfony\Component\HttpFoundation\File\UploadedFile $file the uploaded file
     * @param string $userName the user real name
     *
     * @return Response the response object
     **/
    protected function uploadUserAvatar($file, $userName)
    {
        // Generate image path and upload directory
        $userNameNormalized      = \Onm\StringUtils::normalize_name($userName);
        $relativeAuthorImagePath ="/authors/".$userName;
        $uploadDirectory         =  MEDIA_PATH."/images".$relativeAuthorImagePath;

        // Get original information of the uploaded image
        $originalFileName = $file;
        $originalFileData = pathinfo($originalFileName);
        $fileExtension    = strtolower($originalFileData['extension']);

        // Generate new file name
        $currentTime = gettimeofday();
        $microTime   = intval(substr($currentTime['usec'], 0, 5));
        $newFileName = date("YmdHis").$microTime.".".$fileExtension;

        // Check upload directory
        if (!is_dir($uploadDirectory)) {
            \FilesManager::createDirectory($uploadDirectory);
        }

        // Upload file
        $fileCopied = @copy($file, $uploadDirectory."/".$newFileName);
        $photoId = 0;
        if ($fileCopied) {
            // Get all necessary data for the photo
            $infor = new \MediaItem($uploadDirectory.'/'.$newFileName);
            $data  = array(
                'title'       => $originalFileName,
                'name'        => $newFileName,
                'user_name'   => $newFileName,
                'path_file'   => $relativeAuthorImagePath,
                'nameCat'     => $userName,
                'category'    => '',
                'created'     => $infor->atime,
                'changed'     => $infor->mtime,
                'date'        => $infor->mtime,
                'size'        => round($infor->size/1024, 2),
                'width'       => $infor->width,
                'height'      => $infor->height,
                'type'        => $infor->type,
                'type_img'    => $fileExtension,
                'fk_author'   => $this->elementIsImported(7, 'user'),
                'media_type'  => 'image',
                'author_name' => '',
            );

            // Create new photo
            $photo = new \Photo();
            $photoId = $photo->create($data);

        } else {
             $this->output->writeln('- No photo move -',"{$file}, '-> '.{$uploadDirectory}."/".{$newFileName}");
        }
        return $photoId;
    }

    /**
     * Converts a given string to UTF-8 codification
     *
     * @return string
     **/
    protected function convertoUTF8($string)
    {
       // return mb_convert_encoding($string, 'UTF-8');
       return $string;
    }


    public function printResults()
    {

        $sql = "SELECT type , count( * ) AS `total` FROM `translation_ids` GROUP BY type";

        $count_sql = $GLOBALS['application']->conn->Prepare($sql);
        $rs        = $GLOBALS['application']->conn->Execute($count_sql);

        if (!$rs) {
            $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
        } else {
            while (!$rs->EOF) {
                $this->output->writeln("There are imported {$rs->fields['total']} ".
                    "type {$rs->fields['type']}.\n");
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }

}

