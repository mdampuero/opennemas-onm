<?php
// Contiene funciones comunes a los contents: advertisement, album, poll, opinion
//FIXME:Meter en una clase????????????????????????


/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');


/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

if (isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'get_tags':
            //Las noticias tienen categorias incluido el nombre como tag.
            $cat="";
            if(isset($_GET['categ']) && !empty($_GET['categ']))  {
                $cc = new ContentCategoryManager();
                $father=$cc->get_father($_GET['categ']);
                if ($father=="") {
                   $cat= strtolower($_GET['categ']);
                }else{
                   $cat= strtolower($_GET['categ'])." ".strtolower($father);
                }
            }

            $tags = $cat." ".$_GET['title']." ".$_GET['tags'];
            $tags = StringUtils::get_tags($tags);
            Application::ajaxOut($tags);

            break;

        case 'get_subcategories':
            $ccm = ContentCategoryManager::get_instance();
            list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

            // <editor-fold defaultstate="collapsed" desc="Container gente-fotoactualidad">
            // Parse template.conf to assign
            $tplFrontend = new Template(TEMPLATE_USER);
            $section = $ccm->get_name($_REQUEST['category']);
            $section = (empty($section))? 'home': $section;

            $container_noticias_gente = $tplFrontend->readKeyConfig('template.conf', 'container_noticias_gente', $section);

            if($container_noticias_gente == '1') {
                $tpl->assign('bloqueGente', 'GENTE / FOTO ACTUALIDAD');
            } else {
                $tpl->assign('bloqueGente', 'FOTO ACTUALIDAD / GENTE');
            }
            // </editor-fold>

            $tpl->assign('subcat', $subcat);
            $tpl->assign('allcategorys', $parentCategories);
            $tpl->assign('datos_cat', $datos_cat);
            $allcategorys = $parentCategories;

            $cm = new ContentManager();
            if (($_GET['category']) ||($_GET['category'] != 'home') || ($_GET['category'] != 'todos')){
                    $category= $_GET['category'];
            }else{ $category=10; }

            $tpl->assign('category', $_GET['category']);
            $tpl->assign('home', $_GET['home']);
            $html_out = $tpl->fetch('menu_subcategorys.tpl');
            Application::ajaxOut($html_out);

            break;
    }
}

//////////////////////////////////////////////////////////////////////////
////////////////////////////////// Contenidos Relacionados ///////////////
//////////////////////////////////////////////////////////////////////////

//Print del resultado de noticias relacionadas para ajax
	function print_search_related($id, $search){
		$total=count($search);
			$div='search-noticias';
			$relationes=array();
			$intrelationes=array();
			$rel= new RelatedContent();
  		 	$relationes = $rel->get_relations( $id );//de portada
	        $intrelationes = $rel->get_relations_int( $id );//de interor

			$html_out = "  ";
			if($search){
				$html_out .= "<br/><table class='adminlist '>";

				$html_out .=
                    '	<thead>
						<th>'._('Title').'</th>
						<th>'._('Category').'</th>
						<th style="text-align:center">'._('Published').'</th>
						<th style="text-align:center">'._('Show in frontpage').'</th>
						<th style="text-align:center">'._('Show in inner article').'</th>
					</thead>';

				$i = 0;
				foreach ($search as $art) {
					if ($art['pk_content'] != $id){
						$title=$art['title'];
						$title = preg_replace('/\\\/', '', $art['title']); //Eliminar comillas
					    if($art['available'] == 1) {
					    	$img_status='<img src="themes/default/images/publish_g.png" border="0" alt="Publicado" />';
					    } else {
							$img_status='<img src="themes/default/images/publish_r.png" border="0" alt="Publicado" />';
					    }
						if($i%2){	$html_out .= "<tr bgcolor='#ffffff'>";
						}else{ $html_out .= "<tr bgcolor='#eeeeee'>"; }
						$i++;
			    		$html_out .="<td>".$title."<br> ". /*$art['metadata']. */"</td><td>".strtoupper($art['catName']). /*" Peso".round($art['rel'],4). */ "</td>
								  <td style=\"text-align:center\">".$img_status."</td>
								  <td style=\"text-align:center\">
					 			   <input type='checkbox' onclick=\"javascript:probarArtic(this,'".$div."','thelist2');\"
					 			     class='portada' value='".$title."' id='".$art['pk_content']."'  tipo='Noticia' seccion='".$art['catName']."' ";

			    					foreach($relationes as $rel) {
						                 if ($rel == $art['pk_content']){	$html_out .= " checked='checked' "; }
						            	}
					 			  $html_out .= " '/>
								      </td>
					    	      <td style=\"text-align:center\">
					    	            <input type='checkbox' onclick=\"javascript:probarArtic(this,'".$div."','thelist2int');\"
					    	              class='interior' value='".$title."' id='".$art['pk_content']."'   tipo='Noticia' seccion='".$art['catName']."' ";
					    	             foreach($intrelationes as $rel) {
						                 if ($rel == $art['pk_content']){	$html_out .= " checked='checked' "; }
						            	}
					 			 		 $html_out .= " '/>
					    	            </td>
									</tr>
							";
					}

				}
				$html_out .= " </tbody></table>";
			}

			return $html_out;

	}

