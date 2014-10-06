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
        self::initDatabaseConnections($configOldDB, $configNewDB);
        $this->helper = new CanariasHelper();

    }

    public static function initDatabaseConnections($configOriginDB = array(), $configNewDB = array())
    {
        echo "Initialicing source database connection...".PHP_EOL;
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
        }

        echo "Initialicing Onm database connection...".PHP_EOL;
        define('BD_HOST', $configNewDB['host']);
        define('BD_USER', $configNewDB['user']);
        define('BD_PASS', $configNewDB['password']);
        define('BD_TYPE', $configNewDB['type']);
        define('BD_DATABASE', $configNewDB['database']);

        $GLOBALS['application'] = new Application();

        Application::initDatabase();
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
        $category = $this->helper->convertToUtf8($category);
        $category = \Onm\StringUtils::setSeparator(strtolower($category), '-');
        $key = $this->categoriesMatches[$category];

        if (empty($key)) {
            $this->helper->log(" Category not found: {$category} \n ");
            return 20;
        }
        return $key;

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
            'photo' => 1,
            'publicidad'=> 2,
            'album' => 3,
            'opinion' => 4,
            'comment' => 5,
            'video' => 6,
            'author' => 7,
            'portada' => 8,
            'unknown' => 20,
            'deportes' => 22,
            'economía' => 23,
            'politica' => 24,
            'cultura' => 25,
            'sociedad' => 26,
            'musica' => 27,
            'cine' => 28,
            'television' => 29,
            'curiosidades' => 30,
            'fotos-de-hoy' => 31,
            'portadas' => 32,
            'top-secret' => 33,
            'canarias' => 36,
            'nacional' => 37,
            'internacional'  => 38,
            'tecnología' => 39,
            'wique' => 40,
            'fauna-por-eduardo-pomares' => 41,
            'educación' => 42,
            'sanidad' => 43,
            'sucesos' => 44,
            'tribunales' => 45,
            'medio-ambiente' => 46,
            'futbol' => 47,
            'baloncesto' => 48,
            'otros-deportes' => 49,
            'derbi' => 50,
            'empresas-de-exito' => 51,
            'gran-canaria' => 52,
            'tenerife' => 53,
            'lanzarote' => 54,
            'fuerteventura' => 55,
            'la-gomera'  => 56,
            'la-palma' => 57,
            'el-hierro' => 58,
            'humor' => 59,
            'portadas-del-dia' => 60,
            'elecciones' => 61,
            'ayuntamientos' => 62,
            'canal-energia' => 63,
            'gran-canaria1' => 64,
            'tenerife1' => 65,
            'fuerteventura1' => 66,
            'lanzarote1' => 67,
            'la-gomera1' => 68,
            'la-palma1' => 69,
            'el-hierro1' => 70,
            'sector' => 71,
            'eolica' => 72,
            'solar' => 73,
            'bioenergia' => 74,
            'otras_fuentes' => 75,
            'ahorro' => 76,
            'empleo' => 77,
            'emprendiduria' => 78,
            'empresas-de-éxito' => 79,
            'encuesta-del-dia' => 80,
            'sus-fotos' => 81,
            'frontera' => 82,
            'valverde' => 83,
            'hermigua' => 84,
            'agulo' => 85,
            'club-de-lectores' => 86,
            'elecciones-20n' => 87,
            'exterior' => 88,
            'análisis' => 89,
            'españa' => 37,
            'futbolgc' => 91,
            'futboltf' => 92,
            'motor' => 93,
            'parlamento' => 94,
            'tweet-coberturas' => 95,
            'galerias' => 96,
            'balongc' => 97,
            'cabildos' => 98,
        );

        $this->categoriesData = ContentCategoryManager::get_instance()->categories;

        return false;

    }

    /* create new image */

    public function createImage($data, $dateForDirectory = null)
    {

        $oldPath = OLD_MEDIA.$data['oldName'] ;

        $values = array(
                'title' => $this->helper->convertToUtf8($data['name']),
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
        $newimageID = null;
        try {
            $newimageID = $image->createFromLocalFile($values, $dateForDirectory);
        } catch (Exception $e) {
            $this->helper->insertfailImport('image', $oldPath);
            $this->helper->log(" Problem with image: {$e->getMessage()} {$oldPath} \n ");
        }
        if (is_string($newimageID)) {

            $this->helper->insertImageTranslated($newimageID, $data['oldName'], 'image');

        } else {
            $this->helper->insertfailImport('image', $oldPath);
            $this->helper->log(" Problem with image: {$oldPath} \n ");
            return 'other';
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

        $sql = 'SELECT *  FROM `noticias` ';

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
                $title = $this->helper->convertToUtf8($title);
                $slug = \StringUtils::getTitle($title);

                if (!$photoName) {
                    $photoName = $this->helper->clearImgTag($rs->fields['foto_portada']);
                }



                if (!$photoName && !$photoName2) {
                    // $this->helper->log("{$rs->fields['id']} no photo src  \n");
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
                            'metadata' => \StringUtils::get_tags($title.', '.$category_name),
                            'description' => $title.' '.$category_name,
                            'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'oldName' => $photoName,
                            'local_file' =>OLD_MEDIA,
                        );

                        if (!empty($photoName)) {
                            preg_match(
                                '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
                                $photoName,
                                $match
                            );

                            if (!empty($match[1])) {

                                $imageData['oldName'] = "http://www.youtube.com/watch?v=".$match[1] ;
                                $resp = $this->createVideo($imageData);
                            } else {
                                $resp = $this->createImage($imageData);
                            }
                        }
                        if (!empty($photoName2) && ($photoName != $photoName2)) {
                            $imageData['name'] = $photoName2;
                            preg_match(
                                '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
                                $photoName2,
                                $match
                            );
                            if (!empty($match[1])) {
                                $imageData['oldName'] = "http://www.youtube.com/watch?v=".$match[1];
                                $resp = $this->createVideo($imageData);

                            } else {
                                $imageData['oldName'] = $photoName2;
                                $this->createImage($imageData);
                            }
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
                $title = $this->helper->convertToUtf8($title);

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
                            'metadata' => StringUtils::get_tags($title.', '.$category_name),
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

                if ($this->helper->elementIsImported($originalArticleID, 'Fauna')) {
                    echo "[{$current}/{$totalRows}] Article Fauna with id {$originalArticleID} already imported\n";
                } else {
                    echo "\n [{$current}/{$totalRows}] Importing article Fauna with id {$originalArticleID} - ";
                    $title = !empty($rs->fields['foto_texto']) ?$rs->fields['foto_texto']:$rs->fields['titulo'];
                    $title = $this->helper->convertToUtf8($title);

                    $category = $this->matchInternalCategory('fauna-por-eduardo-pomares');
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'subtitle' =>  $this->helper->convertToUtf8($rs->fields['antetitulo']),
                        'agency' => '',
                        'summary'=>substr($this->helper->convertToUtf8($rs->fields['texto']), 0, 250),
                        'body' =>  $this->helper->convertToUtf8($rs->fields['texto']),
                        'img1' => $this->helper->imageIsImported(
                            $this->helper->clearImgTag($rs->fields['foto_gran']),
                            'image'
                        ),
                        'img1_footer' =>  $this->helper->convertToUtf8($rs->fields['foto_texto']),
                        'img2' => $this->helper->imageIsImported(
                            $this->helper->clearImgTag($rs->fields['foto_gran']),
                            'image'
                        ),
                        'img2_footer' =>  $this->helper->convertToUtf8($rs->fields['foto_texto']),
                        'category_name'=>  $category_name,
                        'description' => substr($this->helper->convertToUtf8($rs->fields['texto']), 0, 120),
                        'content_status' => ($rs->fields['activar'] == 0)? 0:1,
                        'available' => ($rs->fields['activar'] == 0)? 0:1,
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['foto_texto']),
                        'metadata' => \Onm\StringUtils::get_tags(
                            'fauna',
                            $this->helper->convertToUtf8($rs->fields['foto_texto'])
                        ),
                        );

                    $articleID = $article->create($data);

                    if (!empty($articleID)) {
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
                $title = $this->helper->convertToUtf8($title);
                $slug = \StringUtils::getTitle($title);

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
                            'metadata' => StringUtils::get_tags($title.', '.$category_name),
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

                if ($this->helper->elementIsImported($originalArticleID, 'TopSecret')) {
                    echo "[{$current}/{$totalRows}] Article TopSecret with id {$originalArticleID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing article TopSecret with id {$originalArticleID} - ";

                    $title = $this->helper->convertToUtf8($rs->fields['titulo']);

                    $category = $this->matchInternalCategory('top-secret');
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'subtitle' => $this->helper->convertToUtf8($rs->fields['antetitulo']),
                        'agency' => '',
                        'metadata' => \StringUtils::get_tags($title.', '.$category_name),
                        'summary' => substr($this->helper->convertToUtf8($rs->fields['texto']), 0, 250 . '...'),
                        'body' => $this->helper->convertToUtf8($rs->fields['texto']),
                        'img1' =>$this->helper->imageIsImported(
                            $this->helper->clearImgTag($rs->fields['foto_gran']),
                            'image'
                        ),
                        'img1_footer' => $this->helper->convertToUtf8($rs->fields['foto_texto']),
                        'img2' =>$this->helper->imageIsImported(
                            $this->helper->clearImgTag($rs->fields['foto_gran']),
                            'image'
                        ),
                        'img2_footer' =>$this->helper->convertToUtf8($rs->fields['foto_texto']),
                        'category_name'=>  $category_name,
                        'description' => substr($this->helper->convertToUtf8($rs->fields['texto']), 0, 120 . '...'),
                        'content_status' => ($rs->fields['activar'] == 0)? 0:1,
                        'available' => ($rs->fields['activar'] == 0)? 0:1,
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['titulo']),
                        );

                    $articleID = $article->create($data);

                    if (!empty($articleID)) {
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

    public function importMunicipios()
    {

        $this->categoriesMatches = array();

        $sql = "SELECT islas FROM `ayuntamientos_islas`";
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log('problem import categories = islas'.self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;
            $category = new \ContentCategory();

            while (!$rs->EOF) {
                $title = $this->helper->convertToUtf8(strtolower($rs->fields['islas']));
                $data = array();
                $data['name'] = \StringUtils::normalizeName($title);
                $data['title'] = $title;
                $data['inmenu']=1;
                $data['subcategory'] =0;
                $data['internal_category'] =1;
                $data['logo_path'] ='';
                $data['color']  ='';
                $data['params']  ='';

                if ($category->create($data)) {
                    $this->helper->log("added municipio {$title} \n");
                    $title = \Onm\StringUtils::setSeparator(strtolower($title), '-');
                    $this->categoriesMatches[$title] = $category->pk_content_category;
                } else {
                    $this->helper->log("problem creating {$title} \n");
                }

                $rs->MoveNext();
            }
        }

        $sql = 'SELECT `municipio`, islas FROM `ayuntamientos_municipios` ORDER BY islas DESC';
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log('problem import categories = municipios'.self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;
            $category = new \ContentCategory();

            while (!$rs->EOF) {
                $title = $this->helper->convertToUtf8(strtolower($rs->fields['municipio']));
                $isla = $this->helper->convertToUtf8(strtolower($rs->fields['islas']));
                $isla = \Onm\StringUtils::setSeparator(strtolower($isla), '-');

                $data = array();
                $data['name'] = \StringUtils::normalizeName($title);
                $data['title'] = $title;
                $data['inmenu']=1;
                $data['subcategory'] = $this->categoriesMatches[$isla];
                $data['internal_category'] =1;
                $data['logo_path'] ='';
                $data['color']  ='';
                $data['params']  ='';

                if ($category->create($data)) {
                    $title = \Onm\StringUtils::setSeparator(strtolower($title), '-');
                    $this->helper->log("added municipio {$title} \n");
                    $this->categoriesMatches[$title] = $category->pk_content_category;
                } else {
                    $this->helper->log("problem creating {$title} \n");
                }

                $rs->MoveNext();
            }
        }
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
      //  $this->importMunicipios();

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

                if ($this->helper->elementIsImported($originalArticleID, 'Ayuntamientos')) {
                    echo "[{$current}/{$totalRows}] Article Ayuntamientos with ".
                    "id {$originalArticleID} already imported\n";
                } else {
                    $title = $this->helper->convertToUtf8($rs->fields['titulo']);

                    $seccion = !empty($rs->fields['municipio']) ?$rs->fields['municipio']: $rs->fields['isla'];
                    $category = $this->matchInternalCategory($seccion);
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'subtitle' => $this->helper->convertToUtf8($rs->fields['antetitulo']),
                        'agency' => '',
                        'metadata' => \StringUtils::get_tags($title.', '.$category_name),
                        'summary' => substr($this->helper->convertToUtf8($rs->fields['texto']), 0, 250 . '...'),
                        'body' =>$this->helper->convertToUtf8($rs->fields['texto']),
                        'category_name'=>  $category_name,
                        'description' => substr($this->helper->convertToUtf8($rs->fields['texto']), 0, 120 . '...'),
                        'content_status' => ($rs->fields['activar'] == 0)? 0:1,
                        'available' => ($rs->fields['activar'] == 0)? 0:1,
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['titulo']),
                        );

                    $articleID = $article->create($data);
                    echo "\n [{$current}/{$totalRows}] Importing article "
                        ."Ayuntamientos with id {$originalArticleID} - in {$seccion} ";
                    if (!empty($articleID)) {
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
                $title = $this->helper->convertToUtf8($rs->fields['titulo']);

                if ($this->helper->elementIsImported($originalArticleID, 'articleOLDTop')) {
                    echo "[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n";
                } elseif (!empty($title)) {
                    echo "[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ";

                    $category = $this->matchInternalCategory('top-secret');
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'subtitle' => $this->helper->convertToUtf8($rs->fields['antetitulo']),
                        'agency' => $this->helper->convertToUtf8($rs->fields['firma']),
                        'summary' => substr($this->helper->convertToUtf8($rs->fields['texto']), 0, 250 . '...'),
                        'body' => $this->helper->convertToUtf8($rs->fields['texto']),
                        'category_name'=>  $category_name,
                        'metadata' => \StringUtils::get_tags($title.', '.$category_name),
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

                    if (!empty($articleID)) {
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

                if ($this->helper->elementIsImported($originalArticleID, 'articleOLD')) {
                    echo "[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n";
                } elseif (!empty($title)) {
                    echo "[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ";
                    $title= $this->helper->convertToUtf8($rs->fields['title']);
                    $seccion = !empty($rs->fields['subseccion']) ?$rs->fields['subseccion']:$rs->fields['seccion'];
                    $category = $this->matchInternalCategory($seccion);
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'subtitle' => $this->helper->convertToUtf8($rs->fields['antetitulo']),
                        'agency' => $this->helper->convertToUtf8($rs->fields['firma']),
                        'summary' => $this->helper->convertToUtf8($rs->fields['entradilla']),
                        'body' => $this->helper->convertToUtf8($rs->fields['texto']),
                        'img1' => $this->helper->imageIsImported(
                            $this->helper->clearImgTag($rs->fields['foto_seccion']),
                            'image'
                        ),
                        'img1_footer' => $this->helper->convertToUtf8($rs->fields['piedefoto']),
                        'img2' =>$this->helper->imageIsImported(
                            $this->helper->clearImgTag($rs->fields['foto_noti_ampliada']),
                            'image'
                        ),
                        'img2_footer' => $this->helper->convertToUtf8($rs->fields['piedefoto']),
                        'category_name'=>  $category_name,
                        'description' => $this->helper->convertToUtf8($rs->fields['entradilla']),
                        'content_status' => 1,
                        'available' => 1,
                        'metadata' => \StringUtils::get_tags($title.', '.$category_name),
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['titulo']),
                        );

                    $articleID = $article->create($data);

                    if (!empty($articleID)) {
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

         $sql = 'SELECT * FROM `noticias` ';

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
                $title =  $this->helper->convertToUtf8($rs->fields['titulo']);
                if ($this->helper->elementIsImported($originalArticleID, 'article')) {
                    echo "[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n";
                } elseif (!empty($title)) {
                    echo "[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ";

                    $seccion = !empty($rs->fields['subseccion']) ?$rs->fields['subseccion']:$rs->fields['seccion'];
                    $category = $this->matchInternalCategory($seccion);
                    $category_name = $this->categoriesData[$category]->name;

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'metadata' => \StringUtils::get_tags($title.', '.$category_name),
                        'subtitle' => $this->helper->convertToUtf8($rs->fields['antetitulo']),
                        'agency' => $this->helper->convertToUtf8($rs->fields['firma']),
                        'summary' => $this->helper->convertToUtf8($rs->fields['entradilla']),
                        'body' => $this->helper->convertToUtf8($rs->fields['texto']),
                        'img1' => $this->helper->imageIsImported(
                            $this->helper->clearImgTag($rs->fields['foto_seccion']),
                            'image'
                        ),
                        'img1_footer' => $this->helper->convertToUtf8($rs->fields['piedefoto']),
                        'img2' => $this->helper->imageIsImported(
                            $this->helper->clearImgTag($rs->fields['foto_noti_ampliada']),
                            'image'
                        ),
                        'img2_footer' =>$this->helper->convertToUtf8($rs->fields['piedefoto']),
                        'category_name'=>  $category_name,
                        'description' => $this->helper->convertToUtf8($rs->fields['entradilla']),
                        'content_status' => ($rs->fields['activar'] == 0)? 0:1,
                        'available' => ($rs->fields['activar'] == 0)? 0:1,
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $this->helper->getSlug($rs->fields['titulo']),
                        );

                    $articleID = $article->create($data);

                    if (!empty($articleID)) {
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
            $rs->Close(); # optional
        }

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

    public function createDefaultAuthors()
    {

        $sql = "INSERT INTO `authors` "
            ."(`pk_author`, `name`, `politics`, `date_nac`, `fk_user`, `condition`, `blog`, `params`) "
            ."VALUES "
            ."(3, 'Colaboradores', '', '0000-00-00 00:00:00', 0, '', '', NULL) ";

        $request = $GLOBALS['application']->conn->Prepare($sql);
        $rs = $GLOBALS['application']->conn->Execute($request);

        if (!$rs) {
            CanariasHelper::log('insertAuthor : '.self::$originConn->ErrorMsg());

            return false;
        }
    }

    public function isOpinionAuthors($name)
    {
        /*
        Belén Molina, Thalía Rodríguez, Alexis González
         Noé Ramón  Jaime Puig, Martín Macho,

        */

        $editorial = array('belen-molina', 'thalia-rodriguez', 'alexis-gonzalez', 'editorial',
         'noe-ramon', 'jaime-puig', 'martin-macho');
        if (in_array($name, $editorial)) {
            return 1;
        }
        /*
        José A. Alemán, Rafael González Morera, Antonio González Viéitez, Faustino García Márquez
        Cristóbal D. Peñate, Salvador García Llanos, Octavio Hernández, Francisco Morote Costa
        Joaquín Sagaseta de Ilurdoz Paradas, Eduardo Serradilla Sanchís
        */

        $authors = array(
            'jose-aleman', 'jose-a-aleman',
            'antonio-gonzalez-vieitez',
            'francisco-morote',
            'cristobal-penate', 'cristobal-peate', 'cristobal-d-penate',
            'eduardo-serradilla',
            'faustino-garcia-marquez',
            'joaquin-sagaseta-de-ilurdoz-paradas', 'joaquin-sagaseta',
            'octavio-hernandez',
            'rafael-gonzalez-morera',
            'salvador-garcia-llanos',
        );

        if (in_array($name, $authors)) {
            return 0;
        }

        $director = array ('carlos-sosa'); //carlos sosa
        if (in_array($name, $director)) {
            return 2;
        }

        return 3;

    }

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

            if (!empty($name)) {  //&& $this->isOpinionAuthors($name) == 0) {
                $authorID = $this->helper->authorIsImported($name);

                if (!$authorID) {
                        $values = array(
                            'name'      => $this->helper->convertToUtf8($rs->fields['autor']),
                            'fk_user'   =>0,
                            'blog'      => $rs->fields['email'],
                            'politics'  =>'',
                            'condition' =>$name,
                            'date_nac`' =>''
                        );

                        $authorID = $author->create($values);

                        echo "\n new id {$authorID} [DONE]\n";
                        $this->helper->insertAuthorTranslated($authorID, $name);

                }
                $cont++;

            }
            echo '.';
            $rs->MoveNext();
        }
        $rs->Close(); # optional

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

            $name = $this->helper->getSlug($rs->fields['autor']);

            if ($this->isOpinionAuthors($name) == 0) {
                $photoName = $this->helper->clearImgTag($rs->fields['foto']);
                $authorID = $this->helper->authorIsImported($name);

                if (!empty($photoName) && !empty($authorID)) {

                    $photoID = $this->helper->imageIsImported($photoName, 'image');

                    if (empty($photoID)) {

                        $author = new Author($authorID);
                        $authorName = $name;
                        $imageData = array(
                            'name' => $name,
                            'category'=> '7',
                            'title' =>$this->helper->convertToUtf8($rs->fields['autor']),
                            'category_name'=>'autor',
                            'metadata' => StringUtils::get_tags($this->helper->convertToUtf8($rs->fields['autor'])).', opinion',
                            'description' => $this->helper->convertToUtf8($rs->fields['autor']),
                            'created' => $rs->fields['fecha'],
                            'starttime' => $rs->fields['fecha'],
                            'changed' => $rs->fields['fecha'],
                            'oldName' => $photoName,
                        );

                        $photoID = $this->createImage($imageData, '/authors/'.$authorName.'/');

                        $photo = new Photo($photoID);

                        $photoPath = $photo->path_file.$photo->name;

                        $sql2 = "INSERT INTO author_imgs (`fk_author`, `fk_photo`,`path_img`)".
                                    " VALUES ( ?, ?, ?)";
                        $values2 = array( $authorID, $photoID, $photoPath );

                        if (!empty($authorID) && !empty($photoID)) {
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

                if ($this->helper->elementIsImported($originalOpinionID, 'opinion')) {
                    echo "[{$current}/{$totalRows}] Opinion with id {$originalOpinionID} already imported\n";
                } elseif (!empty($rs->fields['titulo'])) {
                    $name = $this->helper->getSlug($rs->fields['autor']);

                    $fkAuthor = 0;
                    $typeOpinion = 0;//$this->isOpinionAuthors($name);
                    $colab ='';
                    if ($typeOpinion == 3) {
                        //Colaboradores
                        $typeOpinion = 0;
                        $fkAuthor = 3;
                        $colab =  $this->helper->convertToUtf8($rs->fields['autor']);
                    } elseif ($typeOpinion == 0) {
                        $fkAuthor = $this->helper->authorIsImported($name);
                    }

                    $title = $this->helper->convertToUtf8($rs->fields['titulo']. ' por '.$rs->fields['autor']);
                    //Check opinion data
                    echo "[{$current}/{$totalRows}] Importing opinion with id {$originalOpinionID} ";
                    $values =
                        array(
                            'title' => $title .$colab,
                            'category' => '4',
                            'category_name' => 'opinion',
                            'type_opinion' => $typeOpinion,
                            'body' => $this->helper->convertToUtf8($rs->fields['texto'])." <br> ".$colab,
                            'metadata' => \StringUtils::get_tags($title.',  opinion', $colab),
                            'description' => $this->helper->convertToUtf8('opinion '.strip_tags(substr($rs->fields['texto'], 0, 100))),
                            'fk_author' => $fkAuthor,
                            'available' => 1,
                            'with_comment' => 0,
                            'content_status' => 1,
                            'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                            'fk_user' => USER_ID,
                            'fk_publisher' => USER_ID,
                            'slug' => \StringUtils::getTitle($title),

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
                $name = StringUtils::getTitle($rs->fields['autor']);

                if ($this->helper->elementIsImported($originalLetterID, 'letter')) {
                    echo "[{$current}/{$totalRows}] Letter with id {$originalLetterID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing Letter with id {$originalLetterID} - ";

                    $values =
                        array(
                            'title' => $this->helper->convertToUtf8($rs->fields['titulo']),
                            'category' => '0',
                            'category_name' => 'letter',
                            'email' => '',
                            'body' => $this->helper->convertToUtf8($rs->fields['texto']),
                            'metadata' => StringUtils::get_tags($rs->fields['autor'])
                                .', '. StringUtils::get_tags($rs->fields['titulo']).', carta',
                            'description' => 'opinion '.$rs->fields['autor']
                                .' '.strip_tags(substr($this->helper->convertToUtf8($data['texto']), 0, 100)),
                            'author' => $rs->fields['autor'],
                            'available' => ($rs->fields['activar'] == 0)? 0:1,
                            'with_comment' => 0,
                            'in_home' => 0,
                            'content_status' => ($rs->fields['activar'] == 0)? 0:1,
                            'created' => $rs->fields['fecha'],
                            'starttime' => $rs->fields['fecha'],
                            'changed' => $rs->fields['fecha'],
                            'fk_user' => USER_ID,
                            'fk_publisher' => USER_ID,
                            'slug' => StringUtils::getTitle($rs->fields['titulo']),
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

                $title = !empty($rs->fields['piedefoto']) ?$this->helper->convertToUtf8($rs->fields['piedefoto']):$photoName;

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
                            'description' => $this->helper->convertToUtf8($rs->fields['piedefoto']),
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
                    'title' => $this->helper->convertToUtf8($title),
                    'name' => $name,
                    'category'=> $category,
                    'category_name'=>  $category_name,
                    'metadata' => StringUtils::get_tags($title.','.$category_name),
                    'description' => $title,
                    'created' => $fields['fecha'].' '.$fields['hora'],
                    'changed' => $fields['fecha'].' '.$fields['hora'],
                    'oldName' => '/img_galeria/'.$name,
                );

                $imageID = $this->createImage($imageData);

            }

            if (!empty($imageID)) {
                $album_photos_id[$position] = $imageID;
                $album_photos_footer[$position] = $this->helper->convertToUtf8($rs->fields['piedefoto']);
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
                if ($this->helper->elementIsImported($originalAlbumID, 'album')) {
                    echo "[{$current}/{$totalRows}] Albums with id {$originalAlbumID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing album with id {$originalAlbumID}  \n";
                    $title= $this->helper->convertToUtf8($rs->fields['titulo']);
                    $seccion = 'galerias';
                    $category = $this->matchInternalCategory($seccion);
                    $category_name = $this->categoriesData[$category]->name;

                    list($album_photos_id, $album_photos_footer) =
                        $this->getAlbumContents($originalAlbumID, $rs->fields);

                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'with_comment' => 0,
                        'content_status' => ($rs->fields['activar'] == 0)? 0:1,
                        'available' => ($rs->fields['activar'] == 0)? 0:1,
                        'metadata' => \StringUtils::get_tags($rs->fields['title']),
                        'subtitle' => '',
                        'agency' => $this->helper->convertToUtf8($rs->fields['firma']),
                        'summary' => '',
                        'fuente' => $this->helper->convertToUtf8($rs->fields['firma']),
                        'category_name'=>  $category_name,
                        'description' => $title,
                        'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'starttime' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => \Onm\StringUtils::getTitle($title),
                        'album_photos_id' => $album_photos_id,
                        'album_photos_footer'=> $album_photos_footer,
                        'cover_id' => $album_photos_id[1],
                        );

                    $album->cover_id = $album_photos_id[1];

                    $albumID = $album->create($data);

                    if (!empty($albumID)) {
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
            if (!file_exists($basePath)) {
                mkdir($basePath, 0777, true);
            }

            $att = new Attachment();
            $p = 0;

            while (!$rs->EOF) {

                $originalAdID = $rs->fields['id'];
                if ($this->helper->elementIsImported($originalAdID, 'attachment')) {
                    echo "[{$current}/{$totalRows}] attachment with id {$originalAdID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing attachment with id {$originalAdID} - ";
                    $title= $this->helper->convertToUtf8($rs->fields['titulo']);
                    $name = $rs->fields['url'];
                    $category = 9;
                    $category_name = 'files';
                    $fileName = preg_replace('/[^a-z0-9_\-\.]/i', '-', strtolower($name));
                    $data = array();
                    $data['path'] = $directoryDate.$fileName;
                    $data['title'] = $title;
                    $data['category'] = $category;
                    $data['category_name'] = $category_name;
                    $data['available'] = 1;

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
     * create Videos & import image ads in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importVideos()
    {
        $sql = 'SELECT `id`, `fecha`, `hora`, `titulo`, `texto`, `imagen`, `video`, `activar` FROM `videos`';

        //$sql = 'SELECT * FROM rel_radio';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;

            while (!$rs->EOF) {
                if (!empty($rs->fields['video'])) {
                    $originalAdID = $rs->fields['id'];
                    if ($this->helper->elementIsImported($originalAdID, 'video')) {
                        echo "[{$current}/{$totalRows}] video with id {$originalAdID} already imported\n";
                    } else {
                        echo "[{$current}/{$totalRows}] Importing video with id {$originalAdID} - ";
                        $title = $this->helper->convertToUtf8($rs->fields['titulo']);
                        $seccion = 'otros';
                        $category = $this->matchInternalCategory($seccion);
                        $category_name = $this->categoriesData[$category]->name;
                        $videoData = array(
                                'title' => $title,
                                'name' => $rs->fields['video'],
                                'available' =>1,
                                'category'=> $category,
                                'metadata' => StringUtils::get_tags($photoName),
                                'description' => $rs->fields['texto'],
                                'created' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                                'changed' => $rs->fields['fecha'].' '.$rs->fields['hora'],
                                'oldName' => $rs->fields['video'],
                                'author_name' => 'internal',
                            );
                            $this->createVideo($videoData, 'file');
                    }
                }
                $current++;
                $rs->MoveNext();
            }

            echo "\n fail $originalAdID video \n";
            $rs->Close(); # optional
        }

        return true;

    }


     /* create new video */

    public function createVideo($data, $type = 'web-source', $dateForDirectory = null)
    {
        $newVideoID = null;
        if ($type === 'file') {
            $oldFile = OLD_MEDIA.'/videos/'.$data['oldName'];
            // Check if the video file entry was completed
            if (!(file_exists($oldFile))
            ) {
                $this->helper->log(" Problem no exists video file:   {$oldFile} \n ");
            } else {
                $videoFileData = array(
                    'file_type'      => 'video/x-flv', //$data['type']
                    'file_path'      => $oldFile,
                    'category'       => $data['category'],
                    'available'      => 1,
                    'content_status' => 1,
                    'title'          => $data['title'],
                    'metadata'       => $data['metadata'],
                    'description'    => $data['description'],
                    'author_name'    => $data['author_name'],
                );

                try {
                    $video = new \Video();
                    $newVideoID = $video->createFromLocalFile($videoFileData);
                    $this->helper->insertImageTranslated($newVideoID, $oldFile, 'video');
                } catch (\Exception $e) {
                    $this->helper->log(" Problem with video file: {$e->getMessage()} {$oldFile} \n ");
                }
            }

        } elseif ($type == 'web-source') {
            var_dump($data['oldName']);

            $url = rawurldecode($data['oldName']);

            if ($url) {
                try {
                    $videoP = new \Panorama\Video($url);
                    $information = $videoP->getVideoDetails();
                    $values = array(
                        'file_path'      => $url,
                        'video_url'      => $url,
                        'category'       => $data['category'],
                        'available'      => 1,
                        'content_status' => 1,
                        'title'          => $this->helper->convertToUtf8($data['title']),
                        'metadata'       => $data['metadata'],
                        'description'    => $data['name'].' video '.$data['texto'],
                        'author_name'    => $data['origin'],
                    );

                } catch (\Exception $e) {
                    $this->helper->log("\n 1 Can't get video information. Check the $url");
                    return;
                }

                $video = new \Video();
                $values['information'] = $information;

                try {
                    $newVideoID = $video->create($values);
                } catch (\Exception $e) {
                    $this->helper->insertfailImport('video', $e->getMessage());
                    $this->helper->log("1 Problem with video: {$e->getMessage()} {$url} \n ");

                    var_dump($information);

                }
                if (is_string($newVideoID)) {
                    $this->helper->insertImageTranslated($newVideoID, $url, 'video');
                } else {
                    $this->helper->insertfailImport('video', $url);
                    $this->helper->log("2 Problem with video: {$url} \n ");
                }

            } else {
                $this->helper->log("There was an error while uploading the form, not all the required data was sent.");

            }
        } else {
            $this->helper->log("There was an error while uploading the form, the video type is not specified.");
        }

        echo "new id {$newVideoID} [DONE]\n";
        return $newVideoID;
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
        documentos_relacion, galeria_relacion,  noti_relacion FROM noticias ';
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


    public function importPolls()
    {


        $sql= 'SELECT `id`, `fecha`, `seccion`, `pregunta`, `foto`, '
            .'`respuesta1`, `respuesta2`, `respuesta3`, `respuesta4`, '
            .'`voto1`, `voto2`, `voto3`, `voto4`, `cerrar`, `activar` FROM `encuestas` ';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);


        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;

            $poll = new Poll();
            while (!$rs->EOF) {

                $originalPollID = $rs->fields['id'];
                if ($this->helper->elementIsImported($originalPollID, 'poll')) {
                    echo "[{$current}/{$totalRows}] Polls with id {$originalPollID} already imported\n";
                } elseif (!empty($rs->fields['pregunta']) && !empty($rs->fields['respuesta1'])) {
                    echo "[{$current}/{$totalRows}] Importing poll with id {$originalPollID}  \n";
                    $title= $this->helper->convertToUtf8($rs->fields['pregunta']);

                    $seccion = 'encuesta-del-dia';
                    $category = '80';
                    $category_name = 'Encuesta del día';


                    $data = array(
                        'title' => $title,
                        'category' => $category,
                        'with_comment' => 0,
                        'content_status' => ($rs->fields['activar'] === 0)? 0:1,
                        'available' => ($rs->fields['activar'] === 0)? 0:1,
                        'metadata' => \StringUtils::get_tags($title),
                        'subtitle' => '',
                        'agency' => '',
                        'summary' => '',
                        'fuente' => '',
                        'category_name'=>  $category_name,
                        'description' => $title,
                        'created' => $rs->fields['fecha'].' 00:00:00',
                        'changed' => $rs->fields['fecha'].' 00:00:00',
                        'starttime' => $rs->fields['fecha'].' 00:00:00',
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => \Onm\StringUtils::getTitle($title),
                        'visualization' => 1,
                        );

                    $data['item'] = array();
                    $total_votes = 0;

                    if (!empty($rs->fields['respuesta1'])) {
                        $data['item'][] = $this->helper->convertToUtf8($rs->fields['respuesta1']);
                        $total_votes  += (int)$rs->fields['voto1'];
                    }
                    if (!empty($rs->fields['respuesta2'])) {
                        $data['item'][] = $this->helper->convertToUtf8($rs->fields['respuesta2']);
                        $total_votes += (int)$rs->fields['voto2'];
                    }
                    if (!empty($rs->fields['respuesta3'])) {
                        $data['item'][] = $this->helper->convertToUtf8($rs->fields['respuesta3']);
                        $total_votes += (int)$rs->fields['voto3'];
                    }
                    if (!empty($rs->fields['respuesta4'])) {
                        $data['item'][] = $this->helper->convertToUtf8($rs->fields['respuesta4']);
                        $total_votes  += (int)$rs->fields['voto4'];
                    }

                    $poll->create($data);

                    $votes = array();
                    $i=0;
                    if (!empty($rs->fields['respuesta1'])) {
                        $votes[$i] = array($rs->fields['voto1'], $poll->id, $this->helper->convertToUtf8($rs->fields['respuesta1']));

                        $i++;
                    }
                    if (!empty($rs->fields['respuesta2'])) {
                        $votes[$i] = array($rs->fields['voto2'], $poll->id, $this->helper->convertToUtf8($rs->fields['respuesta2']));
                        $i++;
                    }
                    if (!empty($rs->fields['respuesta3'])) {
                        $votes[$i] = array($rs->fields['voto3'], $poll->id, $this->helper->convertToUtf8($rs->fields['respuesta3']));
                        $i++;
                    }
                    if (!empty($rs->fields['respuesta4'])) {
                        $votes[$i] = array($rs->fields['voto4'], $poll->id, $this->helper->convertToUtf8($rs->fields['respuesta4']));
                        $i++;
                    }


                    if (!empty($poll->id)) {
                        $this->helper->insertRefactorID($originalPollID, $poll->id, 'poll');
                        $this->helper->updatePolls($total_votes, $poll->id);
                        $this->helper->updatePollVotes($votes);
                    } else {
                        $errorMsg = 'Problem '.$originalPollID.' - '.$title;
                        $this->helper->log('insert poll : '.$errorMsg);
                        $this->helper->log("\n Problem inserting poll {$rs->fields['id']} \n ");
                    }
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

         return true;

    }



    public function getFilesData()
    {

        $sql = "SELECT * FROM `rela_documentos` ";
        $rs  = self::$originConn->Execute($sql);
        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;

            $handle = fopen(__DIR__."/../log/updateFiles.txt", "w");
            while (!$rs->EOF) {
                $id = $this->helper->elementIsImported($rs->fields['id'], 'attachment');
                if (!empty($id)) {
                    $sql = '';
                    $sqlContents = "UPDATE `contents` SET "
                        ."`title`='".addslashes($rs->fields['titulo'])."', "
                        ."`metadata`='".StringUtils::get_tags($rs->fields['titulo'])."' "
                        ." WHERE pk_content=".$id.";  \n";

                    $sqlAttach = "UPDATE `attachments` SET "
                        ."`title`='".addslashes($rs->fields['titulo'])."' "
                        ." WHERE pk_attachment=".$id."; \n ";

                    $datawritten = fwrite($handle, $sqlContents);
                    $datawritten = fwrite($handle, $sqlAttach);
                }
                $rs->MoveNext();
            }

            fclose($handle);
            $rs->Close();

        }

    }

    public function updateContents()
    {

        $sql = "SELECT title, pk_content FROM `contents` WHERE metadata=''";
        $rs  = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            echo  $GLOBALS['application']->conn->ErrorMsg();
            $this->helper->log($GLOBALS['application']->conn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            $values = array();
            while (!$rs->EOF) {
                if (!empty($rs->fields['title'])) {
                    $metadata = $this->helper->getSlug($rs->fields['title']);
                    echo "- $metadata, ".$rs->fields['pk_content']." \n";
                    $values[] = array($metadata, $rs->fields['pk_content']);
                }
                $rs->MoveNext();
            }
            $rs->Close();
            //update
            if (!empty($values)) {

                $sql = 'UPDATE `contents` SET `metadata`=?  WHERE pk_content=?';

                $request = $GLOBALS['application']->conn->Prepare($sql);
                $rss     = $GLOBALS['application']->conn->Execute($request, $values);
                if (!$rss) {
                    echo $GLOBALS['application']->conn->ErrorMsg();
                }
            }
            $rss->Close(); # optional
        }

    }

    //New functions for satelite instaces


    public function createCategories()
    {

        $this->categoriesMatches = array();

        $sql = "SELECT nombre FROM `secciones`";
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log('problem import categories = seccion'.self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;
            $category = new \ContentCategory();

            while (!$rs->EOF) {
                $title = $this->helper->convertToUtf8(strtolower($rs->fields['nombre']));
                $data = array();
                $data['name'] = \StringUtils::normalizeName($title);
                $data['title'] = $title;
                $data['inmenu']=1;
                $data['subcategory'] =0;
                $data['internal_category'] =1;
                $data['logo_path'] ='';
                $data['color']  ='';
                $data['params']  ='';

                if ($category->create($data)) {
                    $this->helper->log("added seccion {$title} \n");
                    $title = \Onm\StringUtils::setSeparator(strtolower($title), '-');
                    $this->categoriesMatches[$title] = $category->pk_content_category;
                } else {
                    $this->helper->log("problem creating {$title} \n");
                }

                $rs->MoveNext();
            }

            $others = array('Galerias', 'Otros');
            foreach ($others as $nombre) {
                $title = $this->helper->convertToUtf8(strtolower($nombre));
                $data = array();
                $data['name'] = \StringUtils::normalizeName($title);
                $data['title'] = ucfirst($title);
                $data['inmenu']=1;
                $data['subcategory'] =0;
                $data['internal_category'] =1;
                $data['logo_path'] ='';
                $data['color']  ='';
                $data['params']  ='';

                if ($category->create($data)) {
                    $this->helper->log("added seccion {$title} \n");
                    $title = \Onm\StringUtils::setSeparator(strtolower($title), '-');
                    $this->categoriesMatches[$title] = $category->pk_content_category;
                } else {
                    $this->helper->log("problem creating {$title} \n");
                }

            }

        }

    }
}

