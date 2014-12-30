<?php
class EditmakerToOnmDataImport {

    public $idsMatches = array();
    public $categoryIDForContentID = array();

    public $categoriesMatches =
            array(
                48 => 13,   // CULTURA Y OCIO
                45 => 14,   // ECONOMIA
                55 => 298,  // ENTREVISTAS
                44 => 11,   // ESPANHA
                65 => 297,  // MEDIO AMBIENTE
                50 => 12,   // MUNDO
                51 => 296,  // SOCIEDAD
            );
    public $categoriesOpinion =
            array(
                39 => 4,     // OPINION
                59 => 4,     // OPINION
                62 => 4,     // OPINION
                63 => 4,     // OPINION
                64 => 4,     // OPINION
                66 => 4,     // OPINION
            );

    public $matchAuthors =
            array(
                74 => 384, //Alberto priego
                89 => 385, //alberto olmos
                //102 => , //alfonso cuadros // no articles
                100 => 368, //amalia albarez
                //113 => , //ana noguera // no articles
                123 => 256, //andres rojo
                70 => 370,
                75  => 258,
                93 => 371,
                62 => 260,
                32 => 372,
                //106 => , //antonio donhate
                67 => 262,
                72 => 263,
                91 => 386, // Antonio santo
                41 => 265,
                //111 => , // Carlos Barra //no articles
                61 => 266,
                26 => 267,
                65 => 269,
                131 => 270,
                121 => 271,
                108 => 273,
                90 => 272,
                59 => 274,
                134 => 276,
                //124 => , // Daniel Jimenez // not created
                137 => 279,
                68 => 280,
                58 => 281,
                54 => 390, // El Señor de Fouquet
                128 => 377,
                39 => 282,
                87 => 283,
                //86 => , // Enrique Vázquez //no articles
                139 => 284,
                78 => 285,
                125 => 286,
                142 => 287,
                36 => 288,
                104 => 289,
                34 => 291,
                45 => 292,
                133 => 294,
                25 => 295,
                48 => 296,
                119 => 297,
                56 => 391, // Ignacio Fernández Toxo
                35 => 273,
                95 => 305,
                28 => 306,
                126 => 307,
                96 => 308,
                144 => 310,
                40 => 312,
                118 => 313,
                //127 => , //
                81 => 392, // José Félix Tezanos
                82 => 314,
                141 => 374,
                138 => 317,
                109 => 318,
                47 => 319,
                //110 => , // Jose María Asencio Mellado //no articles
                52 => 393, // José María Martínez López
                42 => 320,
                29 => 321,
                115 => 322,
                140 => 323,
                69 => 324,
                116 => 325,
                104 => 327,
                55 => 248,
                27 => 328,
                //77 => , 88 => , pueden ser el mismo
                80 => 329,
                99 => 330,
                73 => 331,
                //117 => , // Juanelo  //no articles
                122 => 332,
                43 => 394, // Laureano Lázaro Araujo
                120 => 333,
                //130 => , //
                98 => 395, // Luis Angel Aguilar Montero
                129 => 396, // Macarena Elvira Rubio
                85 => 335,
                60 => 336,
                30 => 337,
                143 => 340,
                92 => 341,
                50 => 397, // Mercedes Arancibia
                //79 => , //
                97 => 343,
                84 => 344,
                //107 => , //
                //71 => , //
                38 => 346,
                136 => 347,
                63 => 348,
                135 => 349,
                46 => 350,
                101 => 351,
                53 => 352,
                57 => 353,
                83 => 354,
                64 => 355,
                51 => 398, // Ramón Espinar
                76 => 356,
                33 => 357,
                49 => 405, // Rodolfo Serrano
                103 => 358,
                94 => 359,
                114 => 360,
                44 => 400, // Susana Iván
                66 => 361,
                112 => 363,
                //132 => , //
                37 => 364,
            );

    public function __construct ($config = array())
    {
        $this->dbConfig = $config;

        if (isset($config['bd_host'])
            && isset($config['bd_database'])
            && isset($config['bd_user'])
            && isset($config['bd_pass'])
            && isset($config['bd_type']))
        {

            $this->orig->conn= ADONewConnection($config['bd_type']);
            $this->orig->conn->PConnect(
                                    $config['bd_host'], $config['bd_user'],
                                    $config['bd_pass'], $config['bd_database']
                                  );

        } else {

            echo    "ERROR: You must provide the connection configuration to the\n"
                    ."\tEditMaker database";
            die();
        }

    }