//Print listados contenidos para ajax
	function print_lists_related($id,$contents, $div){

			$relationes=array();
			$intrelationes=array();
			if(($id) && ($id != 0)){
				$rel= new RelatedContent();
	  		 	$relationes = $rel->get_relations( $id );//de portada
		        $intrelationes = $rel->get_relations_int( $_REQUEST['id'] );//de interior
			}

			$html_out = "<table class='adminlist '>";
				$html_out .= '	<thead>
									<th>'._('Title').'</th>
									<th style="text-align:center">'._('Date').'</th>
									<th style="text-align:center">'._('Show in frontpage').'</th>
									<th style="text-align:center">'._('Show in inner article').'</th>
								</thead>';
				$i=0;
				foreach($contents as $art) {
						$title=$art->title;
						$title = preg_replace('/\\\/', '', $title); //Eliminar comillas
						$tipo ="";
						switch ($art->content_type){
							case 1: $tipo=' Noticia ';break;
							case 7: $tipo=' Galeria ';break;
							case 9: $tipo=' Video ';break;
							case 4: $tipo=' Opinion ';break;
							case 3: $tipo=' Fichero ';break;
						}

						if($i%2){	$html_out .= "<tr bgcolor='#F7F8E0' align='left'>";
						}else{ $html_out .= "<tr bgcolor='#FBFBEF' align='left'>"; }
						$i++;
						$html_out .= '<td>'.$title.'</td>	';
						$html_out .= '<td style="text-align:center">'.$art->created.'</td>';
				/*		$html_out .= "<td width='80'>
										<a onclick=\"preview(this, '".$art->category."','','".$art->id."');\" onmouseover=\"return escape('<u>V</u>er Noticia');\" rel='iframe' class='admin_add' href='#'>
				                  		  <img width='32' border='0' alt='Ver Noticia' title='Ver Noticia' src='themes/default/images/preview.png'/><br/>Ver noticia
				                		</a></td>";
				               */
						$html_out .=" <td style='text-align:center'>
						 			   <input type='checkbox' onclick=\"javascript:probarArtic(this,'".$div."','thelist2');\"
						 			     class='portada' value='".$title."' id='".$art->id."' tipo='".$tipo."' seccion='".$art->category_name."' ";
						 			       foreach($relationes as $rel) {
							                	 if ($rel == $art->id){	$html_out .= " checked='checked' "; }
							            	}
						 			     $html_out .= " '/>
									      </td>
						    	      <td style='text-align:center'>
						    	            <input type='checkbox' onclick=\"javascript:probarArtic(this,'".$div."','thelist2int');\"
						    	            class='interior' value='".$title."' id='".$art->id."'  tipo='".$tipo."' seccion='".$art->category_name."' ";
						    	             foreach($intrelationes as $rel) {
								                 if ($rel == $art->id){	$html_out .= " checked='checked' "; }
								            }
						 			 		$html_out .= " '/>
						    	            </td> ";
						$html_out .= '	</tr> ';
				}
				$html_out .= ' </table>';
			return $html_out;
		}

