<?php
class Related_content {
    var $pk_content1  = NULL;
    var $pk_content2  = NULL;
    var $relationship  = NULL;
	var $text  = NULL;

    /**
      * Constructor PHP5
    */
    function __construct($id=NULL){
        if(!is_null($id)) {
            $this->read($id);
        }

        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
    }

    function create($id,$id2,$position=1,$posint=1,$verport=NULL,$verint=NULL,$relation=NULL) {
        $sql = "INSERT INTO related_contents (`pk_content1`, `pk_content2`, `position`,  `posinterior`, `verportada`, `verinterior`,`relationship`) " .
            " VALUES (?,?,?,?,?,?,?)";

 		$values = array($id, $id2,$position,$posint,$verport,$verint,$relation); //positions=1

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

        return(true);
    }

    function load($properties) {
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        }
    }

    function read($id) {
        $sql = 'SELECT * FROM related_contents WHERE pk_content1 = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_content1 = $rs->fields['pk_content1'];
        $this->pk_content2 = $rs->fields['pk_content2'];
        $this->position = $rs->fields['position'];
        $this->posinterior = $rs->fields['posinterior'];
        $this->verportada = $rs->fields['verportada'];
        $this->verinterior = $rs->fields['verinterior'];

    }

    function update($data) {
        $sql = "UPDATE related_contents SET `pk_content2`=?, `relationship`=?, `text`=?, `position`=?" .
        		"WHERE pk_content1=".($data['id']);

        $values = array($data['pk_content2'], $data['relationship'], $data['text'], $data['position']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
	}

	function delete($id) {
            $sql = 'DELETE FROM related_contents WHERE pk_content1='.($id);

            if($GLOBALS['application']->conn->Execute($sql)===false) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

                return;
            }
	}

	function delete_all($id) {
            $sql = 'DELETE FROM related_contents WHERE pk_content1='.($id).' OR pk_content2='.($id);

            if($GLOBALS['application']->conn->Execute($sql)===false) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

                return;
            }
	}


    /**
     * Get contents related to $id_content for frontpage
     *
     * @param int $id_content Content ID
     * @return array Array of related content IDs
     */
    function get_relations($id_content){
        $related = array();

        if($id_content) {
	    	$sql = 'select pk_content2 from related_contents where verportada="1" AND pk_content1 = ' .($id_content).' ORDER BY position ASC';

	        $rs  = $GLOBALS['application']->conn->Execute($sql);
            if($rs === false) {
                return( array() );
            } else {
                while(!$rs->EOF) {
                    $related[] = $rs->fields['pk_content2'];
                    $rs->MoveNext();
                }
            }

        }
        $related=array_unique($related);
        return $related;
    }

    /**
     * Get contents related to $id_content for inner article
     *
     * @param int $id_content Content ID
     * @return array Array of related content IDs
     */
    function get_relations_int($id_content){
        $related = array();
        if($id_content) {
            $sql = 'SELECT DISTINCT pk_content2 FROM related_contents WHERE verinterior="1" AND pk_content1=? ' .
                   'ORDER BY posinterior ASC';
            $rs = $GLOBALS['application']->conn->Execute($sql, array($id_content));

            if($rs === false) {
                return( array() );
            } else {
                while(!$rs->EOF) {
                    $related[] = $rs->fields['pk_content2'];
                    $rs->MoveNext();
                }
            }
        }

        $related = array_unique($related);
        return $related;
    }

     function get_relations_vic($id_content){
        $related = array();
         if($id_content){
		    	$sql = 'select pk_content1 from related_contents where  pk_content2 = ' .($id_content).' ORDER BY position ASC';
		        $rs = $GLOBALS['application']->conn->Execute($sql);
		        if($rs !== false) {
			        while(!$rs->EOF) {
			        	$related[] = $rs->fields['pk_content1'];
			          	$rs->MoveNext();
			        }
		        }
         }
         $related=array_unique($related);
        return $related;

    }

     function get_content_relations($id_content){
        $related = array();
         if($id_content){
		    	$sql = 'select pk_content1 from related_contents where  pk_content2 = ' .($id_content).' ORDER BY position ASC';
		        $rs = $GLOBALS['application']->conn->Execute($sql);
		        if($rs !== false) {
			        while(!$rs->EOF) {
			        	$related[] = $rs->fields['pk_content1'];
			          	$rs->MoveNext();
			        }
		        }
         }
         $related=array_unique($related);
        return $related;

    }


  //Define relacion entre noticias y entre publi y noticias
	function set_relations($id,$relationes){
	   			  $relations->delete($id);
				  if($relationes){
					   foreach($relationes as $related) {
					        	$relations = new Related_content();
					        	$relations->create($id,$related);
					        }
					        return;
				   }
	}

  //Cambia la posicion en portada
	function set_rel_position($id_content,$position,$id_rel) {
        $sql = 'SELECT position FROM related_contents WHERE pk_content1 ='.($id_content).' AND pk_content2 ='.intval($id_rel);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if(isset($rs->fields['position'])) {
            $sql = "UPDATE related_contents SET  `verportada`=?, `position`=?" .
                " WHERE pk_content1=".($id_content)." AND pk_content2=".($id_rel) ;
            $values = array(1, $position);
        } else {
            $sql = "INSERT INTO related_contents (`pk_content1`, `pk_content2`,`position`,`verportada`) " .
                       " VALUES (?,?,?,?)";

            $values = array($id_content, $id_rel,$position,1);
        }

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
	}


	//Cambia la posicion en el interior
	function set_rel_position_int($id_content,$position,$id_rel){
	    $sql = 'select position from related_contents where pk_content1 = ' .($id_content).' AND pk_content2 = ' .intval($id_rel);
        $rs = $GLOBALS['application']->conn->Execute($sql);


	   if(isset($rs->fields['position'])) {
	           $sql = "UPDATE related_contents SET  `verinterior`=?, `posinterior`=?" .
        		" WHERE pk_content1=".($id_content)." AND pk_content2=".($id_rel) ;
				 $values = array(1,$position);
		}else{
	          $sql = "INSERT INTO related_contents (`pk_content1`, `pk_content2`,`posinterior`,`verinterior`) " .
							" VALUES (?,?,?,?)";

				 $values = array($id_content, $id_rel,$position,1);
	   }

	       if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
	            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
	            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
	            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
	            return;
           }

	}

 function sortArticles($articles) {
//Hay que coger las cats para que sean indices de los arrays
      $cc = new ContentCategoryManager();
	// ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
	//Mirar categorias y se recorre para sacar subcategorias.
	$allcategorys = $cc->find('inmenu=1 AND internal_category=1  AND fk_content_category=0', 'ORDER BY posmenu');
	$i=0;
	foreach( $allcategorys as $prima) {
		$subcat[$i]=$cc->find(' inmenu=1  AND internal_category=1 AND fk_content_category ='.$prima->pk_content_category, 'ORDER BY posmenu');
 		foreach($articles as $article) {
	         if (($article->category == $prima->pk_content_category)  ) {
	                  $output[ $prima->title][] = $article;
	         }
		}
		foreach( $subcat[$i] as $prima) {
	 		foreach($articles as $article) {
		         if (($article->category == $prima->pk_content_category)  ) {
		                  $output[ $prima->title][] = $article;

		         }
			}
		}
	     $i++;
	}
 /*
		for ( $counter = 10; $counter <= 20; $counter++)	{
	        foreach($articles as $article) {
	         if (($article->category == $counter) && ($article->content_status == 1)) {
	                  $output[ $article->category_name][] = $article;
	           }
	         }
		}*/

	        return $output ;
	    }






}
?>
