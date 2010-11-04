<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');
require_once(SITE_CORE_PATH.'string_utils.class.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Import XML');

function createArticle()
{
    $ccm = new ContentCategoryManager();
    $sql = 'SELECT * FROM CXG';
    
    $rs = $GLOBALS['application']->conn->Execute($sql);

    $items=array();
    $data=array();

    while(!$rs->EOF) {
        
        $data['title'] = $rs->fields['title'];
        $data['body'] = $rs->fields['bodytext'];
        $data['summary'] = $rs->fields['introtext'];
        $data['metadata'] = $rs->fields['metakey'];
        $data['fk_publisher']="";
        $data['subtitle']="";
        $data['agency']="";
        $data['pk_author']="";
        $data['category']=$ccm->get_id('cxg');
        $data['available']=1;
        $data['metadata']="";$data['agency_web']="";$data['img1']="";$data['img1_in']="";$data['img1_footer']="";
        $data['img2']="";$data['img2_in']="";$data['img2_footer']="";$data['with_galery']="";$data['with_galery_int']="";$data['with_comment']="";
        $data['columns']="1";$data['description']="";$data['fk_video']="";$data['fk_video2']="";$data['footer_video2']="";

        $article = new Article();
        $article->create( $data );
        
        $rs->MoveNext();
    }

    

}
    



createArticle();

?>