//Print de paginacion de contenidos relacionadas para ajax
	function print_pagination($id,$tipo,$pages,$category){
		$paginacionV= '<br /><p align="center" width="90%" class="pagination">';
			for($i=1;$i<=$pages->_totalPages;$i++){
				 	$paginacionV.=' <a style="cursor:pointer;" onClick="get_div_contents('.$id.',\''.$tipo.'\','.$category.','.$i.');">'.$i.'</a>';
			}
			$paginacionV.='</p>';
		return $paginacionV;
	}

	//Print de paginacion de searchs relacionadas para ajax
	function print_pagination_search($id,$metadata,$pages){
		$paginacionV= '<br /><p align="center" width="90%" class="pagination">';
			for($i=1;$i<=$pages->_totalPages;$i++){
				 	$paginacionV.=' <a style="cursor:pointer;" onClick="search_related('.$id.',\''.$metadata.'\','.$i.');">'.$i.'</a>';
			}
			$paginacionV.='</p>';
		return $paginacionV;
	}

//Print menu categorias contenidos relacionadas para ajax
function print_menu($allcategorys,$subcat,$datos_cat,$tipo){
    if (is_object($datos_cat)) {
        $category = $datos_cat->pk_content_category;
    } else {
        $category = null;
    }

    $html_out ='';
    $html_out .=' <ul class="pills">';
    $i=0;
    foreach($allcategorys as $cat) {
            $html_out .= ' <li> <a href="#"  onClick="get_div_contents(0,\''.$tipo.'\','.$cat->pk_content_category.',1);" ';
             if ($category==$cat->pk_content_category) {
                    $html_out .= ' style="color:#000000; font-weight:bold; background-color:#BFD9BF" ';
             }
             $html_out .= '>'.$cat->title .'</a> ';
             $html_out .='	</li>';
            $i++;
    }
    $html_out .= '</ul> <br />';

    $html_out .='<div style="clear:left;"> ';
    $i=0;
    foreach($allcategorys as $cat) {
        $html_out .= '<div id="'.$cat->name.'" style="display:inline ">
          <ul class="pills"> ';
        foreach($subcat[$i] as $sub){
                  if ($cat->pk_content_category == $category){

                                            $html_out .= '<li> <a href="#" onClick="get_div_contents(0,\''.$tipo.'\','.$sub->pk_content_category.',1);"  >';
                                        $html_out .= '<span style="color:#222 ;margin-left: 12px;margin-right: 12px;">'.$sub->title.'</span></a>
                                        </li>';
                  }else{ //Es una subcategoria
                              $father= $datos_cat->fk_content_category;
                                      if ($sub->fk_content_category==$father){
                                          $html_out .= '<li> <a href="#"  onClick="get_div_contents(0,\''.$tipo.'\','.$sub->pk_content_category.',1);" ';
                                          if ($category==$sub->pk_content_category){
                                            $html_out .= ' style="color:#000000; font-weight:bold; background-color:#BFD9BF" ';
                                          }
                                      $html_out .= ' > ';
                                          $html_out .= '<span style="color:#222 ;margin-left: 12px;margin-right: 12px;">'.$sub->title.'</span></a>
                                             </li>';
                                      }
                    }

            }
            $i++;
            $html_out .='      </ul>
                        </div> ';
    }
    $html_out .= '		</div>
    <br class="clear"/><br class="clear"/>';

    return $html_out;
}
	function print_menu_opinion($type_opinion){

		$html_out ='<ul class="pills">
			<li><a onclick="get_div_contents(0,\'opinions\',0,1);"  ';
			 if ($type_opinion==0){ $html_out .='style="color:#000000; font-weight:bold; cursor:pointer; background-color:#BFD9BF"';}else{$html_out .='style="cursor:pointer;" '; }
			 $html_out .='><b>Opinion Autor</b></a></li>
			<li><a onclick="get_div_contents(0,\'opinions\',1,1);"   ';
			 if ($type_opinion==1){ $html_out .='style="color:#000000; font-weight:bold; cursor:pointer; background-color:#BFD9BF"';}else{$html_out .='style="cursor:pointer;" '; }
			 $html_out .='><b>Editorial</b></a> </li>
			<li><a onclick="get_div_contents(0,\'opinions\',2,1);"  ';
			 if ($type_opinion==2){ $html_out .='style="color:#000000; font-weight:bold; cursor:pointer; background-color:#BFD9BF"';}else{$html_out .='style="cursor:pointer;" '; }
			 $html_out .='><b>Opinion Director</b></a></li>
			</ul><br /><br /><br />';

		return $html_out;
	}

?>
