<?php
class RegionImporter
{
    const CONF_FILE = 'config/config.ini';

    public static $configuration = '';

    public static $originMedia ='';

    public static $originConn = '';

    public static $reflector;

    public $idsMatches = array();

    public $originalCategories = array();

    public $categoriesOpinion = array();

    public function __construct ()
    {
        self::loadConfiguration();

        define('INSTANCE_UNIQUE_NAME', self::$configuration['instanceData']['INSTANCE_UNIQUE_NAME']);

        // Application::initInternalConstants();

        self::initDatabaseConnections();

        $this->originalCategories = self::$configuration['categories'];
        self::$originMedia = self::$configuration['media']['directory'];
    }


    /**
     * Parses the configuration file and loads it
     *
     * @return void
     **/
    public static function loadConfiguration()
    {
        echo "Loading script configuration...".PHP_EOL;
        $configurationFile = realpath(__DIR__.'/../'.self::CONF_FILE);
        if (!is_file($configurationFile)) {
            echo "Configuration file doesn't exists $configurationFile".PHP_EOL;
            die();
        }
        self::$configuration = parse_ini_file($configurationFile, true);


    }


    public static function initDatabaseConnections()
    {
        $configOriginDB = self::$configuration['original-database'];
        echo "Connecting to the original database...".PHP_EOL;
        if (isset($configOriginDB['host'])
            && isset($configOriginDB['database'])
            && isset($configOriginDB['user'])
            && isset($configOriginDB['password'])
            && isset($configOriginDB['type'])
        ) {

            self::$originConn = ADONewConnection($configOriginDB['type']);
            self::$originConn->PConnect(
                $configOriginDB['host'],
                $configOriginDB['user'],
                $configOriginDB['password'],
                $configOriginDB['database']
            );
            self::$originConn->SetFetchMode(ADODB_FETCH_ASSOC);
            self::$originConn->Execute('SET NAMES UTF8');
        }


        $config = self::$configuration['onm-database'];
        echo "Connecting to the new Onm database...".PHP_EOL;
        define('BD_HOST', $config['host']);
        define('BD_USER', $config['user']);
        define('BD_PASS', $config['password']);
        define('BD_TYPE', $config['type']);
        define('BD_DATABASE', $config['database']);


        $GLOBALS['application'] = new Application();
        Application::initDatabase();
        Application::initLogger();

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $GLOBALS['application']->conn->Execute('SET NAMES UTF8');
    }

    public function importCategories()
    {
        $ih = new ImportHelper();

        $rs = self::$originConn->Execute('SELECT * FROM Noticias_Categorias');
        $categories = $rs->getArray();

        foreach ($categories as $category) {
            if ($category['idNoticias_Categorias'] == 0) {
                continue;
            }
            $originalID = $category['idNoticias_Categorias'];

            if ($ih->elementIsImported($originalID, 'category')) {
                echo "Category with id {$originalID} already imported\n";
            } else {
                $data = array(
                    'name'              => (StringUtils::get_title($category['Nombre'])),
                    'title'             => $category['Nombre'],
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
                echo "Importing category with id {$originalID} - ";
                $ih->logElementInsert($originalID, $newID, 'category');
                echo "new id {$newID} [DONE]\n";
            }
        }

        return $this;
    }

    public function loadCategories()
    {
        $this->categories = array();

        $sql = "SELECT * FROM translation_ids WHERE type='category'";

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        $categories = $rs->GetArray();

        foreach ($categories as $category) {
            $this->categories [$category['pk_content_old']] = $category['pk_content'];
        }

        return $this;
    }

    public function matchCategory($categoryId)
    {
        return $this->categories[$categoryId];
    }

    public function importArticles()
    {
        $sql = "SELECT  idNoticias as id, Titulo as title, Antetitulo as subtitle,
                        Subtitulo as summary, Entradilla as body1, Contenido as body2,
                        Publicada as published,
                        Categoria as category_id, HoraPublicacion as created,
                        Fuente as agency, Visitas as views,
                        Keywords as metadata  FROM Noticias";

        $rs = self::$originConn->Execute($sql);

        if (!$rs) {
            ImportHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                if ($ih->elementIsImported($rs->fields['id'], 'article')) {
                    echo "[{$current}/{$totalRows}] Article with id {$rs->fields['id']} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing article with id {$rs->fields['id']} - ";

                    $originalArticleID = $rs->fields['id'];
                    $body =  ImportHelper::convertoUTF8($rs->fields['body1']. ' '.$rs->fields['body2']);
                    $date = new DateTime();
                    $date->setTimestamp($rs->fields['created']);
                    $dateString = $date->format('Y-m-d H:i:s');

                    $values = array(
                        'title'          => ImportHelper::convertoUTF8($rs->fields['title']),
                        'category'       => $this->matchCategory($rs->fields['category_id']),
                        'with_comment'   => 1,
                        'available'      => 1,
                        'content_status' => 1,
                        'frontpage'      => 0,
                        'in_home'        => 0,
                        'title_int'      => ImportHelper::convertoUTF8($rs->fields['title']),
                        'metadata'       => ImportHelper::convertoUTF8($rs->fields['metadata']),
                        'subtitle'       => ImportHelper::convertoUTF8($rs->fields['subtitle']),
                        'agency'         => ImportHelper::convertoUTF8($rs->fields['agency']),
                        'summary'        => ImportHelper::convertoUTF8($rs->fields['summary']),
                        'description'    => '',
                        'body'           => $body,
                        'posic'          => 0,
                        'id'             => 0,
                        'img1'           => 0,
                        'img2'           => 0,
                        'created'        => $dateString,
                        'starttime'      => $dateString,
                        'changed'        => $dateString,
                        'fk_user'        => 0,
                        'fk_publisher'   => 0,
                    );

                    $article = new Article();
                    $newArticleID = $article->create($values);
                    if (is_string($newArticleID)) {
                        $ih->logElementInsert($originalArticleID, $newArticleID, 'article');
                    }
                    echo "new id {$newArticleID} [DONE]\n";
                }

                $current++;

                $rs->MoveNext();
            }

            $rs->Close(); # optional
        }
    }

