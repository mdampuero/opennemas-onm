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
use Onm\StringUtils;
use Onm\Settings as s;

class MigrateSpipToOnm extends Command
{

    protected $categories = array();

    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('originDB', InputArgument::REQUIRED, 'originDB'),
                    new InputArgument('finalDB', InputArgument::REQUIRED, 'finalDB'),

                )
            )
            ->setName('migrate:Spip')
            ->setDescription('Migrate a Spip database to Openemas')
            ->setHelp(
                <<<EOF
The <info>migrate:Spip</info> command migrates one Spip DB to new openenmas database.

<info>php bin/console migrate:Spip originDB finalDB</info>

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


        $originalDirectory = $dialog->ask(
            $output,
            'Where is the Spip media directory?',
            //'/home/opennemas/external/backup/media-import/lavozdelanzarote/IMG/'
            '/opt/backup_opennemas/IMG/'
        );
        $output->writeln("-: ".$originalDirectory);

        $instanceName = $dialog->ask(
            $output,
            'Where is the instance name?',
            'lavozdelanzarote'
        );
        $output->writeln("-: ".$instanceName);

        define('ORIGINAL_MEDIA', $originalDirectory);

        define('CACHE_PREFIX', 'spip');
        define('BD_HOST', $dataBaseHost);
        define('BD_USER', $dataBaseUser);
        define('BD_PASS', $dataBasePass);
        define('BD_TYPE', $dataBaseType);
        define('BD_DATABASE', $dataBaseName);
        define('ORIGIN_BD_DATABASE', $originDataBaseName);

        // Initialize internal constants for logger
        // Logger
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('INSTANCE_UNIQUE_NAME', $instanceName);

        define('IMG_DIR', "images");
        define('MEDIA_PATH', SITE_PATH."media".DS.INSTANCE_UNIQUE_NAME);

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();
        //    \Application::initLogger();


        $GLOBALS['application']->connOrigin = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->connOrigin->Connect(BD_HOST, BD_USER, BD_PASS, ORIGIN_BD_DATABASE);

        $_SESSION['username'] = 'script';
        $_SESSION['userid']   = 12;
        // Execute functions
        $output->writeln("\t<fg=blue>Migrating: ".$originDataBaseName."->".$dataBaseName."</fg=blue>");

        $this->output = $output;

        $this->prepareDatabase();

        $this->loadCategories();

        $this->importUsers();

        $this->importImages();
        $this->importFiles();

        $this->importArticles();
        $this->importOpinions();

        $this->importVideos();

        /*

        $this->updateMetadatas();

        $this->importVideos();


        */
        $this->updateDatabase();

        $output->writeln(
            "\n\t ***Migration finished for Database: ".$dataBaseName."***"
        );
        $this->printResults();
    }

    protected function prepareDatabase()
    {

        /*   $sql = "ALTER TABLE `translation_ids` ".
            "ADD `slug`  VARCHAR( 200 ) NOT NULL DEFAULT  '' ";
        $rss = $GLOBALS['application']->conn->Execute($sql); */
    }

    protected function updateDatabase()
    {

        $sql = "UPDATE articles SET img1='', img2=''  WHERE  pk_article IN ".
        "(SELECT pk_fk_content FROM contents_categories WHERE  ".
            "contents_categories.pk_fk_content_category = 40)";
        $rss = $GLOBALS['application']->conn->Execute($sql);
    }


    /**
     * Read users data and insert this in new database
     *
     * @return void
     **/
    protected function importUsers()
    {
        $sql = "SELECT  id_auteur, nom, bio, nom_site, url_site, pass, pgp, email, login, statut ".
            " FROM `spip_auteurs` WHERE statut <> '5poubelle'";
        //statut  0minirezo (administrador/a), 1comite (redactor/a), 5poubelle (en la papelera), 6forum

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);

        if (!$rs) {
            $this->output->writeln($GLOBALS['application']->connOrigin->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current   = 1;

            while (!$rs->EOF) {
                $originalID = $rs->fields['id_auteur'];
                if ($this->elementIsImported($originalID, 'user')) {
                    $this->output->writeln("[{$current}/{$totalRows}] user already imported");
                } else {

                    $photoId    = '';
                    $username = $this->convertoUTF8(strtolower(str_replace('-', '.', \Onm\StringUtils::get_title($rs->fields['login']))));
                    $values = array(
                        'username'      => $username,
                        'bio'           => $this->convertoUTF8($rs->fields['bio']),
                        'password'      => $rs->fields['pass'],
                        'sessionexpire' =>'30',
                        'email'         => $this->convertoUTF8($rs->fields['email']),
                        'name'          => $this->convertoUTF8($rs->fields['nom']),
                        'type'          => 0,
                        'deposit'       => '',
                        'token'         => '',
                        'activated'     => 1,
                        'fk_user_group' => '',
                        'avatar_img_id' => '',
                        'url'           => '',
                        'id_user_group' => array('3'),
                    );

                    try {
                        $user   = new \User();
                        $user->create($values);
                        $userID = $user->id;

                        if (!empty($userID)) {
                            $user->updateUserPassword($userID, $rs->fields['pass']);

                            $this->insertRefactorID($originalID, $userID, 'user', $username);
                            //$this->output->writeln('- User '. $userID. ' ok');
                        } else {
                            $this->output->writeln('Problem inserting id'.$originalID.'-'.$username .'\n');
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

        $sql = "SELECT * FROM spip_rubriques WHERE statut = 'publie' ORDER BY id_parent ASC ";
        //id_rubrique, id_parent , titre, descriptif, texte ,id_secteur

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs = $GLOBALS['application']->connOrigin->Execute($request);

        $categories = $rs->getArray();

        foreach ($categories as $category) {

            $originalID = $category['id_rubrique'];

            if ($this->elementIsImported($originalID, 'category')) {
                 $this->output->writeln("Category with id {$originalID} already imported\n");
            } else {
                $data = array(
                    'name'              => \StringUtils::get_title($category['titre']),
                    'title'             => $category['titre'],
                    'inmenu'            => 1,
                    'id_parent'         => $this->elementIsImported($category['id_parent'], 'category'),
                    'internal_category' => 1,
                    'logo_path'         => '',
                    'color'             => '',
                    'params'            => array(
                        'title'         => $category['titre'],
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
                    $data['id_parent'],
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
        /**
         * manually assigned because They have reduced the sections number
         * Category "Otros contenidos" - 50 "cajon desastre"
        **/
        $this->categories =
            array(
                1=>24, 2=>24, 3=>24, 4=>26, 10=>4, 11=>40, 22=>23, 23=>22, 34=>24,
                41=>35, 43=>4, 46=>24, 47=>26, 51=>45, 56=>40, 58=>22, 59=>22,
                60=>22, 61=>22, 62=>22, 63=>22, 64=>22, 66=>22, 67=>22, 70=>22,
                81=>48, 82=>42, 83=>43, 99=>37, 100=>37, 101=>37, 106=>44, 113=>39,
                114=> 39, 6=>50, 8=>50, 12=>50, 17=>50, 27=>50, 32=>50, 48=>50,
                55=>50, 69=>50, 95=>46, 97=>50, 102=>50
            );

        /*
        $sql = "SELECT * FROM translation_ids WHERE type ='category'";

        $rs = $GLOBALS['application']->conn->Execute($sql);

        $categories = $rs->GetArray();
        */
        $this->output->writeln('Categories loaded \n');

        return $this->categories;
    }

    protected function matchCategory($categoryId)
    {
        if (array_key_exists($categoryId, $this->categories)) {
            return $this->categories[$categoryId];
        } else {
            return 50;
        }
    }

    /**
     * Read articles data and insert in new database
     *
     * @return void
     **/
    protected function importArticles()
    {
        $sql2 = "SELECT `id_document`, `id_objet` FROM `spip_documents_liens`";
        $request = $GLOBALS['application']->connOrigin->Prepare($sql2);
        $rs2     = $GLOBALS['application']->connOrigin->Execute($request);

        $data        = $rs2->getArray();
        $imagesFront = array();
        foreach ($data as $item) {
            $key               = $item['id_objet'];
            $imagesFront[$key][] = $item['id_document'];

        }

        $sql2 = "SELECT `id_auteur`, `id_article` FROM`spip_auteurs_articles`";
        $request = $GLOBALS['application']->connOrigin->Prepare($sql2);
        $rs2     = $GLOBALS['application']->connOrigin->Execute($request);

        $data        = $rs2->getArray();
        $authors = array();
        foreach ($data as $item) {
            $key           = $item['id_article'];
            $authors[$key] = $item['id_auteur'];

        }

        $sql = "SELECT `id_article`, `surtitre`, `titre`, `soustitre`, ".
            " `descriptif`, `chapo`, `texte`, `ps`, `date`, `statut`, `id_secteur`,".
            " `maj`, `export`, `date_redac`, `visites`, `referers`, `popularite`,".
            " `accepter_forum`, `date_modif`, `lang`, `langue_choisie`, `id_trad`,".
            " `extra`, `id_version`, `nom_site`, `url_site`, `id_rubrique` ".
            " FROM `spip_articles`".
            " WHERE `statut` <> 'refuse' AND `statut` <> 'poubelle' ".
            " AND id_rubrique NOT IN (9, 10, 71) ";//AND date > '2013-08-06 15:05:37'";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);

        if (!$rs) {
            $this->output->writeln('DB problem: '. $GLOBALS['application']->connOrigin->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current   = 1;

            while (!$rs->EOF) {
                $originalArticleID = $rs->fields['id_article'];
                if ($this->elementIsImported($originalArticleID, 'article')) {
                    $this->output->writeln("*");//[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n");
                } else {
                    $this->output->writeln("[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ");

                    $data = $this->clearLabelsInBodyArticle($this->convertoUTF8($rs->fields['texte']));
                    $summary='';
                    if (!empty($rs->fields['descriptif'])) {
                        $summary = $this->convertoUTF8($rs->fields['descriptif']);
                        $patern  = '@\-\*\[(.*)[0-9]+\]@';
                        $summary = trim(preg_replace($patern, '', $summary));
                    }
                    if (empty($summary) || strlen($summary) < 30) {
                           $summary = $summary. ' '. strip_tags(substr($data['body'], 0, 240));
                           $summary = substr($summary, 0, strripos($summary, " ")).' ...';
                    }

                    if (array_key_exists($originalArticleID, $authors)) {
                        $author = $this->elementIsImported($authors[$originalArticleID], 'user');
                    }
                    if (empty($author)) {
                        $author = $this->elementIsImported(2, 'user');
                    }
                    $image = '';
                    if (array_key_exists($originalArticleID, $imagesFront)) {
                        $image = $this->elementIsImported($imagesFront[$originalArticleID][0], 'image');
                    } else {
                        $image = $this->importckeckingImagePath($rs->fields);
                    }
                    $withComments = 1;
                    if ($rs->fields['accepter_forum'] == 'non') {
                        $withComments = 0;
                    }
                    $slug = \StringUtils::get_title($this->convertoUTF8($rs->fields['titre']));
                    $values = array(
                        'title' => $this->convertoUTF8($rs->fields['titre']),
                        'category' => $this->matchCategory($rs->fields['id_rubrique']),
                        'with_comment' => $withComments,
                        'available' => 1,
                        'content_status' => 1,
                        'frontpage' => 0,
                        'in_home' => 0,
                        'title_int' => $this->convertoUTF8($rs->fields['titre']),
                        'metadata' => StringUtils::get_tags($slug),
                        'subtitle' => $this->convertoUTF8($rs->fields['surtitre']),
                        'slug' => $slug,
                        'agency' => '',
                        'summary' => $summary,
                        'description' => strip_tags(substr($summary, 0, 150)),
                        'body' => $data['body'],
                        'posic' => 0,
                        'id' => 0,
                        'img1' => $image,
                        'img2' => $data['img'],
                        'img1_footer' => '', //$data['footer1'],
                        'img2_footer' => '', //$data['footer2'],
                        'fk_video' => '',
                        'fk_video2' => '',
                        'footer_video2' => '',
                        'created' => $rs->fields['date'],
                        'starttime' => $rs->fields['date'],
                        'changed' => $rs->fields['date'],
                        'fk_user' => $author,
                        'fk_author' => $author,
                        'fk_publisher' => $author,
                        'fk_publisher' => $author,
                    );

                    // TODO search files as related contents.
                    // TODO: insert videos.
                    $article      = new \Article();
                    $newArticleID = $article->create($values);

                    if (!empty($newArticleID)) {
                        $this->insertRefactorID($originalArticleID, $newArticleID, 'article', $slug);
                        //  $this->output->writeln('-'. $originalArticleID.'->'.
                        //   $newArticleID. ' article ok');
                        $this->output->write('.');
                    } else {
                        $this->output->writeln(
                            'Problem inserting article '.$originalArticleID.
                            ' - '. $slug .'\n'
                        );
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
    protected function importImages()
    {
        $this->output->writeln('Importing images \n');

        $settings = array( 'image_thumb_size'=>'140',
                            'image_inner_thumb_size'=>'470',
                            'image_front_thumb_size'=>'350');
        foreach ($settings as $key => $value) {
            s::set($key, $value);
        }

        $sql = "SELECT `id_document`, `id_vignette`, `titre`, `date`, `descriptif`,".
            " `fichier`, `taille`, `largeur`, `hauteur`, `mode`, `distant`, `maj`,".
            " `extension` FROM `spip_documents`".
            " WHERE (mode ='vignette' OR mode = 'image' OR (mode='document' AND extension='jpg'))";
            //" AND maj > '2013-08-06 15:05:37'";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);


        $IDCategory ='1'; //fotografias

        if (!$rs) {
            $this->output->writeln($GLOBALS['application']->connOrigin->ErrorMsg());
        } else {
            $authorRedaccion = 2;
            $totalRows = $rs->_numOfRows;
            $current   = 1;
            $photo     = new \Photo();
            $this->output->writeln("Importing {$totalRows} images \n");

            while (!$rs->EOF) {
                if (!empty($rs->fields['fichier'])) {
                    if ($this->elementIsImported($rs->fields['id_document'], 'image')) {
                        $this->output->writeln('*');//"[{$current}/{$totalRows}] Image already imported");
                    } else {

                        $originalImageID = $rs->fields['id_document'];
                        $localFile       = ORIGINAL_MEDIA.$rs->fields['fichier'];

                        $title           = strip_tags($rs->fields['titre']);
                        if (empty($title)) {
                            $title = strip_tags($rs->fields['fichier']);
                        }
                        $imageData = array(
                            'title' => $this->convertoUTF8($title),
                            'category' => $IDCategory,
                            'fk_category' => $IDCategory,
                            'category_name'=> '',
                            'content_status' => 1,
                            'frontpage' => 0,
                            'in_home' => 0,
                            'metadata' => \Onm\StringUtils::get_tags($this->convertoUTF8($title." ".$rs->fields['descriptif'])),
                            'description' => $this->convertoUTF8(strip_tags(substr($rs->fields['descriptif'], 0, 150))),
                            'id' => 0,
                            'created' => $rs->fields['date'],
                            'starttime' => $rs->fields['date'],
                            'changed' => $rs->fields['date'],
                            'fk_user' =>  $this->elementIsImported($authorRedaccion, 'user'),
                            'fk_author' =>  $this->elementIsImported($authorRedaccion, 'user'),
                            'fk_publisher' => $this->elementIsImported($authorRedaccion, 'user'),
                            'fk_user_last_editor' => $this->elementIsImported($authorRedaccion, 'user'),
                            'local_file' => $localFile,
                            'author_name' => '',
                        );

                        $date = new \DateTime($rs->fields['date']);
                        $imageID = @$photo->createFromLocalFile($imageData, $date->format('/Y/m/d/'));

                        if (!empty($imageID)) {
                            $this->insertRefactorID($originalImageID, $imageID, 'image', $title);
                            // $this->output->writeln('- Image '. $imageID. ' ok');
                            $this->output->write('.');
                        } else {
                                $this->output->write('.');
                                $this->output->writeln(
                                    'Problem image '.$originalImageID.
                                    "-". $this->convertoUTF8($title) .' -> '.$rs->fields['fichier']."\n"
                                );

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


    /**
     * check if exist file in folder as rule-> "arton.'articleId'.jpg" Ex:lavozdelanzarote/IMG/arton78150.jpg
     *
     * @return void
     **/
    protected function importckeckingImagePath($article)
    {

        $fileName = 'arton'.$article['id_article'].'.jpg';
        $localFile  = ORIGINAL_MEDIA.$fileName;
        $imageID ='';

        if (file_exists($localFile)) {
            $this->output->writeln("Checking image file {$fileName} \n");
            $IDCategory ='1'; //fotografias

            if ($this->elementIsImported($article['id_article'], 'arton')) {
                $this->output->writeln('*');//"[{$current}/{$totalRows}] Image already imported");
                return $imageID;
            } else {
                $authorRedaccion = 2;
                $photo     = new \Photo();

                $originalImageID = $article['id_article'];

                $title = strip_tags($article['titre']);

                $imageData = array(
                    'title' => $this->convertoUTF8($title),
                    'category' => $IDCategory,
                    'fk_category' => $IDCategory,
                    'category_name'=> '',
                    'content_status' => 1,
                    'frontpage' => 0,
                    'in_home' => 0,
                    'metadata' => \Onm\StringUtils::get_tags($this->convertoUTF8($title." ".$article['surtitre'])),
                    'description' => $this->convertoUTF8(strip_tags(substr($article['texte'], 0, 120))),
                    'id' => 0,
                    'created' => $article['date'],
                    'starttime' => $article['date'],
                    'changed' => $article['date'],
                    'fk_user' =>  $this->elementIsImported($authorRedaccion, 'user'),
                    'fk_author' =>  $this->elementIsImported($authorRedaccion, 'user'),
                    'fk_publisher' => $this->elementIsImported($authorRedaccion, 'user'),
                    'fk_user_last_editor' => $this->elementIsImported($authorRedaccion, 'user'),
                    'local_file' => $localFile,
                    'author_name' => '',
                );

                $date = new \DateTime($article['date']);
                $imageID = $photo->createFromLocalFile($imageData, $date->format('/Y/m/d/'));

                if (!empty($imageID)) {
                    $this->insertRefactorID($article['id_article'], $imageID, 'arton', $title);
                    $this->output->writeln('- Image '. $fileName.' - '.$imageID. ' ok');
                    $this->output->write('.');
                } else {
                        $this->output->write('.');
                        $this->output->writeln(
                            'Problem image '.$originalImageID.
                            "-". $this->convertoUTF8($title) .' -> '.$fileName."\n"
                        );
                }
            }

        }

        return $imageID;
    }

    /**
     * Read images data and insert this in new database
     *
     * @return void
     **/
    protected function importFiles()
    {

        $sql = "SELECT `id_document`, `id_vignette`, `titre`, `date`, `descriptif`,".
            " `fichier`, `taille`, `largeur`, `hauteur`, `mode`, `distant`, `maj`,".
            " `extension` FROM `spip_documents`".
            " WHERE  mode='document' AND extension <>'jpg'";// AND maj > '2013-08-06 15:05:37'";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);

        $oldID = $this->elementIsImported('fotos', 'category');
        $authorRedaccion = 2;
        if (empty($oldID)) {
            $IDCategory ='32'; //portadas
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
                if (!empty($rs->fields['fichier'])) {
                    if ($this->elementIsImported($rs->fields['id_document'], 'files')) {
                        $this->output->writeln("[{$current}/{$totalRows}] File already imported");
                    } else {

                        $originalFileID = $rs->fields['id_document'];

                        $localFile =  ORIGINAL_MEDIA.$rs->fields['fichier'];

                        $fileData = array(
                            'title' => $this->convertoUTF8(strip_tags($rs->fields['titre'])),
                            'category' => $IDCategory,
                            'fk_category' => $IDCategory,
                            'category_name'=> '',
                            'content_status' => 1,
                            'frontpage' => 0,
                            'in_home' => 0,
                            'metadata' => \Onm\StringUtils::get_tags($this->convertoUTF8($rs->fields['titre'].$rs->fields['descriptif'])),
                            'description' => $this->convertoUTF8(strip_tags(substr($rs->fields['descriptif'], 0, 150))),
                            'id' => 0,
                            'created' => $rs->fields['date'],
                            'starttime' => $rs->fields['date'],
                            'changed' => $rs->fields['date'],
                            'fk_user' =>  $this->elementIsImported($authorRedaccion, 'user'),
                            'fk_author' =>  $this->elementIsImported($authorRedaccion, 'user'),
                            'fk_publisher' => $this->elementIsImported($authorRedaccion, 'user'),
                            'fk_user_last_editor' => $this->elementIsImported($authorRedaccion, 'user'),
                            'localFile' => $localFile,
                            'author_name' => '',
                        );

                        $date = new \DateTime($rs->fields['date']);
                        $filesID = @$photo->createFromLocalFile($fileData, $date->format('/Y/m/d/'));

                        if (!empty($fileID)) {
                            $this->insertRefactorID($originalFileID, $fileID, 'files', $rs->fields['titre']);

                            $this->output->write('.');
                        } else {
                                $this->output->write('.');
                                $this->output->writeln(
                                    'Problem file '.$originalFileID.
                                    "-". $rs->fields['titre'] .' -> '.$fileData['localFile'] ."\n"
                                );
                        }
                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $this->output->writeln("Importer files Finished");
            $rs->Close();
        }
    }

       /**
     * Read articles data and insert in new database
     *
     * @return void
     **/
    protected function importOpinions()
    {
        $sql2 = "SELECT `id_document`, `id_objet` FROM `spip_documents_liens`";
        $request = $GLOBALS['application']->connOrigin->Prepare($sql2);
        $rs2     = $GLOBALS['application']->connOrigin->Execute($request);

        $data        = $rs2->getArray();
        $imagesFront = array();
        foreach ($data as $item) {
            $key               = $item['id_objet'];
            $imagesFront[$key][] = $item['id_document'];

        }

        $sql2 = "SELECT `id_auteur`, `id_article` FROM`spip_auteurs_articles`";
        $request = $GLOBALS['application']->connOrigin->Prepare($sql2);
        $rs2     = $GLOBALS['application']->connOrigin->Execute($request);

        $data        = $rs2->getArray();
        $authors = array();
        foreach ($data as $item) {
            $key           = $item['id_article'];
            $authors[$key] = $item['id_auteur'];

        }


        $sql = "SELECT `id_article`, `surtitre`, `titre`, `soustitre`, ".
            " `descriptif`, `chapo`, `texte`, `ps`, `date`, `statut`, `id_secteur`,".
            " `maj`, `export`, `date_redac`, `visites`, `referers`, `popularite`,".
            " `accepter_forum`, `date_modif`, `lang`, `langue_choisie`, `id_trad`,".
            " `extra`, `id_version`, `nom_site`, `url_site`, `id_rubrique` ".
            " FROM `spip_articles`".
            " WHERE `statut` <> 'refuse' AND `statut` <> 'poubelle' ".
            " AND id_rubrique IN (9, 10, 71) ";//AND date > '2013-08-06 15:05:37'";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);

        if (!$rs) {
            $this->output->writeln('DB problem: '. $GLOBALS['application']->connOrigin->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current   = 1;

            while (!$rs->EOF) {
                $originalArticleID = $rs->fields['id_article'];
                if ($this->elementIsImported($originalArticleID, 'opinion')) {
                    $this->output->writeln("*");//[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n");
                } else {
                    $this->output->writeln("[{$current}/{$totalRows}] Importing opinion with id {$originalArticleID} - ");

                    $data = $this->clearLabelsInBodyArticle($this->convertoUTF8($rs->fields['texte']));
                    $summary = '';
                    if (!empty($rs->fields['descriptif'])) {
                        $summary = $this->convertoUTF8($rs->fields['descriptif']);
                    }
                    $summary = $summary."<br /> ".strip_tags(substr($data['body'], 0, 240));
                    $summary = substr($summary, 0, strripos($summary, " ")).' ...';


                    if (array_key_exists($originalArticleID, $authors)) {
                        $author = $this->elementIsImported($authors[$originalArticleID], 'user');
                    }

                    if (empty($author)) {
                        $author = 26; // Colaboradores
                    }
                    if ($rs->fields['id_rubrique'] == 10) {
                        $typeOpinion = 1;
                        $author = 1;
                    }

                    if ($author == 1) {
                        $typeOpinion = 1;
                    } else {
                        $typeOpinion = 0;
                    }
                    $image = '';
                    if (array_key_exists($originalArticleID, $imagesFront)) {
                        $image = $this->elementIsImported($imagesFront[$originalArticleID][0], 'image');
                    }
                    $withComments = 1;
                    if ($rs->fields['accepter_forum'] == 'non') {
                        $withComments = 0;
                    }
                    $slug = \StringUtils::get_title($this->convertoUTF8($rs->fields['titre']));
                    $values = array(
                        'title' => $this->convertoUTF8($rs->fields['titre']),
                        'category' => 4,
                        'fk_category' => 4,
                        'with_comment' => $withComments,
                        'available' => 1,
                        'content_status' => 1,
                        'frontpage' => 0,
                        'in_home' => 0,
                        'title_int' => $this->convertoUTF8($rs->fields['titre']),
                        'metadata' => StringUtils::get_tags($slug),
                        'subtitle' => $this->convertoUTF8($rs->fields['surtitre']),
                        'slug' => $slug,
                        'agency' => '',
                        'summary' => $summary,
                        'description' => strip_tags(substr($summary, 0, 150)),
                        'body' => $summary." ".$data['body'],
                        'posic' => 0,
                        'id' => 0,
                        'type_opinion' =>$typeOpinion,
                        'img1' => $image,
                        'img2' => $data['img'],
                        'img1_footer' => '', //$data['footer1'],
                        'img2_footer' => '', //$data['footer2'],
                        'fk_video' => '',
                        'fk_video2' => '',
                        'footer_video2' => '',
                        'created' => $rs->fields['date'],
                        'starttime' => $rs->fields['date'],
                        'changed' => $rs->fields['date'],
                        'fk_user' => $author,
                        'fk_author' => $author,
                        'fk_publisher' => $author,
                        'fk_author_img' => '',
                    );

                    $article      = new \Opinion();
                    $newArticleID = $article->create($values);

                    if (!empty($newArticleID)) {
                        $this->insertRefactorID($originalArticleID, $newArticleID, 'opinion', $slug);
                        //  $this->output->writeln('-'. $originalArticleID.'->'.
                        //   $newArticleID. ' article ok');
                        $this->output->write('.');
                    } else {
                        $this->output->writeln(
                            'Problem inserting opinion '.$originalArticleID.
                            ' - '. $slug .'\n'
                        );
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
     *  insert the correspondence between identifiers
     *
     * @return void
     **/
    protected function insertRefactorID($contentID, $newID, $type, $slug = "")
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
                $this->output->writeln('- is imported '.$GLOBALS['application']->conn->ErrorMsg());
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            //$this->output->writeln("There is imported {$contentID} - {$contentType}\n.");
        }
        return 0;
    }

    /**
     * Clear body for
     *
     * @return string
     **/
    protected function clearLabelsInBodyArticle($body)
    {

        /* <docXX|left> y <imgXX|right>  */
        $result = array();

        $newBody = $body;

        $paterns = array('/{{/','/}}/', '/{/','/}/', '/{{{/','/}}}/',);
        $replacements = array('<strong>','</strong>', '<em>','</em>', '<strong>','</strong>');
        $newBody = preg_replace($paterns, $replacements, $body);

        $img     = '';
        $file = '';
        $footer  = '';
        $photo     = new \Photo();
        $allowed = '<i><b><p><a><br><ol><ul><li><strong><em>';
        $patern  = '@<img([0-9]*)\|(center|right|left)>@';
        $guid = '';

        preg_match_all($patern, $newBody, $result);
        if (!empty($result[1])) {
            $guid    = $result[1][0];
            //var_dump($guid);
            $img     = $this->elementIsImported($guid, 'image');
        }

        $newBody = preg_replace($patern, '', $newBody);
        $patern  = '@<doc([0-9]*)\|(center|right|left)>@';
        preg_match_all($patern, $newBody, $result);
        if (!empty($result[1])) {

            $newGuid = $result[1][0];
            $file  = $this->elementIsImported($newGuid, 'file');

            if (empty($file)) {
                $this->output->writeln('- doc from Body '. $newGuid. ' fault');
            }
        }

        $newBody = preg_replace($patern, '', $newBody);
        $video='';
        /*
        $patern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
        preg_match(
            $patern,
            $newBody,
            $match
        );
        $video = $this->createVideo($match);
        $newBody = preg_replace($patern, '', $newBody);
        */
        $str = preg_replace(array("/([\r\n])+/i", "/([\n]{2,})/i", "/([\n]{2,})/i", "/(\n)/i"), array('</p><p>', '</p><p>', '<br>', '<br>'), $newBody);
        $newBody = '<p>'.($str).'</p>';

        return array('img' => $img, 'body' => $newBody, 'file' => $file, 'video' => $video, 'footer' => $footer);

    }




    /***** functions fixing lavoz  fails */

    public function updateSummaries()
    {

        $sql = "SELECT pk_article, body, summary FROM articles ";
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $values= array();
        while (!$rs->EOF) {

            $summary = '';
            if (!empty($rs->fields['body'])) {
                $summary = strip_tags(substr($rs->fields['body'], 0, 240));
                $summary = substr($summary, 0, strripos($summary, " ")).' ...';
            }

            $values[] =  array(
                $summary,
                $rs->fields['pk_article'],
            );

            $rs->MoveNext();
        }

        if (!empty($values)) {
            $sql    = 'UPDATE `articles` SET summary =?  WHERE pk_article=?';

            $stmt = $GLOBALS['application']->conn->Prepare($sql);
            $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);
            if (!$rss) {
                $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
            }

        } else {
            //$this->output->writeln("Please provide a contentID and views to update it.");
        }
    }


    public function updateMetadatas()
    {

        $sql = "SELECT pk_content, metadata, slug, title FROM contents ";
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $values= array();
        while (!$rs->EOF) {

            $tags = StringUtils::get_tags($rs->fields['metadata']);
            if (empty($tags)) {
                $tags = StringUtils::get_tags($rs->fields['slug']);
            }

            $values[] =  array(
                $tags,
                $rs->fields['pk_content'],
            );

            $rs->MoveNext();
        }

        if (!empty($values)) {
            $sql    = 'UPDATE `contents` SET metadata =?  WHERE pk_content=?';

            $stmt = $GLOBALS['application']->conn->Prepare($sql);
            $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);
            if (!$rss) {
                $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
            }

        } else {
            //$this->output->writeln("Please provide a contentID and views to update it.");
        }
    }


     /* create new video */

    public function importVideos()
    {
        $sql = "SELECT pk_article, body FROM articles";
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $values= array();
        while (!$rs->EOF) {

            $patern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
            preg_match(
                $patern,
                $rs->fields['body'],
                $match
            );

            $pk_video='';
            if (!empty($match)) {
                $pk_video = $this->createVideo($match[0]);
            }
            if (!empty($pk_video)) {
                $values[] =  array(
                    $pk_video,
                    $rs->fields['pk_article'],
                );
            }
            $rs->MoveNext();
        }

        /*
        if (!empty($values)) {
            $sql    = 'UPDATE `articles` SET fk_video2=?  WHERE pk_article=?';

            $stmt = $GLOBALS['application']->conn->Prepare($sql);
            $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);
            if (!$rss) {
                $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
            }

        } else {
            //$this->output->writeln("Please provide a contentID and views to update it.");
        }
        */
    }
     /* create new video */

    public function createVideo($video)
    {
        $newVideoID = null;

        preg_match(
            '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})"(.*)%i',
            $video,
            $match
        );

        $oldID = $this->elementIsImported('videos', 'category');

        if (empty($oldID)) {
            $IDCategory ='6'; //videos
        } else {
            $IDCategory = $this->matchCategory($oldID); //assign category 'videos' for media elements
        }

        if (!empty($match[1])) {

            $url= "http://www.youtube.com/watch?v=".$match[1] ;


            if ($url) {
                try {
                    $videoP = new \Panorama\Video($url);
                    $information = $videoP->getVideoDetails();

                    $values = array(
                        'file_path'      => $url,
                        'video_url'      => $url,
                        'category'       => $IDCategory,
                        'available'      => 1,
                        'content_status' => 1,
                        'title'          => $information['title'],
                        'metadata'       => StringUtils::get_tags($information['title']),
                        'description'    => $information['title'].' video '.$url,
                        'author_name'    => $url,
                    );

                } catch (\Exception $e) {
                    $this->output->writeln(
                        "\n 1 Can't get video information. Check the $url"
                    );
                    return;
                }

                $video = new \Video();
                $values['information'] = $information;

                try {
                    $newVideoID = $video->create($values);
                } catch (\Exception $e) {

                    $this->output->writeln("1 Problem with video: {$e->getMessage()} {$url}  ");
                }

                if (empty($newVideoID)) {
                    $this->output->writeln("2 Problem with video: {$url}  ");
                }

            } else {
                $this->output->writeln("There was an error while uploading the form, not all the required data was sent.");

            }
        }

        $this->output->writeln("new id {$newVideoID} [DONE]");
        return $newVideoID;
    }


    /**
     * Converts a given string to UTF-8 codification
     *
     * @return string
     **/
    protected function convertoUTF8($string)
    {
        $string = mb_convert_encoding($string, 'UTF-8');
        $string = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
        //preg_replace(('/{{(*)}}/',  '<b>$1</b>',$string)) {

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
                $this->output->writeln(
                    "There are imported {$rs->fields['total']} ".
                    "type {$rs->fields['type']}.\n"
                );
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }
}
