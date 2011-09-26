<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once('pagelet.class.php');
/**
 * Handles all the operations with Pages.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Page {

	var $id_category = null;
	var $id_content = null;
	var $zones = null;
	var $id_template = null;
	var $source_template = null;


    function Page($id_category, $id_content = null) {
        $this->id_category = $id_category;
        $this->id_content = $id_content;
    }

    function __construct($id_category, $id_content = null){
        $this->Page($id_category, $id_content);
    }

	function show(){
		if(is_null($this->id_content) && !is_null($this->id_category)){
			$this->show_front();
		}else{

			$this->show_detail();
		}
	}

    private function show_front() {
    	//Se obtienen los contenidos de todas las zonas ordenados
        $sql = 'select t1.fk_template_front as id_template,
       t1.name as name,
       t1.description as description,
       t2.pk_fk_zone as id_zone,
       t2.pk_fk_content as id_content,
       t2.position as position,
       t2.fk_template_content as id_template_content,
       t3.fk_content_type as id_content_type,
       t4.source as source,
       t5.source as source_content,
       t6.name as name_zone,
       t7.name as name_content_type,
       t6.mode as mode_zone,
       t8.fk_template_default,
       t9.source as source_default
       from content_categories as t1
              inner join category_zones_contents as t2 on t1.fk_template_front = t2.pk_fk_template
                         and t1.pk_content_category = t2.pk_fk_content_category
              inner join contents as t3 on t2.pk_fk_content = t3.pk_content
              inner join templates as t4 on t2.pk_fk_template = t4.pk_template
              left join templates as t5 on t2.fk_template_content = t5.pk_template
              inner join zones as t6 on t2.pk_fk_zone = t6.pk_zone and t2.pk_fk_template = t6.pk_fk_template
              inner join content_types as t7 on t3.fk_content_type = t7.pk_content_type
              left join zones_content_types as t8 on
                   t1.fk_template_front = t8.pk_fk_template and
                   t2.pk_fk_zone = t8.pk_fk_zone and
                   t3.fk_content_type = t8.pk_fk_content_type
              left join templates as t9 on  t8.fk_template_default = t9.pk_template
		where
      			t1.pk_content_category = ' .intval($this->id_category) .'
		order by t2.pk_fk_zone, t2.position';
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        $this->zones = array();
        //Esta array se utiliza para acumular los contenidos que se pasan como un conjunto a una platilla
        //de una zona con modo m(multiple)
        $array_contents = array();
        $template_zone_m = null;
        $name_zone_m = null;
        while(!$rs->EOF) {
        	if(is_null($this->id_template)){
        		$this->id_template = $rs->fields['id_template'];
        		$this->source_template = $s->fields['source'];
        	}
        	$id_content = $rs->fields['id_content'];
        	$name_content_type = $rs->fields['name_content_type'];
        	$template_source_content = $rs->fields['source_content'];
        	if(is_null($template_source_content)){
        		$template_source_content = $rs->fields['source_default'];
        	}
        	$template_source = $rs->fields['source'];
        	$name_zone = $rs->fields['name_zone'];
        	$mode_zone = $rs->fields['mode_zone'];
        	if(!is_null($id_content) && !is_null($name_content_type) && !is_null($template_source_content) && !is_null($name_zone) && !is_null($template_source)){
        		if($mode_zone != 'm'){
        			if((count($array_contents) > 0)&&(!is_null($template_zone_m))){
        				$tpl_temp = new Template();
        				$tpl_temp->assign('content', $array_contents);
        				$tpl_temp->assign('id_content', null);
        				$tpl_temp->assign('id_category', $this->id_category);
        				$this->zones[$name_zone_m] = $tpl_temp->fetch($template_zone_m);
        				$array_contents = array();
        				$name_zone_m = null;
        				$template_zone_m = null;
        			}
        		$content = new $name_content_type( $id_content );
        		$tpl = new Template();
        		$tpl->assign('content', $content);
        		$tpl->assign('id_content', $id_content);
        		$tpl->assign('id_category', $this->id_category);
        		$this->zones[$name_zone] = $this->zones[$name_zone] . $tpl->fetch($template_source_content);
        		}else{
        			if((count($array_contents) > 0)&&(!is_null($template_zone_m)) && ($name_zone_m != $name_zone)){
        				$tpl_temp = new Template();
        				$tpl_temp->assign('content', $array_contents);
        				$tpl_temp->assign('id_content', null);
        				$tpl_temp->assign('id_category', $this->id_category);
        				$this->zones[$name_zone_m] = $tpl_temp->fetch($template_zone_m);
        				$array_contents = array();
        				$name_zone_m = null;
        				$template_zone_m = null;
        			}
					$array_contents[] = new $name_content_type( $id_content );
					$name_zone_m = $name_zone;
					$template_zone_m = $template_source_content;
        		}
        	}else{
        		//print'ummm:';
        	}
        	$rs->MoveNext();
        }

		if((count($array_contents) > 0)&&(!is_null($template_zone_m))){
			$tpl_temp = new Template();
			$tpl_temp->assign('content', $array_contents);
			$tpl_temp->assign('id_content', null);
			$tpl_temp->assign('id_category', $this->id_category);
			$this->zones[$name_zone_m] = $tpl_temp->fetch($template_zone_m);
			$array_contents = array();
			$name_zone_m = null;
			$template_zone_m = null;
       	}
        //Para cada zona, se le asigna el contenido
        $tpl_front = new Template();
        foreach ($this->zones as $key => $value){
        	$tpl_front->assign($key,$value);
        }

        //Esto es temporal hasta que se decida el tipo de contenido
        $tpl_front->assign('accordion', array(array('image' => 'media/images/opinion01.jpg',
                                      'text'=> '"Esta es mi opinión, si no le gusta tengo otras." - Groucho Marx'),
                                array('image' => 'media/images/opinion02.jpg',
                                      'text'=> '"Esta es mi opinión, si no le gusta tengo otras." - Groucho Marx'),
                                array('image' => 'media/images/opinion03.jpg',
                                      'text'=> '"Esta es mi opinión, si no le gusta tengo otras." - Groucho Marx') ));
        //Se genera el contenido
        $tpl_front->display($template_source);
    }



    private function show_detail() {
    	//Se obtienen los contenidos de todas las zonas ordenados
        $sql = 'select t1.fk_template_detail as id_template,
       t1.name as name,
       t1.description as description,
       t2.pk_fk_zone as id_zone,
       t2.pk_fk_content as id_content,
       t2.position as position,
       t2.fk_template_content as id_template_content,
       t3.fk_content_type as id_content_type,
       t4.source as source,
       t5.source as source_content,
       t6.name as name_zone,
       t7.name as name_content_type,
       t6.mode as mode_zone,
       t8.fk_template_default,
       t9.source as source_default
       from content_categories as t1
              inner join category_zones_contents as t2 on t1.fk_template_detail = t2.pk_fk_template
                         and t1.pk_content_category = t2.pk_fk_content_category
              inner join contents as t3 on t2.pk_fk_content = t3.pk_content
              inner join templates as t4 on t2.pk_fk_template = t4.pk_template
              left join templates as t5 on t2.fk_template_content = t5.pk_template
              inner join zones as t6 on t2.pk_fk_zone = t6.pk_zone and t2.pk_fk_template = t6.pk_fk_template
              inner join content_types as t7 on t3.fk_content_type = t7.pk_content_type
              left join zones_content_types as t8 on
                   t1.fk_template_front = t8.pk_fk_template and
                   t2.pk_fk_zone = t8.pk_fk_zone and
                   t3.fk_content_type = t8.pk_fk_content_type
              left join templates as t9 on  t8.fk_template_default = t9.pk_template
		where
      			t1.pk_content_category = ' .intval($this->id_category) .'
		order by t2.pk_fk_zone, t2.position';
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        $this->zones = array();
        //Esta array se utiliza para acumular los contenidos que se pasan como un conjunto a una platilla
        //de una zona con modo m(multiple)
        $array_contents = array();
        $template_zone_m = null;
        $name_zone_m = null;
        while(!$rs->EOF) {
        	if(is_null($this->id_template)){
        		$this->id_template = $rs->fields['id_template'];
        		$this->source_template = $s->fields['source'];
        	}
        	$id_content = $rs->fields['id_content'];
        	$name_content_type = $rs->fields['name_content_type'];
        	$template_source_content = $rs->fields['source_content'];
        	if(is_null($template_source_content)){
        		$template_source_content = $rs->fields['source_default'];
        	}
        	$template_source = $rs->fields['source'];
        	$name_zone = $rs->fields['name_zone'];
        	$mode_zone = $rs->fields['mode_zone'];
        	if(!is_null($id_content) && !is_null($name_content_type) && !is_null($template_source_content) && !is_null($name_zone) && !is_null($template_source)){
        		if($mode_zone != 'm'){
        			if((count($array_contents) > 0)&&(!is_null($template_zone_m))){
        				$tpl_temp = new Template();
        				$tpl_temp->assign('content', $array_contents);
        				$tpl_temp->assign('id_content', null);
        				$tpl_temp->assign('id_category', $this->id_category);
        				$this->zones[$name_zone_m] = $tpl_temp->fetch($template_zone_m);
        				$array_contents = array();
        				$name_zone_m = null;
        				$template_zone_m = null;
        			}
        		$content = new $name_content_type( $id_content );
        		$tpl = new Template();
        		$tpl->assign('content', $content);
        		$tpl->assign('id_content', $id_content);
        		$tpl->assign('id_category', $this->id_category);
        		$this->zones[$name_zone] = $this->zones[$name_zone] . $tpl->fetch($template_source_content);
        		}else{
        			if((count($array_contents) > 0)&&(!is_null($template_zone_m)) && ($name_zone_m != $name_zone)){
        				$tpl_temp = new Template();
        				$tpl_temp->assign('content', $array_contents);
        				$tpl_temp->assign('id_content', null);
        				$tpl_temp->assign('id_category', $this->id_category);
        				$this->zones[$name_zone_m] = $tpl_temp->fetch($template_zone_m);
        				$array_contents = array();
        				$name_zone_m = null;
        				$template_zone_m = null;
        			}
					$array_contents[] = new $name_content_type( $id_content );
					$name_zone_m = $name_zone;
					$template_zone_m = $template_source_content;
        		}
        	}else{
        		//print'ummm:';
        	}
        	$rs->MoveNext();
        }

		if((count($array_contents) > 0)&&(!is_null($template_zone_m))){
			$tpl_temp = new Template();
			$tpl_temp->assign('content', $array_contents);
			$tpl_temp->assign('id_content', null);
			$tpl_temp->assign('id_category', $this->id_category);
			$this->zones[$name_zone_m] = $tpl_temp->fetch($template_zone_m);
			$array_contents = array();
			$name_zone_m = null;
			$template_zone_m = null;
       	}
        //Para cada zona, se le asigna el contenido
        $tpl_detail = new Template();
        foreach ($this->zones as $key => $value){
        	$tpl_detail->assign($key,$value);
        }

        //Se obtiene el contenido principal
        $sql = 'select t1.fk_content_type as id_content_type,
       		t2.name as name_content_type,
       		t2.fk_template_default as id_template_default,
       		t3.source as source
		from contents as t1
     		inner join content_types as t2 on t1.fk_content_type = t2.pk_content_type
     		inner join templates as t3 on t2.fk_template_default = t3.pk_template
		where t1.pk_content = ' . $this->id_content ;

		$rs = $GLOBALS['application']->conn->Execute( $sql );
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        $name_content_type = $rs->fields['name_content_type'];
        $template_content_source = $rs->fields['source'];
        $content = new $name_content_type( $this->id_content );
        $tpl_temp = new Template();
        				$tpl_temp->assign('content', $content);
        				$tpl_temp->assign('id_content', $this->id_content);
        				$tpl_temp->assign('id_category', $this->id_category);
        				$contenido  = $tpl_temp->fetch($template_content_source);
        $tpl_detail->assign('content', $contenido);
        //Se genera el contenido
        $tpl_detail->display($template_source);


    }



}
?>