    public function matchCategory($category)
    {

        return $this->categoriesMatches[$category];

    }

    public function matchAuthor($author)
    {

        return ((isset($this->matchAuthors[$author])
                        ? $this->matchAuthors[$author]
                        : false));

    }

    public function matchID($originalID)
    {
        if(isset($this->idsMatches[$originalID])) {

            return $this->idsMatches[$originalID];

        } else {

            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=?';

            $values = array($originalID);
            $getRowFromPkContentOld = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($getRowFromPkContentOld,
                                                          $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                $this->idsMatches[$originalID] = $rss->fields['pk_content'];
                return ($rss->fields['pk_content']);
            }

        }
    }

    public function getCategoryIDFromContent($pkContent) {

        if(isset($this->categoryIDForContentID[$pkContent])) {

            return $this->categoryIDForContentID[$pkContent];

        } else {

            $sql = 'SELECT pk_fk_content_category FROM `contents_categories` WHERE `pk_fk_content`=?';

            $values = array($pkContent);
            $getCategoryIDfromPkContent = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($getCategoryIDfromPkContent,
                                                          $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                $this->categoryIDForContentID[$pkContent] = $rss->fields['pk_fk_content_category'];
                return ($rss->fields['pk_fk_content_category']);
            }

        }
    }

    public function importCategories()
    {
        $ih = new ImportHelper();

        foreach( $this->categoriesMatches as $originalID => $newID ) {

            if ($ih->elementIsImported($originalID, 'category') ) {
                echo "Category with id {$originalID} already imported\n";
            } else {
                echo "Importing category with id {$originalID} - ";
                $ih->logElementInsert($originalID, $newID, 'category');
                echo "new id {$newID} [DONE]\n";
            }

        }

        foreach( $this->categoriesOpinion as $originalID => $originalID) {

            if ($ih->elementIsImported($originalID, 'category') ) {
                echo "Category with id {$originalID} already imported\n";
            } else {
                echo "Importing category with id {$originalID} - ";
                $ih->logElementInsert($originalID, $newID, 'category');
                echo "new id {$newID} [DONE]\n";
            }

        }

    }

    public function importArticles()
    {

        $_sql_where = ' WHERE Seccion IN ('.implode(', ', array_keys($this->categoriesMatches)).") ";
        $_limit = ''; /*'LIMIT 0,1'*/;

        $sql = 'SELECT * FROM noticias'.$_sql_where.' '.$_limit;

        // Fetch the list of Articles available in EditMaker
        $request = $this->orig->conn->Prepare($sql);
        $rs = $this->orig->conn->Execute($request);


        if (!$rs) {
            ImportHelper::messageStatus($this->orig->conn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $articles = $rs->fields;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                if ($ih->elementIsImported($rs->fields['id'], 'article') ) {
                    echo "[{$current}/{$totalRows}] Article with id {$rs->fields['id']} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing article with id {$rs->fields['id']} - ";

                    $originalArticleID = $rs->fields['id'];

                    $values = array(
                            'title' => $rs->fields['Titulo'],
                            'category' => $this->matchCategory($rs->fields['Seccion']),
                            'with_comment' => 1,
                            'content_status' => 1,
                            'frontpage' => 0,
                            'in_home' => 0,
                            'title_int' => $rs->fields['Titulo'],
                            'metadata' => StringUtils::getTags($rs->fields['Titulo']),
                            'subtitle' => $rs->fields['Antetit'],
                            'agency' => 'nuevatribuna.es',
                            'summary' => $rs->fields['Entrad'],
                            'body' => $rs->fields['Texto'],
                            'posic' => 0,
                            'id' => 0,
                            'fk_publisher' => 125
                        );

                    $article = new Article();
                    $newArticleID = $article->create($values);
                    if(is_string($newArticleID)) {

                        $ih->logElementInsert($originalArticleID, $newArticleID, 'article');
                        $ih->updateViews($newArticleID, $rs->fields['visitas']);
                        $ih->updateCreateDate($newArticleID, $rs->fields['fecha']);

                    }
                    echo "new id {$newArticleID} [DONE]\n";
                    //ImportHelper::messageStatus("Importing Articles: $current/$totalRows");
                    //sleep(0.12);
                }

                $current++;

                $rs->MoveNext();
            }

            $rs->Close(); # optional

        }

    }

