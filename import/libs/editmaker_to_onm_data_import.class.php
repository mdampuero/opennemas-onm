<?php
class EditmakerToOnmDataImport {
    
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
            
    public $matchAuthor = array();
    
    public function __construct ($config = array())
    {
        
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
    
    public function importArticles()
    {
        
        $_sql_where = ' WHERE Seccion IN ('.implode(', ', array_keys($this->categoriesMatches)).") ";
        $_limit = ''; /*'LIMIT 0,1';*/
        
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
            
            while (!$rs->EOF) {
                
                $originalArticleID = $rs->fields['id'];
                
                $values = array(
                        'title' => $rs->fields['Titulo'],
                        'category' => $this->matchCategory($rs->fields['Seccion']),
                        'with_comment' => 1,
                        'content_status' => 1,
                        'frontpage' => 0,
                        'in_home' => 0,
                        'title_int' => $rs->fields['Titulo'],
                        'metadata' => String_Utils::get_tags($rs->fields['Titulo']),
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
                    
                    $ih = new ImportHelper();
                    $ih->logElementInsert($originalArticleID, $newArticleID, 'author');
                    
                }
                //ImportHelper::messageStatus("Importing Articles: $current/$totalRows");
                sleep(0.12);
                $current++;
                
                
                //$article->set_numViews();
                //$article['views'] = $rs->fields['visitas'];
                //
                //$sql =  'UPDATE `contents` SET `views`='.$article['views'].' '
                //    .'WHERE `available`=1 AND `pk_content`='.$lastStoredArticleID;
                //    
                //    $article['created'] = $rs->fields['fecha'];
                //
                //
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
            
            while (!$rs->EOF) {
                
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
                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }
    }
    

    public function importComments()
    {
        
    }
}
