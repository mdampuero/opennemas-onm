<?php


class Opinion extends Content {

	var $pk_opinion             = NULL;
	var $fk_content_categories  = NULL;
	var $fk_author              = NULL;
	var $body                   = NULL;
        var $author                 = NULL;
	var $fk_author_img          = NULL;
	var $with_comment           = NULL;
	var $fk_author_img_widget   = NULL;

    private static $instance    = NULL;

    /**
     * Array of authors
     */
    private $authors_name       = NULL;

    function __construct($id=null) {

        parent::__construct($id);

        if(is_numeric($id)) {
            $this->read($id);
        }

        $this->content_type = 'Opinion';

    }

    function get_instance() {

        if( is_null(self::$instance) ) {
            self::$instance = new Opinion();
            return self::$instance;

        } else {

            return self::$instance;
        }
    }

	public function __get($name) {

        switch ($name) {
            case 'uri': {

				// Happy hacking!

				if ($this->fk_author == 0) {

					if ((int)$this->type_opinion == 1) {
						$authorName = 'Editorial';
					} elseif ((int)$this->type_opinion == 2) {
						$authorName = 'Director';
					}

				} else {
					$authorName = new Author($this->fk_author);
				}


				$uri =  Uri::generate('opinion',
                            array(
                                'id' => $this->id,
                                'date' => date('Y-m-d', strtotime($this->created)),
                                'slug' => $this->slug,
                                'author' => String_Utils::get_title($authorName),
                            )
                        );
					//'opinion/_AUTHOR_/_DATE_/_SLUG_/_ID_.html'

                 return $uri;

                break;
            }
            case 'slug': {
                return String_Utils::get_title($this->title);
                break;
            }
            default: {
                break;
            }
        }
    }

    function create($data) {

        $data['content_status'] = $data['available'];
        $data['position']   =  1;

        parent::create($data);

        $sql = 'INSERT INTO opinions (`pk_opinion`, `fk_author`, `body`,`fk_author_img`,`with_comment`, type_opinion,fk_author_img_widget) VALUES (?,?,?,?,?,?,?)';

        $values = array($this->id,  $data['fk_author'], $data['body'],$data['fk_author_img'],$data['with_comment'], $data['type_opinion'],$data['fk_author_img_widget']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

           return(false);
        }

       return(true);
    }

    function read($id) {
        parent::read($id);
                  //Saca todos los datos de opinion, tiene que ser con left join por si no tiene autor (p.ej editorial) o foto.
        $sql = 'SELECT opinions.*, authors.name, authors.condition, authors.gender, authors.politics, author_imgs.path_img  FROM opinions '
                .'LEFT JOIN authors ON (opinions.fk_author=authors.pk_author)'
                .'LEFT JOIN author_imgs ON (opinions.fk_author_img=author_imgs.pk_img ) WHERE pk_opinion = '.($id).' ';
        //  $sql = 'SELECT opinions.*, authors.name, authors.condition, authors.gender, authors.politics FROM opinions, authors WHERE pk_opinion = '.($id).' and opinions.fk_author=authors.pk_author  ';

        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

          $this->author =  $rs->fields['name'] ; //Used front opinion.
          $this->load( $rs->fields );

    }

/* Mejoras sql -- lee los datos del autor y la imagen todo en la misma consulta
    function read($id) {
        parent::read($id);

        $sql = 'SELECT * FROM opinions WHERE pk_opinion = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

          $this->load( $rs->fields );
          $opinion_instance = Opinion::get_instance();
      	  $this->author     = $opinion_instance->get_author_name( $rs->fields['fk_author'] );
    }

    /**
     * Get author name
     * @param Integer $fk_author
     *
     * @return String Return name of author
     */
    function get_author_name($fk_author) {
        if( is_null( $this->authors_name ) ) {
            $sql = 'SELECT pk_author, name FROM `authors`';
            $rs = $GLOBALS['application']->conn->Execute( $sql );

            while(!$rs->EOF) {
                $this->authors_name[ $rs->fields['pk_author'] ] = $rs->fields['name'];

                $rs->MoveNext();
            }
        }

        if( !isset($this->authors_name[ $fk_author ]) ) {
            return('');
        }

        return( $this->authors_name[ $fk_author ] );
    }