    public function importOpinions()
    {
        $sql = "SELECT * FROM translation_ids WHERE type='author'";

        // Fetch the list of Articles available in EditMaker and some statistics
        $request = $GLOBALS['application']->conn->Prepare($sql);
        $rs = $GLOBALS['application']->conn->Execute($request);

        if (!$rs) {
            ImportHelper::messageStatus($GLOBALS['application']->conn->ErrorMsg());
        } else {

            while (!$rs->EOF) {

                $this->matchAuthors[$rs->fields['pk_content_old']] = $rs->fields['pk_content'];
                $rs->MoveNext();

            }


            $rs->Close(); # optional

            // Getting the
            $_filter_by_section = ' Seccion IN ('.implode(', ', array_keys($this->categoriesOpinion)).") ";
            $sql = 'SELECT noticias.id, noticias.Antetit, noticias.Titulo,
                    noticias.Subtit, noticias.Entrad, noticias.Texto,
                    noticias.seccion, noticias.visitas, noticias.fecha,
                    firmantes.id as firmante_id, firmantes.nombre as firmante_nombre
                    FROM `noticias`, `noti_firma`, firmantes
                    WHERE '
                    .$_filter_by_section
                    .' AND noti_firma.id_firma = firmantes.id
                       AND noti_firma.id_noti = noticias.id'
                    /*.' LIMIT 0,10'*/;
            // Fetch the list of Opinions available for one author in EditMaker
            $request = $this->orig->conn->Prepare($sql);
            $rs = $this->orig->conn->Execute($request);


            if(!$rs) {
                ImportHelper::messageStatus($this->orig->conn->ErrorMsg());
            } else {

                $totalRows = $rs->_numOfRows;
                $opinions = $rs->fields;
                $current = 1;
                $ih = new ImportHelper();

                while (!$rs->EOF) {

                    if ($ih->elementIsImported($rs->fields['id'], 'opinion') ) {
                        echo "[{$current}/{$totalRows}] Opinion with id {$rs->fields['id']} already imported\n";
                    } else {
                        echo "[{$current}/{$totalRows}] Importing opinion with id {$rs->fields['id']} - ";

                        $originalOpinionID = $rs->fields['id'];

                        if ($this->matchAuthor($rs->fields['firmante_id'])) {

                            //var_dump($rs->fields['firmante_id'], $this->matchAuthor($rs->fields['firmante_id']));
                            //die();
                            //

                            $values =
                                array(
                                    'title' => $rs->fields['Titulo'],
                                    'category' => 'opinion',
                                    'body' => $rs->fields['Texto'],
                                    'type_opinion' => '0',
                                    'metadata' => StringUtils::getTags($rs->fields['Titulo'].' '.$rs->fields['firmante_nombre'] ),
                                    'views' => $rs->fields['visitas'],
                                    'created' => $rs->fields['fecha'],
                                    'fk_author' => (int)$this->matchAuthor($rs->fields['firmante_id']),
                                    'publisher' => 112,
                                    'available' => 1,
                                    'with_comment' => 1,
                                    'in_home' => 0,
                                );



                            $opinion = new Opinion();
                            $newOpinionID = $opinion->create($values);

                            if(is_string($newOpinionID)) {

                                $ih->logElementInsert($originalOpinionID, $newOpinionID, 'opinion');
                                $ih->updateViews($newOpinionID,$rs->fields['visitas']);
                                $ih->updateCreateDate($newOpinionID, $rs->fields['fecha']);

                            }
                            echo "new id {$newOpinionID} [DONE]\n";

                        } else {
                            echo "This has not an correlated element\n";
                        }

                    }

                    $current++;
                    $rs->MoveNext();
                }
                $rs->Close(); # optional
            }
        }

    }

    public function importArrayAuthors()
    {
        $ih = new ImportHelper();

        $totalRows = count($this->matchAuthors);
        $current = 1;


        foreach ($this->matchAuthors as $original_id => $new_id) {

            if ($ih->elementIsImported($original_id, 'author') ) {
                echo "[{$current}/{$totalRows}] Author with id {$original_id} already imported\n";
            } else {

                echo "[{$current}/{$totalRows}] Importing author with id {$original_id} - ";
                $ih->logElementInsert($original_id, $new_id, 'author');
                echo "new id {$new_id} [DONE]\n";

            }
            $current++;
        }

    }

