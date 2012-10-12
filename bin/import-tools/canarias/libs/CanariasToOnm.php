<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class CanariasToOnm
{

    public static $originConn = '';

    public $idsMatches = array();

    public $categoriesMatches = array();

    public $categoriesData = array();

    public $helper = null;

    public function __construct($configOldDB = array(), $configNewDB = array())
    {

        // Application::initInternalConstants();

        self::initDatabaseConnections($configOldDB, $configNewDB);
        $this->helper = new CanariasHelper();

    }

    public static function initDatabaseConnections($configOriginDB = array(), $configNewDB = array())
    {

        echo "Initialicing source database connection...".PHP_EOL;
        if (
            isset($configOriginDB['host'])
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
        }

        echo "Initialicing Onm database connection...".PHP_EOL;
        define('BD_HOST', $configNewDB['host']);
        define('BD_USER', $configNewDB['user']);
        define('BD_PASS', $configNewDB['password']);
        define('BD_TYPE', $configNewDB['type']);
        define('BD_DATABASE', $configNewDB['database']);

        $GLOBALS['application'] = new Application();

        Application::initDatabase();
        Application::initLogger();
    }

    /**
     * Explanation for this function.
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function matchInternalCategory($category)
    {

        $category = \Onm\StringUtils::setSeparator(strtolower($category), '-');

        $keys = array_keys($this->categoriesMatches, $category);
        if (empty($keys)) {
            $this->helper->log(" Category not found: {$category} \n ");
            return 20;
        }
        return $keys[0];

    }

    /**
     * Extract category data of old db and insert these in new DB.
     * Drop example categories in new DB.
     *
     * @param array $categories categories ids of used contents
     *
     * @return bool
     */

    public function importCategories()
    {

        $this->categoriesMatches = array(
            1 => 'photo',
            2 => 'publicidad',
            3 => 'album',
            4 => 'opinion',
            5 => 'comment',
            6 => 'video',
            7 => 'author',
            8 => 'portada',
            20 => 'unknown',
            22 => 'deportes',
            23 => 'economía',
            24 => 'politica',
            25 => 'cultura',
            26 => 'sociedad',
            27 => 'musica',
            28 => 'cine',
            29 => 'television',
            30 => 'curiosidades',
            31 => 'fotos-de-hoy',
            32 => 'portadas',
            33 => 'top-secret',
            36 => 'canarias',
            37 => 'nacional',
            38 => 'internacional',
            39 => 'tecnología',
            40 => 'wique',
            41 => 'fauna-por-eduardo-pomares',
            42 => 'educación',
            43 => 'sanidad',
            44 => 'sucesos',
            45 => 'tribunales',
            46 => 'medio-ambiente',
            47 => 'futbol',
            48 => 'baloncesto',
            49 => 'otros-deportes',
            50 => 'derbi',
            51 => 'empresas-de-exito',
            52 => 'gran-canaria',
            53 => 'tenerife',
            54 => 'lanzarote',
            55 => 'fuerteventura',
            56 => 'la-gomera',
            57 => 'la-palma',
            58 => 'el-hierro',
            59 => 'humor',
            60 => 'portadas-del-dia',
            61 => 'elecciones',
            62 => 'ayuntamientos',
            63 => 'canal-energia',
            64 => 'gran-canaria1',
            65 => 'tenerife1',
            66 => 'fuerteventura1',
            67 => 'lanzarote1',
            68 => 'la-gomera1',
            69 => 'la-palma1',
            70 => 'el-hierro1',
            71 => 'sector',
            72 => 'eolica',
            73 => 'solar',
            74 => 'bioenergia',
            75 => 'otras_fuentes',
            76 => 'ahorro',
            77 => 'empleo',
            78 => 'emprendiduria',
            79 => 'empresas-de-éxito',
            80 => 'encuesta-del-dia',
            81 => 'sus-fotos',
            82 => 'frontera',
            83 => 'valverde',
            84 => 'hermigua',
            85 => 'agulo',
            86 => 'club-de-lectores',
            87 => 'elecciones-20n',
            88 => 'exterior',
            89 => 'análisis',
            90 => 'españa',
            91 => 'futbolgc',
            92 => 'futboltf',
            93 => 'motor',
            94 => 'parlamento',
            95 => 'tweet-coberturas',
            96 => 'galerias',
        );

        $this->categoriesData = ContentCategoryManager::get_instance()->categories;

        return false;

    }


    /* create new image */

    public function createImage($data, $dateForDirectory = null)
    {

        $oldPath = OLD_MEDIA.$data['oldName'] ;

        $values = array(
                'title' => utf8_encode($data['name']),
                'category' => $data['category'],
                'fk_category' => $data['category'],
                'category_name'=> $data['category_name'],
                'content_status' => 1,
                'frontpage' => 0,
                'in_home' => 0,
                'metadata' => $data['metadata'],
                'description' => $data['title'],
                'id' => 0,
                'created' => $data['created'],
                'starttime' => $data['created'],
                'changed' => $data['changed'],
                'fk_user' => USER_ID,
                'fk_author' => USER_ID,
                'local_file' => $oldPath,
            );

        $image = new Photo();
        if (is_null($dateForDirectory)) {
            try {
                $date = new DateTime(substr($data['created'], 0, 9));
            } catch (Exception $e) {
                $this->helper->log("problem with date ".$e->getMessage() ." \n ");
                $date = new DateTime();
            }

            $dateForDirectory = date_format($date, "/Y/m/d/");
        }
        $newimageID = $image->createFromLocalFile($values, $dateForDirectory);
        if (is_string($newimageID)) {

            $this->helper->insertImageTranslated($newimageID, $data['oldName'], 'image');

        } else {
            $this->helper->insertfailImport('image', $oldPath);
            $this->helper->log(" Problem with image: {$oldPath} \n ");
        }

        echo "new id {$newimageID} [DONE]\n";
        return $newimageID;
    }
    /**
     * Insert image data in DB
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function importImagesArticles()
    {

        $sql = 'SELECT `id`, `fecha`, `hora`, `seccion`, `subseccion`,
            `islas`, `titulo`, `antetitulo`, `titulo_home`,
            `piedefoto`, `foto_noti_ampliada`, `foto_portada`, `foto_seccion`,
            `foto_panoramica`  FROM `noticias` ';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            while (!$rs->EOF) {

                $photoName = $this->helper->clearImgTag($rs->fields['foto_seccion']);
                $photoName2 = $this->helper->clearImgTag($rs->fields['foto_noti_ampliada']);
                $title = !empty($rs->fields['piedefoto']) ?$rs->fields['piedefoto']:$rs->fields['titulo'];
                $title = utf8_encode($title);
                $slug = \StringUtils::get_title($title);

                if (!$photoName) {
                    $photoName = $this->helper->clearImgTag($rs->fields['foto_portada']);
                }



                if (!$photoName && !$photoName2) {
                    $this->helper->log("{$rs->fields['id']} no photo src  \n");
                } else {

                    // echo("$photoName, $title,  \n");

                     $imageID = $this->helper->imageIsImported($photoName, 'image');

                    if (!empty($imageID)) {
                        echo "[{$current}/{$totalRows}] images with id {$imageID} already imported\n";
                    } else {
                        echo "[{$current}/{$totalRows}] Importing image with id ".
                        "{$rs->fields['id']} - {$rs->fields['subseccion']} - {$rs->fields['seccion']} ";
                        $seccion = !empty($rs->fields['subseccion'])? $rs->fields['subseccion']: $rs->fields['seccion'];
                        $category = $this->matchInternalCategory($seccion);
                        $category_name = $this->categoriesData[$category]->name;

                        $imageData = array(
                            'title' => $title,
                            'name' => $photoName,
                            'available' =>1,
                            'category'=> $category,
                            'category_name'=>  $category_name,
                            'metadata' => \StringUtils::get_tags($slug.', '.$category_name),
                            'description' => $title.' '.$category_name,
                            'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'oldName' => $photoName,
                            'local_file' =>OLD_MEDIA,
                        );

                        if (!empty($photoName)) {
                            $this->createImage($imageData);
                        }
                        if (!empty($photoName2) && ($photoName != $photoName2)) {
                            $imageData['name'] = $photoName2;
                            $imageData['oldName'] = $photoName2;
                            $this->createImage($imageData);
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
     * Insert image fauna  data in DB
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function importImagesFauna()
    {

        $sql = 'SELECT `id`, `fecha`, `hora`, `antetitulo`, `titulo`, `texto`,'.
        ' `foto_gran`, `foto_texto`,   `activar` FROM `fauna` ';


        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            while (!$rs->EOF) {

                $photoName = $this->helper->clearImgTag($rs->fields['foto_gran']);

                $title = !empty($rs->fields['foto_texto']) ?$rs->fields['foto_texto']:$rs->fields['titulo'];
                $title = utf8_encode($title);

                if ($photoName) {
                    $imageID = $this->helper->imageIsImported($photoName, 'image');

                    if (!empty($imageID)) {
                        echo "[{$current}/{$totalRows}] images with id {$imageID} already imported\n";
                    } else {
                        echo "[{$current}/{$totalRows}] Importing image with id ".
                        "{$rs->fields['id']} - {$rs->fields['subseccion']} - {$rs->fields['seccion']} ";
                        $seccion = 'fauna-por-eduardo-pomares';
                        $category = $this->matchInternalCategory($seccion);
                        $category_name = $this->categoriesData[$category]->name;

                        $imageData = array(
                            'title' => $title,
                            'name' => $photoName,
                            'available' =>1,
                            'category'=> $category,
                            'category_name'=>  $category_name,
                            'metadata' => StringUtils::get_tags($slug.', '.$category_name),
                            'description' => $title.' '.$category_name,
                            'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'oldName' => $photoName,
                            'local_file' =>OLD_MEDIA,
                        );

                        $this->createImage($imageData);

                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }
    /**
     * create articles from fauna in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importFauna()
    {

        $this->importImagesFauna();

        $sql = 'SELECT `id`, `fecha`, `hora`, `antetitulo`, `titulo`, `texto`,'.
        ' `foto_gran`, `foto_texto`, `noti_relacion`, `activar` FROM `fauna` ';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log('problem import Fauna'.self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;

            $article = new Article();
            while (!$rs->EOF) {

                $originalArticleID = $rs->fields['id'];

                if ($this->helper->elementIsImported($originalArticleID, 'Fauna') ) {
                    echo "[{$current}/{$totalRows}] Article Fauna with id {$originalArticleID} already imported\n";
                } else {
                    echo "\n [{$current}/{$totalRows}] Importing article Fauna with id {$originalArticleID} - ";
                    $title = !empty($rs->fields['foto_texto']) ?$rs->fields['foto_texto']:$rs->fields['titulo'];
                    $title = utf8_encode($title);

                    $category = $this->matchInternalCategory('fauna-por-eduardo-pomares');
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'subtitle' => $rs->fields['antetitulo'],
                        'agency' => '',
                        'summary' => substr($rs->fields['texto'], 0, 250 . '...'),
                        'body' => $rs->fields['texto'],
                        'img1' =>$this->helper->imageIsImported($rs->fields['foto_gran'], 'image'),
                        'img1_footer' =>$rs->fields['foto_texto'],
                        'img2' =>$this->helper->imageIsImported($rs->fields['foto_gran'], 'image'),
                        'img2_footer' =>$rs->fields['foto_texto'],
                        'category_name'=>  $category_name,
                        'description' => substr($rs->fields['texto'], 0, 120 . '...'),
                        'content_status' => $rs->fields['activar'],
                        'available' => $rs->fields['activar'],
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['foto_texto']),
                        'metadata' => \Onm\StringUtils::get_tags(utf8_encode($rs->fields['foto_texto'])).', fauna',
                        );

                    $articleID = $article->create($data);

                    if (!empty($articleID) ) {
                        $this->helper->insertRefactorID($originalArticleID, $articleID, 'Fauna');

                    } else {
                        $errorMsg = 'Problem '.$originalArticleID.' - '.$title;
                        $this->helper->log('\n insert article : '.$errorMsg);
                        $this->helper->log(
                            "\n Problem inserting article {$rs->fields['titulo']}-{$rs->fields['id']}\n "
                        );
                    }
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

        return true;

    }


     /**
     * Insert image TopSecret  data in DB
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function importImagesTopSecret()
    {

        $sql = 'SELECT `id`, `fecha`, `hora`, `antetitulo`, `titulo`, `texto`,
         `foto_gran`, `foto_texto`, `activar` FROM `topsecret` ';


        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            while (!$rs->EOF) {

                $photoName = $this->helper->clearImgTag($rs->fields['foto_gran']);

                $title = !empty($rs->fields['foto_texto']) ?$rs->fields['foto_texto']:$rs->fields['titulo'];
                $title = utf8_encode($title);
                $slug = \StringUtils::get_title($title);

                if ($photoName) {
                    $imageID = $this->helper->imageIsImported($photoName, 'image');

                    if (!empty($imageID)) {
                        echo "[{$current}/{$totalRows}] images with id {$imageID} already imported\n";
                    } else {
                        echo "[{$current}/{$totalRows}] Importing image with id ".
                        "{$rs->fields['id']} - {$rs->fields['subseccion']} - {$rs->fields['seccion']} ";
                        $seccion = 'top-secret';
                        $category = $this->matchInternalCategory($seccion);
                        $category_name = $this->categoriesData[$category]->name;

                        $imageData = array(
                            'title' => $title,
                            'name' => $photoName,
                            'available' =>1,
                            'category'=> $category,
                            'category_name'=>  $category_name,
                            'metadata' => StringUtils::get_tags($slug.', '.$category_name),
                            'description' => $title.' '.$category_name,
                            'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'oldName' => $photoName,
                            'local_file' =>OLD_MEDIA,
                        );

                        $this->createImage($imageData);

                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }
    /**
     * create articles from topsecret table 33 => 'top-secret', in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importTopSecret()
    {

        $this->importImagesTopSecret();

         $sql = 'SELECT `id`, `fecha`, `hora`, `antetitulo`, `titulo`, `texto`,
         `foto_gran`, `foto_texto`, `activar` FROM `topsecret` ';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log('problem import TopSecret'.self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;

            $article = new Article();
            while (!$rs->EOF) {

                $originalArticleID = $rs->fields['id'];

                if ($this->helper->elementIsImported($originalArticleID, 'TopSecret') ) {
                    echo "[{$current}/{$totalRows}] Article TopSecret with id {$originalArticleID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing article TopSecret with id {$originalArticleID} - ";

                    $title = utf8_encode($rs->fields['titulo']);

                    $category = $this->matchInternalCategory('top-secret');
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'subtitle' => $rs->fields['antetitulo'],
                        'agency' => '',
                        'summary' => substr($rs->fields['texto'], 0, 250 . '...'),
                        'body' => $rs->fields['texto'],
                        'img1' =>$this->helper->imageIsImported($rs->fields['foto_gran'], 'image'),
                        'img1_footer' =>$rs->fields['foto_texto'],
                        'img2' =>$this->helper->imageIsImported($rs->fields['foto_gran'], 'image'),
                        'img2_footer' =>$rs->fields['foto_texto'],
                        'category_name'=>  $category_name,
                        'description' => substr($rs->fields['texto'], 0, 120 . '...'),
                        'content_status' => $rs->fields['activar'],
                        'available' => $rs->fields['activar'],
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['titulo']),
                        );

                    $articleID = $article->create($data);

                    if (!empty($articleID) ) {
                        $this->helper->insertRefactorID($originalArticleID, $articleID, 'TopSecret');

                    } else {
                        $errorMsg = 'Problem '.$originalArticleID.' - '.$title;
                        $this->helper->log('\n insert article : '.$errorMsg);
                        $this->helper->log(
                            "\n Problem inserting article {$rs->fields['title']}-{$rs->fields['pk_content']}\n "
                        );
                    }
                }

                $current++;
                $rs->MoveNext();
            }

        }
        $rs->Close(); # optional
        return true;

    }

    /**
     * create articles from ayuntamientos, in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importAyuntamientos()
    {

         $sql = 'SELECT `noticia`, `fecha`, `hora`, `antetitulo`, `titulo`, '.
         ' `texto`, `isla`, `municipio`, `activar` FROM `ayuntamientos_noticias` ';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log('problem import TopSecret'.self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;

            $article = new Article();
            while (!$rs->EOF) {

                $originalArticleID = $rs->fields['noticia'];

                if ($this->helper->elementIsImported($originalArticleID, 'Ayuntamientos') ) {
                    echo "[{$current}/{$totalRows}] Article Ayuntamientos with ".
                    "id {$originalArticleID} already imported\n";
                } else {
                    echo "\n [{$current}/{$totalRows}] Importing article Ayuntamientos with id {$originalArticleID} - ";

                    $title = utf8_encode($rs->fields['titulo']);

                    $category = $this->matchInternalCategory('top-secret');
                    $category_name = $this->categoriesData[$category]->name;
                    $seccion = !empty($rs->fields['isla']) ?$rs->fields['isla']:'ayuntamientos';
                    $category = $this->matchInternalCategory($seccion);
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'subtitle' => $rs->fields['antetitulo'],
                        'agency' => '',
                        'summary' => substr($rs->fields['texto'], 0, 250 . '...'),
                        'body' => $rs->fields['texto'],
                        'category_name'=>  $category_name,
                        'description' => substr($rs->fields['texto'], 0, 120 . '...'),
                        'content_status' => $rs->fields['activar'],
                        'available' => $rs->fields['activar'],
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['titulo']),
                        );

                    $articleID = $article->create($data);

                    if (!empty($articleID) ) {
                        $this->helper->insertRefactorID($originalArticleID, $articleID, 'Ayuntamientos');

                    } else {
                        $errorMsg = 'Problem '.$originalArticleID.' - '.$title;
                        $this->helper->log('\n insert article : '.$errorMsg);
                        $this->helper->log(
                            "\n Problem inserting article {$rs->fields['title']}-{$rs->fields['pk_content']}\n "
                        );
                    }
                }

                $current++;
                $rs->MoveNext();
            }

        }
        $rs->Close(); # optional
        return true;

    }

    /**
     * create articles from  HemerotecaTopSecret 33 => 'top-secret',  in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importHemerotecaTopSecret()
    {

         $sql = 'SELECT `id`, `titulo`, `antetitulo`, `texto`,
          `fecha`, `hora`
         FROM `hemeroteca_topsecret`';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log('problem'.self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;

            $article = new Article();
            while (!$rs->EOF) {

                $originalArticleID = $rs->fields['id'];
                $title = utf8_encode($rs->fields['titulo']);

                if (empty($title) || $this->helper->elementIsImported($originalArticleID, 'articleOLDTop') ) {
                    echo "[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ";

                    $category = $this->matchInternalCategory('top-secret');
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'subtitle' => $rs->fields['antetitulo'],
                        'agency' => $rs->fields['firma'],
                        'summary' => substr($rs->fields['texto'], 0, 250 . '...'),
                        'body' => $rs->fields['texto'],
                        'category_name'=>  $category_name,
                        'description' => substr($rs->fields['texto'], 0, 120 . '...'),
                        'content_status' =>1,
                        'available' => 1,
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['titulo']),
                        );

                    $articleID = $article->create($data);

                    if (!empty($articleID) ) {
                        $this->helper->insertRefactorID($originalArticleID, $articleID, 'articleOLDTop');

                    } else {
                        $errorMsg = 'Problem '.$originalArticleID.' - '.$title;
                        $this->helper->log('\n insert article : '.$errorMsg);
                        $this->helper->log(
                            "\n Problem inserting article {$rs->fields['title']}-{$rs->fields['pk_content']}\n "
                        );
                    }
                }

                $current++;
                $rs->MoveNext();
            }

        }
        $rs->Close(); # optional
        return true;

    }

     /**
     * create articles from hemeroteca in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importHemeroteca()
    {

         $sql = 'SELECT `id`, `titulo`, `antetitulo`, `entradilla`, `texto`,
         `seccion`, `fecha`, `hora`, `data`, `firma`
         FROM `hemeroteca_noticias`';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log('problem'.self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;

            $article = new Article();
            while (!$rs->EOF) {

                $originalArticleID = $rs->fields['id'];

                if ($this->helper->elementIsImported($originalArticleID, 'articleOLD') ) {
                    echo "[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ";
                    $title= utf8_encode($rs->fields['title']);
                    $seccion = !empty($rs->fields['subseccion']) ?$rs->fields['subseccion']:$rs->fields['seccion'];
                    $category = $this->matchInternalCategory($seccion);
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $rs->fields['titulo'],
                        'category' => $category,
                        'subtitle' => $rs->fields['antetitulo'],
                        'agency' => $rs->fields['firma'],
                        'summary' => $rs->fields['entradilla'],
                        'body' => $rs->fields['texto'],
                        'img1' =>$this->helper->imageIsImported($rs->fields['foto_seccion'], 'image'),
                        'img1_footer' =>$rs->fields['piedefoto'],
                        'img2' =>$this->helper->imageIsImported($rs->fields['foto_noti_ampliada'], 'image'),
                        'img2_footer' =>$rs->fields['piedefoto'],
                        'category_name'=>  $category_name,
                        'description' => $rs->fields['entradilla'],
                        'content_status' => $rs->fields['activar'],
                        'available' => $rs->fields['activar'],
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['titulo']),
                        );

                    $articleID = $article->create($data);

                    if (!empty($articleID) ) {
                        $this->helper->insertRefactorID($originalArticleID, $articleID, 'articleOLD');

                    } else {
                        $errorMsg = 'Problem '.$originalArticleID.' - '.$title;
                        $this->helper->log('\n insert article : '.$errorMsg);
                        $this->helper->log(
                            "\n Problem inserting article {$rs->fields['id']} \n "
                        );
                    }
                }

                $current++;
                $rs->MoveNext();
            }

        }
        $rs->Close(); # optional
        return true;

    }

    /**
     * create articles in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importArticles()
    {

         $sql = 'SELECT `id`, `fecha`, `fecha_activar`, `hora`, `hora_activar`,
            `especiales`, `tipo`, `EFE_motor`, `efe_id`, `version`, `texto_id`,
            `EFE_seccion`, `canal`, `seccion`, `seccion_activar`, `subseccion`,
            `islas`, `titulo`, `antetitulo`, `titulo_home`, `titulo_premium`,
            `antetitulo_premium`, `entradilla_premium`, `antetitulo_home`,
            `entradilla`, `data`, `firma`, `firma_activar`, `piedefoto`,
            `foto_noti_ampliada`, `foto_portada`, `foto_seccion`,
            `foto_panoramica`, `noti_relacion`, `opinion_relacion`,
            `documentos_relacion`, `audio_relacion`, `video_relacion`,
            `galeria_relacion`, `susfotos_relacion`, `encuesta_relacion`,
            `fuerteventuraahora_relacion`, `diversia_relacion`, `activar_diversia`,
            `deportes_portada`, `empresarios_url`, `radio`, `activar`, `texto`
            FROM `noticias` ';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log('problem'.self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;

            $article = new Article();
            while (!$rs->EOF) {

                $originalArticleID = $rs->fields['id'];
                $title =  utf8_encode($rs->fields['titulo']);
                if (empty($title) || $this->helper->elementIsImported($originalArticleID, 'article') ) {
                    echo "[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ";

                    $seccion = !empty($rs->fields['subseccion']) ?$rs->fields['subseccion']:$rs->fields['seccion'];
                    $category = $this->matchInternalCategory($seccion);
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'subtitle' => $rs->fields['antetitulo'],
                        'agency' => $rs->fields['firma'],
                        'summary' => $rs->fields['entradilla'],
                        'body' => $rs->fields['texto'],
                        'img1' => $this->helper->imageIsImported($rs->fields['foto_seccion'], 'image'),
                        'img1_footer' =>$rs->fields['piedefoto'],
                        'img2' => $this->helper->imageIsImported($rs->fields['foto_noti_ampliada'], 'image'),
                        'img2_footer' =>$rs->fields['piedefoto'],
                        'category_name'=>  $category_name,
                        'description' => $rs->fields['entradilla'],
                        'content_status' => $rs->fields['activar'],
                        'available' => $rs->fields['activar'],
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['titulo']),
                        );

                    $articleID = $article->create($data);

                    if (!empty($articleID) ) {
                        $this->helper->insertRefactorID($originalArticleID, $articleID, 'article');

                    } else {
                        $errorMsg = 'Problem '.$originalArticleID.' - '.$title;
                        $this->helper->log('\n insert article : '.$errorMsg);
                        $this->helper->log(
                            "\n Problem inserting article {$rs->fields['title']}-{$rs->fields['pk_content']}\n "
                        );
                    }
                }

                $current++;
                $rs->MoveNext();
            }

        }
        $rs->Close(); # optional
        return true;

    }

    /**
     * Insert author data & photos in old DB
     *
     * @param array $data author information
     *
     * @return bool
     *
     */
    public function importAuthorsOpinion()
    {

        $sql="SELECT distinct(`autor`), `email`, `foto` FROM `opinion` ".
                "ORDER BY `opinion`.`autor` DESC";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            CanariasHelper::log('insertAuthor : '.self::$originConn->ErrorMsg());

            return false;
        }

        $author = new Author();
        $cont=0;

        while (!$rs->EOF) {

            $name =  $this->helper->getSlug($rs->fields['autor']);
            $authorID = $this->helper->authorIsImported($name);

            if (!$authorID) {
                    $values = array(
                        'name'      => utf8_encode($rs->fields['autor']),
                        'fk_user'   =>0,
                        'blog'      => $rs->fields['email'],
                        'politics'  =>'',
                        'condition' =>$name,
                        'date_nac`' =>''
                    );


                    $authorID = $author->create($values);

                    echo "\n new id {$authorID} [DONE]\n";
                    $this->helper->insertAuthorTranslated($authorID, $name);

            } else {
                echo "\n name -".$name.$authorID;
                $cont++;
            }
            $rs->MoveNext();
        }
        $rs->Close(); # optional
        echo "\n Please Check duplicate entries or similar texts in author names. ("
            .$cont." duplicated)\n";
        return true;
    }

    public function importPhotoAuthorsOpinion()
    {

        $sql="SELECT distinct(`autor`), foto, `fecha` FROM `opinion`  WHERE foto !='' ".
                "ORDER BY `opinion`.`autor`  DESC";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            CanariasHelper::log('insertAuthor : '.self::$originConn->ErrorMsg());

            return false;
        }

        $author = new Author();

        while (!$rs->EOF) {
            $photoName = $this->helper->clearImgTag($rs->fields['foto']);
            $name = $this->helper->getSlug($rs->fields['autor']);
            $authorID = $this->helper->authorIsImported($name);

            if (!empty($photoName) && !empty($authorID) ) {

                $photoID = $this->helper->imageIsImported($photoName, 'image');

                if (empty($photoID)) {

                    $author = new Author($authorID);
                    $authorName = $name;
                    $imageData = array(
                        'name' => $name,
                        'category'=> '7',
                        'title' =>utf8_encode($rs->fields['autor']),
                        'category_name'=>'autor',
                        'metadata' => StringUtils::get_tags(utf8_encode($rs->fields['autor'])).', opinion',
                        'description' => $rs->fields['autor'],
                        'created' => $rs->fields['fecha'],
                        'changed' => $rs->fields['fecha'],
                        'oldName' => $photoName,
                    );

                    $photoID = $this->createImage($imageData, '/authors/'.$authorName.'/');

                    $photo = new Photo($photoID);

                    $photoPath = $photo->path_file.$photo->name;

                    $sql2 = "INSERT INTO author_imgs (`fk_author`, `fk_photo`,`path_img`)".
                                " VALUES ( ?, ?, ?)";
                    $values2 = array( $authorID, $photoID, $photoPath );

                    if (!empty($authorID) && !empty($photoID) ) {
                        $rs2 =$GLOBALS['application']->conn->Execute($sql2, $values2);
                        if (!$rs2) {
                            echo($sql2.' '. $GLOBALS['application']->conn->ErrorMsg());
                             $this->helper->log($sql2.' '. $GLOBALS['application']->conn->ErrorMsg()."\n ");
                        }
                    }

                }
            }
            $rs->MoveNext();
        }
        $rs->Close(); # optional
        return true;
    }

    public function importOpinions()
    {

        echo "IMPORTING OPINIONS\n";
        $sql = "SELECT  `id`, `fecha`, `hora`, `titulo`, `antetitulo`, "
            ."`entradilla`, `autor`, `email`, `texto`, `foto`, `orden`, "
            ."`activar`, `nohemeroteca`, `opinion_destacada`, `opinion_jorgebatista`"
            ." FROM `opinion` ORDER BY id ASC ";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        $opinion = new Opinion();

        if (!$rs) {
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            $opinion = new Opinion();

            while (!$rs->EOF) {
                echo 'rs'.$rs->fields['id'];
                 $originalOpinionID = $rs->fields['id'];

                if ($this->helper->elementIsImported($originalOpinionID, 'opinion') ) {
                    echo "[{$current}/{$totalRows}] Opinion with id {$originalOpinionID} already imported\n";
                } else {

                    $name = $this->helper->getSlug($rs->fields['autor']);
                    $authorID = $this->helper->authorIsImported($name);
                    $title= utf8_encode($rs->fields['titulo']);
                    //Check opinion data
                    echo "[{$current}/{$totalRows}] Importing opinion with id {$originalOpinionID} ";
                    $values =
                        array(
                            'title' => $title,
                            'category' => '4',
                            'category_name' => 'opinion',
                            'type_opinion' => 0,
                            'body' => $rs->fields['texto'],
                            'metadata' => \StringUtils::get_tags($titulo.', '.$rs->fields['autor']).', opinion',
                            'description' => 'opinion '.$rs->fields['autor']
                                .' '.strip_tags(substr($rs->fields['texto'], 0, 100)),
                            'fk_author' => $authorID,
                            'available' => $rs->fields['activar'],
                            'with_comment' => 0,
                            'content_status' =>  $rs->fields['activar'],
                            'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'fk_user' => USER_ID,
                            'fk_publisher' => USER_ID,
                            'slug' => \StringUtils::get_title($title),

                        );


                        $newOpinionID = $opinion->create($values);

                    if (is_string($newOpinionID)) {

                        $this->helper->insertRefactorID($originalOpinionID, $newOpinionID, 'opinion');
                        echo "\n new id {$newOpinionID} [DONE]\n";
                    } else {
                         $this->helper->log("\n Problem inserting opinion {$rs->fields['id']}\n ");
                    }

                }

                $current++;
                $rs->MoveNext();
                //var_dump($rs->EOF);
            }

            $rs->Close(); # optional
        }
    }

    /**
     * Insert image humor  data in DB
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function importImagesHumor()
    {

        $sql = 'SELECT `id`, `fecha`, `hora`,  humor_gran , `activar` FROM `humor` ';


        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            while (!$rs->EOF) {

                $photoName = $this->helper->clearImgTag($rs->fields['humor_gran']);

                $title = $photoName;

                if ($photoName) {
                    $imageID = $this->helper->imageIsImported($photoName, 'image');

                    if (!empty($imageID)) {
                        echo "[{$current}/{$totalRows}] images with id {$imageID} already imported\n";
                    } else {
                        echo "[{$current}/{$totalRows}] Importing image with id ".
                        "{$rs->fields['id']} - {$rs->fields['subseccion']} - {$rs->fields['seccion']} ";
                        $seccion = 'humor';
                        $category = $this->matchInternalCategory($seccion);
                        $category_name = $this->categoriesData[$category]->name;

                        $imageData = array(
                            'title' => $title,
                            'name' => $photoName,
                            'available' =>1,
                            'category'=> $category,
                            'category_name'=>  $category_name,
                            'metadata' => StringUtils::get_tags($photoName.', '.$category_name),
                            'description' => $title.' '.$category_name,
                            'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'oldName' => $photoName,
                            'local_file' =>OLD_MEDIA,
                        );

                        $this->createImage($imageData);

                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }
    /**
     * create articles in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importLetters()
    {

        echo "IMPORTING OPINIONS\n";
        $sql ="SELECT `id`, `fecha`, `hora`, `titulo`, `autor`, `texto`, "
            ." `orden`, `activar` FROM `cartas` ";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        $letter = new Letter();

        if (!$rs) {
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;

            while (!$rs->EOF) {
                $originalLetterID = $rs->fields['id'];
                $name = StringUtils::get_title($rs->fields['autor']);

                if ($this->helper->elementIsImported($originalLetterID, 'letter') ) {
                    echo "[{$current}/{$totalRows}] Letter with id {$originalLetterID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing Letter with id {$originalLetterID} - ";

                    $values =
                        array(
                            'title' => $rs->fields['titulo'],
                            'category' => '0',
                            'category_name' => 'letter',
                            'email' => '',
                            'body' => $rs->fields['texto'],
                            'metadata' => StringUtils::get_tags($rs->fields['autor'])
                                .', '. StringUtils::get_tags($rs->fields['titulo']).', carta',
                            'description' => 'opinion '.$rs->fields['autor']
                                .' '.strip_tags(substr($data['texto'], 0, 100)),
                            'author' => $rs->fields['autor'],
                            'available' => $rs->fields['activar'],
                            'with_comment' => 0,
                            'in_home' => 0,
                            'content_status' => $rs->fields['activar'],
                            'created' => $rs->fields['fecha'],
                            'starttime' => $rs->fields['fecha'],
                            'changed' => $rs->fields['fecha'],
                            'fk_user' => USER_ID,
                            'fk_publisher' => USER_ID,
                            'slug' => StringUtils::get_title($rs->fields['titulo']),
                        );

                    $letter = new Letter();
                    $newLetterID = $letter->create($values);

                    if (is_string($newLetterID)) {

                        $this->helper->insertRefactorID($originalLetterID, $newLetterID, 'letter');

                    } else {
                        $this->helper->log("\n Problem inserting letter {$rs->fields['id']} \n ");
                    }
                    echo "new id {$newLetterID} [DONE]\n";

                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }

    /**
     * create albums in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */


    public function importImagesAlbum()
    {

        $sql = 'SELECT `id`, `imagen`, `imagen_peq`, `piedefoto`, `idnoticia` FROM `galeria_fotos`  ';


        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            while (!$rs->EOF) {

                $photoName = $this->helper->clearImgTag($rs->fields['imagen']);

                $title = !empty($rs->fields['piedefoto']) ?utf8_encode($rs->fields['piedefoto']):$photoName;

                if ($photoName) {
                    $imageID = $this->helper->imageIsImported($photoName, 'image');

                    if (!empty($imageID)) {
                        echo "[{$current}/{$totalRows}] images with id {$imageID} already imported\n";
                    } else {
                        echo "[{$current}/{$totalRows}] Importing image with id ".
                        "{$rs->fields['id']} - {$rs->fields['subseccion']} - {$rs->fields['seccion']} ";
                        $seccion = 'galerias';
                        $category = $this->matchInternalCategory($seccion);
                        $category_name = $this->categoriesData[$category]->name;

                        $imageData = array(
                            'title' => $title,
                            'name' => $photoName,
                            'available' =>1,
                            'category'=> $category,
                            'category_name'=>  $category_name,
                            'metadata' => StringUtils::get_tags($photoName.', '.$category_name),
                            'description' => utf8_encode($rs->fields['piedefoto']),
                            'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'oldName' => $photoName,
                            'local_file' =>OLD_MEDIA,
                        );

                        $this->createImage($imageData);

                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }

    public function getAlbumContents($originalAlbumID, $fields)
    {

        $sql2= "SELECT `id`, `imagen`, `imagen_peq`, `piedefoto`, `idnoticia` ".
             "FROM `galeria_fotos` WHERE idnoticia = {$originalAlbumID}";
        $request = self::$originConn->Prepare($sql2);
        $rs = self::$originConn->Execute($request);
        $position =1;
        $album_photos_id = array();
        $album_photos_footer = array();

        while (!$rs->EOF) {
            $imageID = $this->helper->imageIsImported($rs->fields['id'], 'image');
            if (empty($imageID)) {

                $seccion = 'galerias';
                $category = $this->matchInternalCategory($seccion);
                $category_name = $this->categoriesData[$category]->name;

                $name = $rs->fields['imagen'];
                $title = !empty($rs->fields['piedefoto']) ?$rs->fields['piedefoto']: $fields->titulo;

                $imageData = array(
                    'title' => utf8_encode($title),
                    'name' => $name,
                    'category'=> $category,
                    'category_name'=>  $category_name,
                    'metadata' => StringUtils::get_tags($title.','.$category_name),
                    'description' => $title,
                    'created' => $fields['fecha'].' '.$fields['hora'],
                    'changed' => $fields['fecha'].' '.$fields['hora'],
                    'oldName' => 'img_galeria/'.$name,
                );

                $imageID = $this->createImage($imageData);

            }

            if (!empty($imageID)) {
                $album_photos_id[$position] = $imageID;
                $album_photos_footer[$position] = $utf8_encode($rs->fields['piedefoto']);
                $position++;
            }
            $rs->MoveNext();

        }

        $rs->Close(); # optional
        return array($album_photos_id, $album_photos_footer);
    }

    public function importAlbums()
    {

        $this->importImagesAlbum();

        $sql= 'SELECT `id`, `fecha`, `hora`, `titulo`, `firma`, `activar` FROM `rela_galerias` ';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);


        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;

            $album = new Album();
            while (!$rs->EOF) {

                $originalAlbumID = $rs->fields['id'];
                if ($this->helper->elementIsImported($originalAlbumID, 'album') ) {
                    echo "[{$current}/{$totalRows}] Albums with id {$originalAlbumID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing album with id {$originalAlbumID}  \n";
                    $title= utf8_encode($rs->fields['titulo']);
                    $seccion = 'galerias';
                    $category = $this->matchInternalCategory($seccion);
                    $category_name = $this->categoriesData[$category]->name;

                    list($album_photos_id, $album_photos_footer) =
                        $this->getAlbumContents($originalAlbumID, $rs->fields);

                    $data = array(
                        'title' => $title,
                        'category' => $this->matchInternalCategory($rs->fields['pk_fk_content_category']),
                        'with_comment' => 0,
                        'content_status' =>$rs->fields['activar'],
                        'available' => $rs->fields['activar'],
                        'metadata' => \StringUtils::get_tags($rs->fields['title']),
                        'subtitle' => '',
                        'agency' => $rs->fields['firma'],
                        'summary' => '',
                        'fuente' => $rs->fields['firma'],
                        'category_name'=>  $category_name,
                        'description' => $title,
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => \Onm\StringUtils::get_title($title),
                        'album_photos_id' => $album_photos_id,
                        'album_photos_footer'=> $album_photos_footer,
                        'cover_id' => $album_photos_id[1],
                        );

                    $album->cover_id = $album_photos_id[1];

                    $albumID = $album->create($data);

                    if (!empty($albumID) ) {
                        $this->helper->insertRefactorID($originalAlbumID, $albumID->id, 'album');
                        $this->helper->updateCover($albumID->id, $album_photos_id[1]);
                    } else {
                        $errorMsg = 'Problem '.$originalAlbumID.' - '.$title;
                        $this->helper->log('insert album : '.$errorMsg);
                        $this->helper->log("\n Problem inserting album {$rs->fields['id']} \n ");
                    }
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

         return true;

    }


    /**
     * create comments
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importComments()
    {
        $sql = 'SELECT `id`, `fecha`, `hora`, `nombre`, `email`, `asunto`, '.
            ' `texto`, `seccion`, `idnoticia`, `ip_real`, `revisado`, `activar`'.
            ' FROM opinion_lectores'
                .$limit;

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;

            $comment = new Comment();
            while (!$rs->EOF) {

                $originalCommentID = $rs->fields['id'];
                if ($this->helper->elementIsImported($originalCommentID, 'comment') ) {
                    echo "[{$current}/{$totalRows}] comment with id {$originalCommentID} already imported\n";
                } else {
                    $contentId = $this->helper->elementIsImported($$rs->fields['idnoticia'], 'article');
                    $content = new Article($contentId);
                    if (!empty($content->pk_content)) {
                        echo "[{$current}/{$totalRows}] Importing comment with id {$originalCommentID} - ";
                        $title = utf8_encode($rs->fields['asunto']);
                        $category = $content->category;
                        $category_name = $content->category_name;

                        $data = array(
                            'title' => $title,
                            'category' => $category,
                            'summary' => $title,
                            'body' => $rs->fields['texto'],
                            'category_name'=>  $category_name,
                            'description' => $title,
                            'created' => $rs->fields['fecha']." ".$rs->fields['hora'],
                            'changed' => $rs->fields['fecha']." ". $rs->fields['hora'],
                            'starttime' => $rs->fields['fecha']." ".$rs->fields['hora'],
                            'fk_user' => USER_ID,
                            'fk_author' => USER_ID,
                            'slug' => '',
                            'fk_content' => $content->pk_content,
                            'available' => $rs->fields['activar'],
                            );

                        $commentID = $comment->create($data)->id;

                        if (!empty($commentID) ) {
                            $this->helper->insertRefactorID($originalCommentID, $commentID, 'comment');


                        } else {
                            $errorMsg = 'Problem '.$originalCommentID.' - '.$title;
                            $this->helper->log('insert comment : '.$originalCommentID. $errorMsg);
                            $this->helper->log("\n Problem inserting ads {$rs->fields['id']}\n ");
                        }
                    } else {
                        $this->helper->log('insert comment no content: '.$originalCommentID. $contentId);
                    }
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

        return true;

    }


    /**
     * create Attachments & import image ads in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importAttachments()
    {
        $sql = 'SELECT `id`, `fecha`, `hora`, `titulo`, `url`, `activar` FROM rela_documentos';

        //$sql = 'SELECT * FROM rel_radio';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;
            $oldPath = OLD_MEDIA.'/documentos/' ;
            $directoryDate =date("/Y/m/d/");
            $basePath = MEDIA_PATH.'/'.FILE_DIR.$directoryDate ;
             // Create folder if it doesn't exist
            if (!file_exists($basePath) ) {
                mkdir($basePath, 0777, true);
            }

            $att = new Attachment();
            $p = 0;

            while (!$rs->EOF) {

                $originalAdID = $rs->fields['id'];
                if ($this->helper->elementIsImported($originalAdID, 'attachment') ) {
                    echo "[{$current}/{$totalRows}] attachment with id {$originalAdID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing attachment with id {$originalAdID} - ";
                    $title= $rs->fields['asunto'];
                    $name = $rs->fields['url'];
                    $category = 9;
                    $category_name = 'files';
                    $fileName = preg_replace('/[^a-z0-9_\-\.]/i', '-', strtolower($name));
                    $data = array();
                    $data['path'] = $directoryDate.$fileName;
                    $data['title'] = $title;
                    $data['category'] = $category;
                    $data['category_name'] = $category_name;
                    $data['available'] = $rs->fields['activar'];

                    $data['description'] = $title;
                    $data['metadata'] = StringUtils::get_tags($title.', '.$category_name);
                    $data['fk_publisher'] = $_SESSION['userid'];
                    $data['fk_user'] =  USER_ID;
                    $data['fk_author'] = USER_ID;

                    // Move uploaded file
                     $uploadStatus = copy($oldPath."/".$name, $basePath.$fileName);

                    if ($uploadStatus !== false) {

                        if ($att->create($data)) {
                            $adID = $att->id;
                            $this->helper->insertRefactorID($originalAdID, $adID, 'attachment');
                        } else {
                            $errorMsg = 'Problem '.$originalAdID.' - '.$title;
                            $this->helper->log('insert att : '.$errorMsg);
                        }
                    } else {

                        $this->helper->log($p.' - Problem copying attachment '." $name ->  $fileName \n");
                        $this->helper->log($oldPath."/".$name.", ".$basePath.$fileName." \n ");
                        $p++;
                    }
                }
                $current++;
                $rs->MoveNext();
            }

            echo "\n fail $p attachments \n";
            $rs->Close(); # optional
        }

        return true;

    }



    /**
     * create articles in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importRelatedContents()
    {
        $sql = 'SELECT id, opinion_relacion, video_relacion, radio_relacion,
        documentos_relacion, galeria_relacion, encuesta_relacion, noti_relacion FROM noticias ';
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);
        $related = new RelatedContent();
        $position = 0;
        $posinterior = $position;
        $verportada =1;
        $verinterior=1;
        /*
        while (!$rs->EOF) {
            $contentID1 = $this->helper->elementTranslate($rs->fields['id']);
            $contentID2 = $this->helper->elementTranslate($rs->fields['opinion_relacion']);
            if (!empty($contentID1) && !empty($contentID2)) {
                $related->create(
                    $contentID1,
                    $contentID2,
                    $position,
                    $posinterior,
                    $verportada,
                    $verinterior,
                );
            }
            $rs->MoveNext();

        }


        $rs->Close(); # optional
        */
    }
}