    function update($data) {
        $data['content_status']= $data['available'];
        parent::update($data);
        $sql = "UPDATE opinions SET `fk_author`=?, `body`=?,`fk_author_img`=?, `with_comment`=?, `type_opinion`=?, `fk_author_img_widget`=?
                    WHERE pk_opinion=".($data['id']);

        $values = array($data['fk_author'],$data['body'],$data['fk_author_img'],$data['with_comment'],$data['type_opinion'],$data['fk_author_img_widget'] );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $GLOBALS['application']->dispatch('onAfterUpdateOpinion', $this);
    }

    function remove($id) { //Elimina definitivamente
        parent::remove($id);

	$sql = 'DELETE FROM opinions WHERE pk_opinion ='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    function find_by_gender($gender) {

	   /* $sql = 'SELECT contents.title, opinions.pk_opinion, opinions.fk_author, opinions.fk_author_img, contents.pk_content, contents.permalink, authors.name, author_imgs.path_img FROM opinions, contents, authors, author_imgs' .
			    ' WHERE in_litter=0 AND content_status=1 AND authors.pk_author= opinions.fk_author AND  pk_opinion = pk_content AND opinions.fk_author_img=author_imgs.pk_img AND gender="'.$gender.'" ORDER BY created DESC';
	  echo $sql;
	 */
        $sql = 'SELECT contents.title, opinions.pk_opinion, opinions.fk_author, opinions.fk_author_img, opinions.fk_author_img_widget, contents.pk_content, contents.permalink, authors.name FROM opinions, contents, authors WHERE in_litter=0 AND content_status=1 and type_opinion=0 AND authors.pk_author= opinions.fk_author AND  pk_opinion = pk_content AND  gender="'.$gender.'" ORDER BY created DESC';

	    $rs = $GLOBALS['application']->conn->Execute($sql);
		$i = 0;

	    while(!$rs->EOF) {
                $items[$i]->pk_opinion       = $rs->fields['pk_opinion'];
                $items[$i]->permalink       = $rs->fields['permalink'];
                $items[$i]->title			= $rs->fields['title'];
	        $items[$i]->name       		= $rs->fields['name'];
	        $items[$i]->fk_author       		= $rs->fields['fk_author'];
	        $items[$i]->fk_author_img       	= $rs->fields['fk_author_img'];
	        $items[$i]->fk_author_img_widget    = $rs->fields['fk_author_img_widget'];

	   //     $items[$i]->path_img       	= $rs->fields['path_img'];
	    	$i++;
		    $rs->MoveNext();
	    }

		return( $items );
	}

	//Poner en una clase aparte
    function get_opinion_algoritm() {
        $sql = 'SELECT opinion_algoritm FROM settings';
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }

       return $rs->fields['opinion_algoritm'];
    }

    function set_opinion_algoritm($value) {
        $sql = "UPDATE settings SET `opinion_algoritm`='".$value."'";
        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    function count_inhome_type($type_opinion=NULL) {
        if(($type_opinion==NULL) && ($this->type_opinion)){
            $type_opinion=$this->type_opinion;
        }

        $sql = "SELECT count(pk_content) FROM contents, opinions WHERE `contents`.`in_litter`=0 AND ".
                "`contents`.`in_home`=1 AND `opinions`.`type_opinion`=".$type_opinion." AND `contents`.`pk_content`= `opinions`.pk_opinion";


        $rs = $GLOBALS['application']->conn->Execute( $sql );
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }

        return $rs->fields['count(pk_content)'];
    }

    function onUpdateClearCacheOpinion() {
        require_once(dirname(__FILE__).'/template_cache_manager.class.php');
        $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

        if(property_exists($this, 'pk_opinion')) {
            $tplManager->delete('opinion|' . $this->pk_opinion);
            $tplManager->fetch(SITE_URL . $this->permalink);
            if(isset($this->in_home) && $this->in_home) {
                $tplManager->delete('home|0');
            }
        }
    }

}