    public function importAuthorsWeirdMode()
    {
        $sql = "SELECT  original_authors.id as original_id,
                        original_authors.nombre as name,
                        new_authors.pk_author as new_id
                FROM    `".$this->dbConfig['bd_database']."`.`firmantes` as `original_authors`,
                        `".BD_INST."`.`authors` as `new_authors`
                WHERE `original_authors`.`nombre` =  `new_authors`.`name`
                ORDER BY original_id";

        // Fetch the list of Opinions available for one author in EditMaker
        $request = $this->orig->conn->Prepare($sql);
        $rs = $this->orig->conn->Execute($request);


        if(!$rs) {
            ImportHelper::messageStatus($this->orig->conn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $opinions = $rs->fields;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                if ($ih->elementIsImported($rs->fields['original_id'], 'author') ) {
                    echo "[{$current}/{$totalRows}] Author with id {$rs->fields['original_id']} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing author with id {$rs->fields['original_id']} - ";

                    if(is_string($rs->fields['new_id'])) {

                        $ih->logElementInsert($rs->fields['original_id'], $rs->fields['new_id'], 'author');

                    }
                    echo "new id {$rs->fields['new_id']} [DONE]\n";

                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }

    public function importAuthors()
    {


        $sql = 'SELECT * FROM firmantes';

        // Fetch the list of Articles available in EditMaker and some statistics
        $request = $this->orig->conn->Prepare($sql);
        $rs = $this->orig->conn->Execute($request);

        if (!$rs) {
            ImportHelper::messageStatus($this->orig->conn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $authors = $rs->fields;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                if ($ih->elementIsImported($rs->fields['id'], 'author') ) {
                    echo "[{$current}/{$totalRows}] Author with id {$rs->fields['id']} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing author with id {$rs->fields['id']} - ";

                    $originalAuthorID = $rs->fields['id'];

                    $values = array(
                            'name' => iconv("ISO-8859-1", "UTF-8", $rs->fields['nombre']),
                        );

                    $author = new Author();
                    $newAuthorID = $author->create($values);

                    if(is_string($newAuthorID)) {

                        $ih = new ImportHelper();
                        $ih->logElementInsert($originalAuthorID, $newAuthorID, 'author');
                        $this->matchAuthor[$originalAuthorID] = $newAuthorID;

                    }
                    echo "new id {$newAuthorID} [DONE]\n";
                }
                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }
    }


    public function importComments()
    {
        $sql = 'SELECT id_noti as fk_content,opiniones.*
                FROM  `opiniones` , noti_foro
                WHERE opiniones.id_foro = noti_foro.id_foro
                GROUP BY `id`';

        // Fetch the list of Comments available in EditMaker and some statistics
        $request = $this->orig->conn->Prepare($sql);
        $rs = $this->orig->conn->Execute($request);

        if (!$rs) {
            ImportHelper::messageStatus($this->orig->conn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $authors = $rs->fields;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                if ($ih->elementIsImported($rs->fields['id'], 'comment') ) {
                    echo "[{$current}/{$totalRows}] Comment with id {$rs->fields['id']} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing comment with id {$rs->fields['id']} over content with id {$this->matchID($rs->fields['fk_content'])} - ";

                    $originalCommentID = $rs->fields['id'];

                    $data = array(
                                    'title' => '',
                                    'available' => 1,
                                    'content_status' => 1,
                                    'category' => $this->getCategoryIDFromContent($this->matchID($rs->fields['fk_content'])),
                                    'body'   => iconv("ISO-8859-1", "UTF-8", $rs->fields['comentario']),
                                    'author' => iconv("ISO-8859-1", "UTF-8", $rs->fields['nombre']),
                                    'email'  => iconv("ISO-8859-1", "UTF-8", $rs->fields['email']),
                                  );

                    $comment = new Comment();
                    $newCommentID = $comment->create(
                                                        array(
                                                            'id' => $this->matchID($rs->fields['fk_content']),
                                                            'data' => $data,
                                                            'ip' => $rs->fields['ip']
                                                        )
                                                    );

                    $ih->updateCreateDate($newCommentID, $rs->fields['fecha']);

                    if(is_string($newCommentID)) {

                        $ih = new ImportHelper();
                        $ih->logElementInsert($originalCommentID, $newCommentID, 'comment');

                    }
                    echo "new id {$newCommentID} [DONE]\n";

                }
                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }
    }

}