    public function importOpinions()
    {
        echo "IMPORTING OPINIONS\n";
        $_filter_by_section = ' `wp_term_relationships`.`term_taxonomy_id` IN ('.
                implode(', ', array_keys($this->categoriesOpinion)).") ";

        $sql = "SELECT * FROM `wp_posts`, `wp_term_relationships` WHERE ".
        "`post_type` = 'post' AND `ID`=`object_id` ".
        " AND ".$_filter_by_section;

        // Fetch the list of Opinions available for one author in EditMaker
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);


        if (!$rs) {
            ImportHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                if ($ih->elementIsImported($rs->fields['ID'], 'opinion')) {
                    echo "[{$current}/{$totalRows}] Opinion with id {$rs->fields['ID']} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing opinion with id {$rs->fields['ID']} - ";

                    $originalOpinionID = $rs->fields['ID'];
                    $data = $this->clearLabelsInBodyArticle($rs->fields['post_content']); //extract image html in body
                    $values =
                        array(
                            'title'        => ImportHelper::convertoUTF8($rs->fields['post_title']),
                            'category'     => '4',
                            'type_opinion' => self::$configuration['opinion']['typeOpinion'],
                            'body'         => $data['body'],
                            'metadata'     => strip_tags(
                                StringUtils::get_tags(ImportHelper::convertoUTF8($rs->fields['post_title']))
                            ),
                            'description'    => strip_tags(substr($data['body'], 0, 150)),
                            'fk_author'      => self::$configuration['opinion']['authorOpinion'],
                            'available'      => 1,
                            'with_comment'   => 1,
                            'in_home'        => 0,
                            'content_status' => 1,
                            'created'        => $rs->fields['post_date'],
                            'starttime'      => $rs->fields['post_date'],
                            'changed'        => $rs->fields['post_modified'],
                            'fk_user'        => self::$configuration['idUser']['id'],
                            'fk_publisher'   => self::$configuration['idUser']['id'],
                        );



                    $opinion = new Opinion();
                    $newOpinionID = $opinion->create($values);

                    if (is_string($newOpinionID)) {
                        $ih->logElementInsert($originalOpinionID, $newOpinionID, 'opinion');

                    }
                    echo "new id {$newOpinionID} [DONE]\n";
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }

    public function importPhotos()
    {
        echo "\nIMPORTING IMAGES\n";

        // Create category where import images into if not exists
        $categoryName = self::$configuration['media']['category_name'];
        $rs = $GLOBALS['application']->conn->GetOne(
            'SELECT * FROM content_categories WHERE title = ?',
            $categoryName
        );

        if ($rs == null) {
            $data = array(
                'title'               => $categoryName,
                'name'                => \Onm\StringUtils::get_title($categoryName),
                'inmenu'              => 1,
                'internal_category'   => 7,
                'fk_content_category' => '0',
            );

            $data['params']['title']  = 'Importadas';
            $data['params']['inrss']  = 0;

            $category = new ContentCategory();
            $category->create($data);
            $categoryId = $category->pk_content_category;
        } else {
            $categoryId = $rs;
        }

        if (!$categoryId) {
            echo "Needs category id for images. Unable to import images without a category to add to\n";
            die();
        }

        $sql = "SELECT idElemento as id, Archivo as file_name,
                Alt as title, Pie as description, Nombre as title2,
                Fecha as created FROM Elementos WHERE Tipo = 'foto'";

        // Fetch the list of post type attachment = images
        $rs = self::$originConn->Execute($sql);

        if (!$rs) {
            ImportHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current   = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {
                $basePath = self::$configuration['media']['images_directory'];

                if ($ih->elementIsImported($rs->fields['id'], 'image')) {
                    echo "[{$current}/{$totalRows}] image with id {$rs->fields['id']} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing image with id {$rs->fields['id']} - ";

                    $filePath = $basePath.$rs->fields['file_name'];
                    if (!file_exists($filePath)) {
                        echo "File not available in local [FAILED]\n";

                        $current++;
                        $rs->MoveNext();
                        continue;
                    }

                    $time = (empty($rs->fields['created'])) ? time() : $rs->fields['created'];
                    $originalImageID = $rs->fields['id'];
                    $date = new DateTime();
                    $date->setTimestamp($time);
                    $dateString = $date->format('Y-m-d H:i:s');

                    $values = array(
                        'id'             => 0,
                        'title'          => strip_tags($rs->fields['title']),
                        'category'       => $categoryId,
                        'fk_category'    => $categoryId,
                        'category_name'  => self::$configuration['media']['category_name'],
                        'content_status' => 1,
                        'frontpage'      => 0,
                        'in_home'        => 0,
                        'metadata'       => StringUtils::get_tags($rs->fields['title']),
                        'description'    => $rs->fields['description'],
                        'created'        => $dateString,
                        'starttime'      => $dateString,
                        'changed'        => $dateString,
                        'fk_user'        => 0,
                        'fk_author'      => 0,
                        'local_file'     => $filePath,
                    );

                    $image = new Photo();
                    $newimageID = $image->createFromLocalFile($values);

                    if (is_string($newimageID)) {
                        $ih->logElementInsert($originalImageID, $newimageID, 'image');
                    }
                    echo "new id {$newimageID} [DONE]\n";
                }
                $current++;

                $rs->MoveNext();
            }

            $rs->Close(); # optional
        }
        return $this;
    }


    public function assignMediaToArticles()
    {
        echo "Assigning photos to articles\n";
        $rs = self::$originConn->Execute("SELECT idElemento, idNoticia FROM Elementos WHERE Tipo = 'foto'");

        if (!$rs) {
            ImportHelper::messageStatus(self::$originConn->ErrorMsg());

            return $this;
        }

        $photos = array();
        $originalPhotos = $rs->GetArray();
        foreach ($originalPhotos as $originalPhoto) {
            $photos [$originalPhoto['idElemento']] = $originalPhoto['idNoticia'];
        }

        $sql = "SELECT pk_content_old, pk_content, type FROM translation_ids WHERE type = 'image' OR type = 'article'";
        $rs = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            ImportHelper::messageStatus($GLOBALS['application']->conn->ErrorMsg());

            return $this;
        }
        $importedElements = $rs->GetArray();
        $articleTranslations = $photoTranslations = array();
        foreach ($importedElements as $importedElement) {
            if ($importedElement['type'] == 'article') {
                $articleTranslations [$importedElement['pk_content_old']] = $importedElement['pk_content'];
            } else {
                $photoTranslations  [$importedElement['pk_content_old']] = $importedElement['pk_content'];
            }
        }
        unset($importedElements, $rs);

        foreach ($photos as $originalPhotoId => $originalArticleID) {
            $newImageID   = $photoTranslations[$originalPhotoId];
            $newArticleID = $articleTranslations[$originalArticleID];

            $sql = "UPDATE articles SET img2 = ? WHERE pk_article=?";

            $rs = $GLOBALS['application']->conn->Execute($sql, array($newImageID, $newArticleID));
        }

        return $this;
    }

    public function clearLabelsInBodyArticle($body)
    {
        $result = array();

        $newBody='';
        $img='';
        preg_match_all('/(<a .*?href="(.+?)".*?><img .*?src=".+?".*?><\/a>)/', $body, $result);
        if (!empty($result)) {
            $guid = $result[2][0];

            $newBody = preg_replace('/(<a .*?href="(.+?)".*?><img .*?src=".+?".*?><\/a>)/', '', $body);
            $newBody = ImportHelper::convertoUTF8(strip_tags($newBody, '<p><a><br>'));
        }
        $newBody = preg_replace('/(\[caption .*?\]\[\/caption\])/', '', $newBody);

        return array('img'=>$img, 'body'=>$newBody);
    }

    public function printResults()
    {
        ImportHelper::printResults();
    }
}
